<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BioVerifyPdfService
{
    private function baseUrl(): string
    {
        return rtrim(
            config('services.bioverify_pdf.url', 'http://127.0.0.1:8002'),
            '/'
        );
    }

    public function indexDocument(
        int $documentId,
        string $title,
        string $originalFilename,
        string $contents
    ): array {
        try {
            // The upload gets a fixed ".pdf" name so valid PDFs with other
            // names pass the RAG API's extension check; the real name is
            // sent separately for source citations.
            $response = Http::connectTimeout(10)
                ->timeout(600)
                ->attach('file', $contents, "document-{$documentId}.pdf")
                ->post($this->baseUrl() . '/documents/index', [
                    'document_id' => (string) $documentId,
                    'title' => $title,
                    'original_filename' => $originalFilename,
                ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    $this->errorDetail($response, 'PDF indexing failed')
                );
            }

            $data = $response->json();

            if (!is_array($data) || empty($data['success'])) {
                throw new RuntimeException(
                    'The PDF API returned an invalid response.'
                );
            }

            return $data;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new RuntimeException(
                'Cannot connect to the RAG API at ' . $this->baseUrl() . '. Ensure it is running.',
                previous: $e
            );
        }
    }

    public function ask(
        string $question,
        ?int $documentId = null
    ): array {
        try {
            $response = Http::connectTimeout(10)
                ->timeout(300)
                ->post($this->baseUrl() . '/documents/ask', [
                    'question' => $question,
                    'document_id' => $documentId,
                ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    $this->errorDetail($response, 'PDF question failed')
                );
            }

            $data = $response->json();

            if (!is_array($data) || empty($data['answer'])) {
                throw new RuntimeException(
                    'The PDF API returned an invalid answer.'
                );
            }

            return [
                'answer' => $data['answer'],
                'sources' => $data['sources'] ?? [],
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new RuntimeException(
                'Cannot connect to the RAG API at ' . $this->baseUrl() . '. Ensure it is running.',
                previous: $e
            );
        }
    }

    /**
     * Remove a document's chunks from the RAG index.
     */
    public function deleteDocument(int $documentId): void
    {
        try {
            $response = Http::connectTimeout(10)
                ->timeout(60)
                ->delete($this->baseUrl() . '/documents/' . $documentId);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new RuntimeException(
                'Cannot connect to the RAG API at ' . $this->baseUrl() . '. Ensure it is running.',
                previous: $e
            );
        }

        if ($response->failed()) {
            throw new RuntimeException(
                $this->errorDetail($response, 'Removing the PDF from the index failed')
            );
        }
    }

    /**
     * FastAPI returns `detail` as a string for HTTPException but as an
     * array for request validation errors.
     */
    private function errorDetail(Response $response, string $fallback): string
    {
        $detail = $response->json('detail');

        if (is_string($detail) && $detail !== '') {
            return $detail;
        }

        if (is_array($detail)) {
            return json_encode($detail);
        }

        return $fallback . ' with HTTP ' . $response->status();
    }
}
