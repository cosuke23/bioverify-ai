"""
Shared ChromaDB client and Ollama helpers for the BioVerify RAG service.

ChromaDB's PersistentClient must be owned by a single process. The chat
API (chat_api.py) is that process: it serves /chat, the PDF routes from
pdf_api.py and the notes re-index route. Do not open another
PersistentClient on CHROMA_DIR while the service is running.
"""

import json
import urllib.error
import urllib.request
from pathlib import Path

import chromadb

PROJECT_ROOT = Path(__file__).resolve().parents[2]
CHROMA_DIR = PROJECT_ROOT / "rag-ai" / "chroma_db"
KNOWLEDGE_DIR = PROJECT_ROOT / "knowledge-base"

OLLAMA_BASE_URL = "http://127.0.0.1:11434"
CHAT_MODEL = "llama3.2:1b"
EMBED_MODEL = "nomic-embed-text:latest"

NOTES_COLLECTION = "bioverify_notes"
PDF_COLLECTION = "bioverify_pdfs"

# nomic-embed-text is trained with task prefixes; questions and stored
# chunks must use the matching prefix or retrieval quality drops.
QUERY_PREFIX = "search_query: "
DOCUMENT_PREFIX = "search_document: "

# Laravel waits 190s for /chat. Keep embed + generation below that.
EMBED_TIMEOUT = 30
CHAT_TIMEOUT = 150

EMBED_BATCH_SIZE = 32

_client = None


def get_client():
    """Return the single PersistentClient for this process."""
    global _client

    if _client is None:
        _client = chromadb.PersistentClient(path=str(CHROMA_DIR))

    return _client


def ollama_request(endpoint: str, payload: dict, timeout: int = CHAT_TIMEOUT) -> dict:
    request = urllib.request.Request(
        f"{OLLAMA_BASE_URL}{endpoint}",
        data=json.dumps(payload).encode("utf-8"),
        headers={"Content-Type": "application/json"},
        method="POST",
    )

    try:
        with urllib.request.urlopen(request, timeout=timeout) as response:
            return json.loads(response.read().decode("utf-8"))

    except urllib.error.HTTPError as exc:
        details = exc.read().decode("utf-8", errors="replace")
        raise RuntimeError(
            f"Ollama HTTP {exc.code}: {details}"
        ) from exc

    except urllib.error.URLError as exc:
        raise RuntimeError(
            "Cannot connect to Ollama at 127.0.0.1:11434."
        ) from exc


def _embed_batch(texts: list[str]) -> list[list[float]]:
    result = ollama_request(
        "/api/embed",
        {
            "model": EMBED_MODEL,
            "input": texts,
        },
        timeout=EMBED_TIMEOUT + 5 * len(texts),
    )

    embeddings = result.get("embeddings") or []

    if len(embeddings) != len(texts) or not all(embeddings):
        raise RuntimeError("Ollama returned an incomplete set of embeddings.")

    return embeddings


def create_embedding(question: str) -> list[float]:
    """Embed a search question."""
    return _embed_batch([QUERY_PREFIX + question])[0]


def create_document_embeddings(chunks: list[str]) -> list[list[float]]:
    """Embed stored chunks (notes or PDF text) in batches."""
    embeddings = []

    for start in range(0, len(chunks), EMBED_BATCH_SIZE):
        batch = chunks[start:start + EMBED_BATCH_SIZE]
        embeddings.extend(
            _embed_batch([DOCUMENT_PREFIX + chunk for chunk in batch])
        )

    return embeddings
