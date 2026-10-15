<?php

namespace Tests\Feature;

use App\Models\PdfDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsBiometricFixtures;
use Tests\TestCase;

class PdfDocumentTest extends TestCase
{
    use BuildsBiometricFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->signInAsAdmin();
    }

    private function pdfUrl(string $path): string
    {
        return rtrim(config('services.bioverify_pdf.url'), '/') . $path;
    }

    private function fakePdf(int $kilobytes = 50): UploadedFile
    {
        return UploadedFile::fake()->create('manual.pdf', $kilobytes, 'application/pdf');
    }

    private function completedDocument(array $attributes = []): PdfDocument
    {
        return PdfDocument::create(array_merge([
            'title' => 'Manual',
            'original_filename' => 'manual.pdf',
            'file_path' => 'pdf-documents/manual.pdf',
            'file_hash' => str_repeat('a', 64),
            'status' => 'completed',
        ], $attributes));
    }

    public function test_the_index_page_renders(): void
    {
        $this->get(route('pdf-documents.index'))->assertOk();
    }

    public function test_guests_cannot_reach_pdf_documents(): void
    {
        auth()->logout();

        $this->get(route('pdf-documents.index'))->assertRedirect(route('login'));
        $this->postJson(route('pdf-documents.ask', $this->completedDocument()), ['question' => 'Hi'])
            ->assertUnauthorized();
    }

    public function test_upload_requires_a_pdf_within_the_size_limit(): void
    {
        Http::fake();

        $this->post(route('pdf-documents.store'), [
            'title' => 'Too big',
            'pdf_file' => $this->fakePdf(20481),
        ])->assertSessionHasErrors('pdf_file');

        $this->post(route('pdf-documents.store'), [
            'title' => 'Not a pdf',
            'pdf_file' => UploadedFile::fake()->create('notes.txt', 5, 'text/plain'),
        ])->assertSessionHasErrors('pdf_file');

        $this->assertSame(0, PdfDocument::count());
        Http::assertNothingSent();
    }

    public function test_upload_stores_privately_and_indexes_the_document(): void
    {
        Http::fake([
            $this->pdfUrl('/documents/index') => Http::response([
                'success' => true,
                'page_count' => 3,
                'chunk_count' => 7,
                'summary' => 'A short summary.',
            ]),
        ]);

        $response = $this->post(route('pdf-documents.store'), [
            'title' => 'Manual',
            'pdf_file' => $this->fakePdf(),
        ]);

        $document = PdfDocument::sole();
        $response->assertRedirect(route('pdf-documents.show', $document));

        $this->assertSame('completed', $document->status);
        $this->assertSame(3, $document->page_count);
        $this->assertSame('A short summary.', $document->summary);
        Storage::disk('local')->assertExists($document->file_path);
        $this->assertStringStartsWith('pdf-documents/', $document->file_path);

        Http::assertSent(function (Request $request) use ($document) {
            $parts = collect($request->data())->keyBy('name');

            // The RAG API gets a fixed .pdf name; the real name is sent
            // separately for source citations.
            return $request->url() === $this->pdfUrl('/documents/index')
                && $parts['file']['filename'] === "document-{$document->id}.pdf"
                && $parts['original_filename']['contents'] === 'manual.pdf';
        });
    }

    public function test_an_already_indexed_pdf_is_not_uploaded_twice(): void
    {
        Http::fake();
        $file = $this->fakePdf();
        $existing = $this->completedDocument([
            'file_hash' => hash_file('sha256', $file->getRealPath()),
        ]);

        $this->post(route('pdf-documents.store'), [
            'title' => 'Manual again',
            'pdf_file' => $file,
        ])->assertRedirect(route('pdf-documents.show', $existing));

        $this->assertSame(1, PdfDocument::count());
        Http::assertNothingSent();
    }

    public function test_indexing_failure_marks_the_document_failed(): void
    {
        Http::fake([
            $this->pdfUrl('/documents/index') => Http::response(['detail' => 'boom'], 503),
        ]);

        $this->post(route('pdf-documents.store'), [
            'title' => 'Manual',
            'pdf_file' => $this->fakePdf(),
        ]);

        $this->assertSame('failed', PdfDocument::sole()->status);
    }

    public function test_questions_require_a_completed_document(): void
    {
        Http::fake();
        $document = $this->completedDocument(['status' => 'processing']);

        $this->postJson(route('pdf-documents.ask', $document), ['question' => 'What is this?'])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        Http::assertNothingSent();
    }

    public function test_questions_are_scoped_to_the_document(): void
    {
        $document = $this->completedDocument();

        Http::fake([
            $this->pdfUrl('/documents/ask') => Http::response([
                'answer' => 'It is a manual.',
                'sources' => [['page' => 1]],
            ]),
        ]);

        $this->postJson(route('pdf-documents.ask', $document), ['question' => 'What is this?'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'answer' => 'It is a manual.',
                'sources' => [['page' => 1]],
            ]);

        Http::assertSent(fn (Request $request) =>
            $request->url() === $this->pdfUrl('/documents/ask')
            && $request['document_id'] === $document->id);
    }

    public function test_deleting_removes_the_file_the_index_and_the_row(): void
    {
        Storage::disk('local')->put('pdf-documents/manual.pdf', '%PDF-1.4 test');
        $document = $this->completedDocument();

        Http::fake([
            $this->pdfUrl('/documents/' . $document->id) => Http::response([
                'success' => true,
                'deleted_chunks' => 4,
            ]),
        ]);

        $this->delete(route('pdf-documents.destroy', $document))
            ->assertRedirect(route('pdf-documents.index'));

        $this->assertSame(0, PdfDocument::count());
        Storage::disk('local')->assertMissing('pdf-documents/manual.pdf');
        Http::assertSent(fn (Request $request) =>
            $request->method() === 'DELETE'
            && $request->url() === $this->pdfUrl('/documents/' . $document->id));
    }

    public function test_nothing_is_deleted_when_the_index_cannot_be_updated(): void
    {
        Storage::disk('local')->put('pdf-documents/manual.pdf', '%PDF-1.4 test');
        $document = $this->completedDocument();

        Http::fake([
            $this->pdfUrl('/documents/' . $document->id) => Http::response(['detail' => 'down'], 503),
        ]);

        $this->delete(route('pdf-documents.destroy', $document))
            ->assertRedirect(route('pdf-documents.show', $document))
            ->assertSessionHas('error');

        $this->assertSame(1, PdfDocument::count());
        Storage::disk('local')->assertExists('pdf-documents/manual.pdf');
    }

    public function test_a_failed_document_can_be_retried(): void
    {
        Storage::disk('local')->put('pdf-documents/manual.pdf', '%PDF-1.4 test');
        $document = $this->completedDocument(['status' => 'failed']);

        Http::fake([
            $this->pdfUrl('/documents/index') => Http::response([
                'success' => true,
                'page_count' => 2,
                'summary' => 'Retried.',
            ]),
        ]);

        $this->post(route('pdf-documents.retry', $document))
            ->assertRedirect(route('pdf-documents.show', $document));

        $this->assertSame('completed', $document->fresh()->status);
    }
}
