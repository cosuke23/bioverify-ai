@extends('layouts.pcg')

@section('title', 'Documents')

@section('crumbs')
    <span>Knowledge</span>
    <span aria-hidden="true">/</span>
    <strong>Documents</strong>
@endsection

@push('head')
    <style>
        .docs [hidden] {
            display: none !important;
        }

        .docs-drop {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 92px;
            padding: 16px 18px;
            border: 1.5px dashed var(--line);
            border-radius: var(--radius);
            background: var(--canvas);
            transition: border-color .15s, background .15s;
        }

        .docs-drop:hover,
        .docs-drop:focus-within,
        .docs-drop.is-over {
            border-color: var(--pcg-blue);
            background: var(--info-soft);
        }

        .docs-drop.is-invalid {
            border-color: var(--danger);
        }

        .docs-drop input[type='file'] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .docs-drop-icon,
        .docs-file-icon {
            display: grid;
            place-items: center;
            flex: 0 0 44px;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--surface);
            border: 1px solid var(--line);
            color: var(--pcg-blue);
        }

        .docs-file-icon {
            flex-basis: 38px;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--info-soft);
            border-color: transparent;
        }

        .docs-title-link {
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
        }

        .docs-title-link:hover {
            color: var(--pcg-blue);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .docs-row-link::after {
            /* Whole row is clickable via the title link. */
            content: '';
            position: absolute;
            inset: 0;
        }

        .docs-table tbody tr {
            position: relative;
        }

        .docs-empty {
            padding: 48px 22px;
            text-align: center;
        }

        .docs-empty-icon {
            display: grid;
            place-items: center;
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 16px;
            background: var(--line-soft);
            color: var(--muted);
        }
    </style>
@endpush

@section('content')
    <div class="docs">
        <header class="page-head rise">
            <div>
                <div class="eyebrow">Knowledge</div>
                <h1 class="page-title">Documents</h1>
                <p class="page-lede">
                    Upload circulars, manuals and SOPs as PDF. Each file is indexed, summarised and made
                    available to Ask Pong and to per-document questions.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="tag">{{ number_format($documents->total()) }} {{ \Illuminate\Support\Str::plural('document', $documents->total()) }}</span>
            </div>
        </header>

        {{-- Upload --}}
        <section class="card mb-6 rise rise-2" aria-labelledby="uploadTitle">
            <div class="card-head">
                <div>
                    <h2 class="card-title" id="uploadTitle">Upload a document</h2>
                    <p class="muted text-sm">PDF only, up to 20 MB. Processing runs in the background.</p>
                </div>
            </div>

            <form action="{{ route('pdf-documents.store') }}" method="POST" enctype="multipart/form-data"
                class="card-pad grid gap-5" id="uploadForm">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div>
                            <strong class="block mb-1">Please correct the following:</strong>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="field">
                        <label for="title" class="label">Document title</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" required
                            maxlength="255" placeholder="e.g. Maritime Safety Manual 2026"
                            class="input @error('title') is-invalid @enderror"
                            @error('title') aria-invalid="true" aria-describedby="titleError" @enderror>

                        @error('title')
                            <p class="field-error" id="titleError">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <span class="label" id="pdfFileLabel">PDF file</span>
                        <div class="docs-drop @error('pdf_file') is-invalid @enderror" id="pdfDrop">
                            <span class="docs-drop-icon" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <path d="M14 2v6h6" />
                                    <path d="M12 18v-6m-3 3 3-3 3 3" />
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <strong class="block text-sm" id="pdfFileName">Choose a PDF or drop it here</strong>
                                <span class="block muted text-xs mono" id="pdfFileMeta">.pdf · max 20 MB</span>
                            </span>
                            <input id="pdf_file" name="pdf_file" type="file" accept=".pdf,application/pdf" required
                                aria-labelledby="pdfFileLabel"
                                @error('pdf_file') aria-invalid="true" aria-describedby="pdfFileError" @enderror>
                        </div>

                        @error('pdf_file')
                            <p class="field-error" id="pdfFileError">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="muted text-xs m-0">
                        Requires the PDF service (port 8003), Ollama and the queue worker to be running.
                    </p>
                    <button type="submit" class="btn btn-primary" id="uploadButton">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 16V4m-5 5 5-5 5 5" />
                            <path d="M4 16v4h16v-4" />
                        </svg>
                        <span>Upload and analyse</span>
                    </button>
                </div>
            </form>
        </section>

        {{-- Library --}}
        <section class="card overflow-hidden rise rise-3" aria-labelledby="libraryTitle">
            <div class="card-head">
                <div>
                    <h2 class="card-title" id="libraryTitle">Library</h2>
                    <p class="muted text-sm">Newest first. Open a document for its summary and Q&amp;A.</p>
                </div>
            </div>

            @if ($documents->count())
                <div class="table-wrap">
                    <table class="table docs-table">
                        <thead>
                            <tr>
                                <th scope="col">Document</th>
                                <th scope="col">Status</th>
                                <th scope="col">Pages</th>
                                <th scope="col">Uploaded</th>
                                <th scope="col"><span class="sr-only">Open</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $document)
                                @php
                                    $status = $document->status ?? 'pending';
                                    $badge = match ($status) {
                                        'completed' => 'badge-ok',
                                        'processing', 'pending' => 'badge-info',
                                        'failed' => 'badge-danger',
                                        default => 'badge-muted',
                                    };
                                @endphp
                                <tr>
                                    <td style="min-width:260px">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="docs-file-icon" aria-hidden="true">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                    <path d="M14 2v6h6M8 13h8M8 17h5" />
                                                </svg>
                                            </span>
                                            <div class="min-w-0">
                                                <a href="{{ route('pdf-documents.show', $document) }}"
                                                    class="docs-title-link docs-row-link block truncate">{{ $document->title }}</a>
                                                <span class="mono muted text-xs block truncate" style="max-width:420px">{{ $document->original_filename }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $badge }}">{{ ucfirst($status) }}</span>
                                        @if ($document->isStale())
                                            <span class="badge badge-warn ml-1" title="No progress for over {{ \App\Models\PdfDocument::STALE_AFTER_MINUTES }} minutes">Stalled</span>
                                        @endif
                                    </td>
                                    <td class="mono">{{ $document->page_count ?? '—' }}</td>
                                    <td class="whitespace-nowrap">
                                        @if ($document->created_at)
                                            <time datetime="{{ $document->created_at->toIso8601String() }}">
                                                {{ $document->created_at->format('M d, Y') }}
                                            </time>
                                            <span class="block muted text-xs mono">{{ $document->created_at->format('h:i A') }}</span>
                                        @else
                                            <span class="muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="muted inline-block" aria-hidden="true">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($documents->hasPages())
                    <div class="px-5 py-4" style="border-top:1px solid var(--line-soft)">
                        {{ $documents->links() }}
                    </div>
                @endif
            @else
                <div class="docs-empty">
                    <div class="docs-empty-icon" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                            <path d="M8 13h8M8 17h5" />
                        </svg>
                    </div>
                    <h3 class="card-title">No documents yet</h3>
                    <p class="muted text-sm mt-2">Upload the first PDF to start the knowledge library.</p>
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';

            const form = document.getElementById('uploadForm');
            const input = document.getElementById('pdf_file');
            const drop = document.getElementById('pdfDrop');
            const nameEl = document.getElementById('pdfFileName');
            const metaEl = document.getElementById('pdfFileMeta');
            const button = document.getElementById('uploadButton');

            if (!form || !input) return;

            function showFile() {
                const file = input.files && input.files[0];

                if (!file) {
                    nameEl.textContent = 'Choose a PDF or drop it here';
                    metaEl.textContent = '.pdf · max 20 MB';
                    return;
                }

                nameEl.textContent = file.name;
                metaEl.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB' +
                    (file.size > 20 * 1024 * 1024 ? ' · exceeds 20 MB limit' : '');
            }

            input.addEventListener('change', showFile);

            ['dragenter', 'dragover'].forEach((type) => drop.addEventListener(type, () => drop.classList.add('is-over')));
            ['dragleave', 'drop'].forEach((type) => drop.addEventListener(type, () => drop.classList.remove('is-over')));

            form.addEventListener('submit', () => {
                // Prevent a double upload while the file transfers.
                button.disabled = true;
                button.querySelector('span').textContent = 'Uploading…';
            });
        })();
    </script>
@endpush
