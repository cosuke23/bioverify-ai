<?php

namespace App\Http\Controllers;

use App\Jobs\IndexPdfDocument;
use App\Models\PdfDocument;
use App\Services\BioVerifyPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PdfDocumentController extends Controller
{
    public function index(): View
    {
        $documents = PdfDocument::latest()->paginate(10);

        return view('pdf-documents.index', compact('documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'pdf_file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:20480',
            ],
        ]);

        $file = $request->file('pdf_file');

        $hash = hash_file('sha256', $file->getRealPath());

        // Avoid indexing the same PDF again unnecessarily.
        $existing = PdfDocument::where('file_hash', $hash)
            ->orderByRaw("status = 'completed' desc")
            ->latest()
            ->first();

        if ($existing && $existing->status === 'completed') {
            return redirect()
                ->route('pdf-documents.show', $existing)
                ->with('info', 'This PDF has already been uploaded and indexed.');
        }

        if ($existing && !$existing->canRetry()) {
            return redirect()
                ->route('pdf-documents.show', $existing)
                ->with('info', 'This PDF is already being processed.');
        }

        // A failed or stalled upload of the same file is re-queued
        // instead of creating a duplicate document.
        if ($existing) {
            return $this->queueIndexing($existing);
        }

        $path = $file->store('pdf-documents', 'local');

        if (!$path) {
            return back()
                ->withInput()
                ->withErrors([
                    'pdf_file' => 'The PDF could not be saved.',
                ]);
        }

        try {
            $document = PdfDocument::create([
                'title' => $validated['title'],
                'original_filename' => Str::limit(
                    basename($file->getClientOriginalName()),
                    250,
                    ''
                ),
                'file_path' => $path,
                'file_hash' => $hash,
                'status' => 'pending',
            ]);
        } catch (Throwable $e) {
            Storage::disk('local')->delete($path);

            throw $e;
        }

        return $this->queueIndexing($document);
    }

    public function destroy(
        PdfDocument $pdfDocument,
        BioVerifyPdfService $pdfService
    ): RedirectResponse {
        if (in_array($pdfDocument->status, ['pending', 'processing'], true) && !$pdfDocument->isStale()) {
            return redirect()
                ->route('pdf-documents.show', $pdfDocument)
                ->with('error', 'Wait for processing to finish before deleting this PDF.');
        }

        try {
            // Remove the chunks first so Pong never cites a document
            // that no longer exists.
            $pdfService->deleteDocument($pdfDocument->id);
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('pdf-documents.show', $pdfDocument)
                ->with('error', 'The PDF could not be removed from the AI index. Make sure the RAG service is running, then try again.');
        }

        Storage::disk('local')->delete($pdfDocument->file_path);

        $title = $pdfDocument->title;
        $pdfDocument->delete();

        return redirect()
            ->route('pdf-documents.index')
            ->with('success', "\"{$title}\" was deleted.");
    }

    public function retry(PdfDocument $pdfDocument): RedirectResponse
    {
        if (!$pdfDocument->canRetry()) {
            return redirect()
                ->route('pdf-documents.show', $pdfDocument)
                ->with('info', 'This PDF does not need to be processed again.');
        }

        return $this->queueIndexing($pdfDocument);
    }

    private function queueIndexing(PdfDocument $document): RedirectResponse
    {
        $document->update([
            'status' => 'pending',
            'processing_error' => null,
        ]);

        IndexPdfDocument::dispatch($document);

        return redirect()
            ->route('pdf-documents.show', $document)
            ->with('success', 'PDF uploaded. Indexing and summarizing has started; this page updates when it finishes.');
    }

    public function show(PdfDocument $pdfDocument): View
    {
        return view('pdf-documents.show', [
            'document' => $pdfDocument,
        ]);
    }

    public function ask(
        Request $request,
        PdfDocument $pdfDocument,
        BioVerifyPdfService $pdfService
    ): JsonResponse {
        $validated = $request->validate([
            'question' => ['required', 'string', 'min:1', 'max:4000'],
        ]);

        if ($pdfDocument->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'This document is not ready for questions yet.',
            ], 422);
        }

        // The RAG API may take up to 300s; keep PHP's limit above that.
        set_time_limit(330);

        try {
            $result = $pdfService->ask(
                trim($validated['question']),
                $pdfDocument->id
            );

            return response()->json([
                'success' => true,
                'answer' => $result['answer'],
                'sources' => $result['sources'],
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'The AI could not answer this question right now. Please try again in a moment.',
            ], 503);
        }
    }
}
