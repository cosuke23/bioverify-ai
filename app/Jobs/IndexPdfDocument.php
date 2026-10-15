<?php

namespace App\Jobs;

use App\Models\PdfDocument;
use App\Services\BioVerifyPdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Index and summarize an uploaded PDF outside the web request.
 *
 * Indexing makes one Ollama embedding call per chunk plus a summary
 * call, which can take several minutes. Running it in the queue keeps
 * it clear of PHP's max_execution_time in the web server.
 */
class IndexPdfDocument implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 900;

    public function __construct(
        public PdfDocument $document
    ) {
    }

    public function handle(BioVerifyPdfService $pdfService): void
    {
        $this->document->update([
            'status' => 'processing',
            'processing_error' => null,
        ]);

        $contents = Storage::disk('local')->get($this->document->file_path);

        if ($contents === null) {
            throw new \RuntimeException('The stored PDF file could not be read.');
        }

        $result = $pdfService->indexDocument(
            $this->document->id,
            $this->document->title,
            $this->document->original_filename,
            $contents
        );

        $this->document->update([
            'status' => 'completed',
            'page_count' => $result['page_count'] ?? null,
            'summary' => $result['summary'] ?? null,
            'processing_error' => null,
            'processed_at' => now(),
        ]);
    }

    public function failed(?Throwable $e): void
    {
        $this->document->update([
            'status' => 'failed',
            'processing_error' => mb_strcut(
                $e?->getMessage() ?? 'PDF processing failed.',
                0,
                60000
            ),
        ]);
    }
}
