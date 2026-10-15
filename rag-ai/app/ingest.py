import hashlib
import sys
import urllib.error
import urllib.request

try:
    from .rag_store import (
        CHROMA_DIR,
        KNOWLEDGE_DIR,
        NOTES_COLLECTION as COLLECTION_NAME,
        create_document_embeddings,
        get_client,
    )
except ImportError:
    from rag_store import (
        CHROMA_DIR,
        KNOWLEDGE_DIR,
        NOTES_COLLECTION as COLLECTION_NAME,
        create_document_embeddings,
        get_client,
    )

CHUNK_SIZE = 1200
CHUNK_OVERLAP = 200

RAG_SERVICE_HEALTH_URL = "http://127.0.0.1:8002/health"


def split_text(text: str) -> list[str]:
    text = text.strip()
    if not text:
        return []

    chunks = []
    start = 0

    while start < len(text):
        end = min(start + CHUNK_SIZE, len(text))
        chunk = text[start:end].strip()

        if chunk:
            chunks.append(chunk)

        if end >= len(text):
            break

        start = end - CHUNK_OVERLAP

    return chunks


def build_notes_index(client) -> dict:
    """
    Rebuild the notes collection from knowledge-base/*.md.

    All embeddings are generated before the old collection is touched,
    so an Ollama failure leaves the existing index in place.
    """
    if not KNOWLEDGE_DIR.exists():
        raise RuntimeError(f"Knowledge base not found: {KNOWLEDGE_DIR}")

    markdown_files = sorted(KNOWLEDGE_DIR.rglob("*.md"))

    if not markdown_files:
        raise RuntimeError(
            f"No Markdown files found in {KNOWLEDGE_DIR}. "
            "Create at least one .md note first."
        )

    ids = []
    documents = []
    metadatas = []
    files = {}

    for file_path in markdown_files:
        relative_path = file_path.relative_to(KNOWLEDGE_DIR).as_posix()
        content = file_path.read_text(encoding="utf-8-sig")
        chunks = split_text(content)

        for index, chunk in enumerate(chunks):
            digest = hashlib.sha256(
                f"{relative_path}:{index}:{chunk}".encode("utf-8")
            ).hexdigest()[:24]

            ids.append(digest)
            documents.append(chunk)
            metadatas.append({
                "source": relative_path,
                "chunk": index,
            })

        files[relative_path] = len(chunks)

    embeddings = create_document_embeddings(documents)

    # This collection belongs exclusively to this ingestion step.
    # Rebuild it so removed or edited chunks do not remain stale.
    try:
        client.delete_collection(COLLECTION_NAME)
    except Exception:
        pass

    collection = client.create_collection(
        name=COLLECTION_NAME,
        metadata={"hnsw:space": "cosine"},
    )

    if ids:
        collection.add(
            ids=ids,
            documents=documents,
            embeddings=embeddings,
            metadatas=metadatas,
        )

    return {
        "files": files,
        "chunks_indexed": len(ids),
        "collection": COLLECTION_NAME,
    }


def rag_service_is_running() -> bool:
    try:
        with urllib.request.urlopen(RAG_SERVICE_HEALTH_URL, timeout=3):
            return True
    except (urllib.error.URLError, OSError):
        return False


def main() -> None:
    # Only one process may own ChromaDB. When the RAG service is up,
    # ask it to re-index instead of opening a second client here.
    if rag_service_is_running():
        print(
            "The RAG service is running on port 8002, so it must do the "
            "re-index itself. Run:\n\n"
            "  Invoke-RestMethod -Method Post "
            "http://127.0.0.1:8002/notes/reindex\n"
        )
        sys.exit(1)

    result = build_notes_index(get_client())

    for relative_path, count in result["files"].items():
        print(f"Indexed {relative_path}: {count} chunk(s)")

    print("\nIngestion complete.")
    print(f"Markdown files: {len(result['files'])}")
    print(f"Chunks indexed: {result['chunks_indexed']}")
    print(f"ChromaDB: {CHROMA_DIR}")
    print(f"Collection: {COLLECTION_NAME}")


if __name__ == "__main__":
    main()
