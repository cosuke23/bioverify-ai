import argparse
import json
import urllib.request
import urllib.error
from pathlib import Path

import chromadb

PROJECT_ROOT = Path(__file__).resolve().parents[2]
CHROMA_DIR = PROJECT_ROOT / "rag-ai" / "chroma_db"

OLLAMA_URL = "http://127.0.0.1:11434/api/embeddings"
EMBED_MODEL = "nomic-embed-text:latest"
COLLECTION_NAME = "bioverify_notes"


def embed_text(text: str) -> list[float]:
    payload = json.dumps({
        "model": EMBED_MODEL,
        "prompt": text
    }).encode("utf-8")

    request = urllib.request.Request(
        OLLAMA_URL,
        data=payload,
        headers={"Content-Type": "application/json"},
        method="POST",
    )

    try:
        with urllib.request.urlopen(request, timeout=120) as response:
            result = json.loads(response.read().decode("utf-8"))
    except urllib.error.URLError as exc:
        raise RuntimeError(
            "Cannot reach Ollama. Make sure Ollama is running."
        ) from exc

    embedding = result.get("embedding")
    if not embedding:
        raise RuntimeError(f"Ollama returned no embedding: {result}")

    return embedding


def main():
    parser = argparse.ArgumentParser(
        description="Search the BioVerify AI Obsidian knowledge base."
    )
    parser.add_argument(
        "question",
        nargs="*",
        help="Question or keywords to search for."
    )
    parser.add_argument(
        "--top-k",
        type=int,
        default=3,
        help="Number of matching note chunks to return (default: 3)."
    )
    args = parser.parse_args()

    question = " ".join(args.question).strip()
    if not question:
        question = input("Enter your search question: ").strip()

    if not question:
        raise SystemExit("Please enter a search question.")

    client = chromadb.PersistentClient(path=str(CHROMA_DIR))

    try:
        collection = client.get_collection(COLLECTION_NAME)
    except Exception as exc:
        raise SystemExit(
            "Knowledge collection not found. Run ingest.py first."
        ) from exc

    if collection.count() == 0:
        raise SystemExit(
            "The knowledge collection is empty. Run ingest.py first."
        )

    query_embedding = embed_text(question)

    results = collection.query(
        query_embeddings=[query_embedding],
        n_results=min(max(args.top_k, 1), collection.count()),
        include=["documents", "metadatas", "distances"],
    )

    print("\n=== BioVerify AI Knowledge Search ===")
    print(f"Question: {question}")
    print(f"Matching notes: {len(results['documents'][0])}")

    for index, document in enumerate(results["documents"][0], start=1):
        metadata = results["metadatas"][0][index - 1] or {}
        distance = results["distances"][0][index - 1]

        print(f"\n--- Result {index} ---")
        print(f"Source: {metadata.get('source', 'Unknown')}")
        print(f"Chunk: {metadata.get('chunk', 'Unknown')}")
        print(f"Cosine distance: {distance:.4f}")
        print(document)

    print("\n=== Search complete ===")


if __name__ == "__main__":
    main()
