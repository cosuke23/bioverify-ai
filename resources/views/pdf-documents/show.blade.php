@extends('layouts.pcg')

@section('title', $document->title)

@php
    $status = $document->status ?? 'pending';
    $inFlight = in_array($status, ['pending', 'processing'], true) && !$document->isStale();
    $statusBadge = match (true) {
        $document->isStale() => 'badge-warn',
        $status === 'completed' => 'badge-ok',
        in_array($status, ['pending', 'processing'], true) => 'badge-info',
        $status === 'failed' => 'badge-danger',
        default => 'badge-muted',
    };
@endphp

@section('crumbs')
    <a href="{{ route('pdf-documents.index') }}">Documents</a>
    <span aria-hidden="true">/</span>
    <strong>{{ $document->title }}</strong>
@endsection

@push('head')
    <style>
        .doc [hidden] {
            display: none !important;
        }

        .doc-grid {
            display: grid;
            gap: 20px;
            align-items: start;
        }

        @media (min-width: 1100px) {
            .doc-grid {
                grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
            }

            .doc-aside {
                position: sticky;
                top: 84px;
            }
        }

        .doc-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .doc-stat-text {
            margin-top: 8px;
            font-family: var(--font-mono);
            font-size: 15px;
            color: var(--ink);
        }

        .doc-summary {
            color: var(--ink-2);
            line-height: 1.75;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .doc-progress {
            position: relative;
            height: 4px;
            margin-top: 10px;
            overflow: hidden;
            border-radius: 999px;
            background: var(--info-soft);
        }

        .doc-progress::after {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 35%;
            border-radius: 999px;
            background: var(--info);
            animation: doc-indeterminate 1.4s ease-in-out infinite;
        }

        @keyframes doc-indeterminate {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(300%);
            }
        }

        .doc-answer {
            padding: 18px;
            border: 1px solid var(--line);
            border-left: 4px solid var(--pcg-gold);
            border-radius: var(--radius);
            background: var(--canvas);
        }

        .doc-answer-text {
            color: var(--ink-2);
            line-height: 1.75;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .doc-sources {
            display: grid;
            gap: 6px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .doc-sources li {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--ink-2);
        }

        @media (prefers-reduced-motion: reduce) {
            .doc-progress::after {
                animation: none;
                width: 100%;
                opacity: .5;
            }
        }
    </style>
@endpush

@section('content')
    <div class="doc">
        <header class="page-head rise">
            <div class="min-w-0">
                <div class="eyebrow">Knowledge · Document</div>
                <h1 class="page-title" style="overflow-wrap:anywhere">{{ $document->title }}</h1>
                <p class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="tag" style="max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                        title="{{ $document->original_filename }}">{{ $document->original_filename }}</span>
                    <span class="badge {{ $statusBadge }}">{{ $document->isStale() ? 'Stalled' : ucfirst($status) }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pdf-documents.index') }}" class="btn btn-ghost btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 12H5M12 19l-7-7 7-7" />
                    </svg>
                    All documents
                </a>

                <form method="POST" action="{{ route('pdf-documents.destroy', $document) }}"
                    onsubmit="return confirm('Delete this PDF and remove it from Pong AI\'s knowledge?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </header>

        <div class="doc-stats rise rise-2">
            <div class="stat">
                <div class="stat-label">Pages</div>
                <div class="stat-value">{{ $document->page_count ?? '—' }}</div>
            </div>
            <div class="stat {{ $document->processed_at ? 'is-ok' : 'is-gold' }}">
                <div class="stat-label">Processed</div>
                <div class="doc-stat-text">{{ $document->processed_at?->format('M d, Y h:i A') ?? 'Not completed' }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Uploaded</div>
                <div class="doc-stat-text">{{ $document->created_at?->format('M d, Y h:i A') ?? 'Unknown' }}</div>
            </div>
        </div>

        @if ($inFlight)
            <div class="alert alert-info mb-5 rise" role="status">
                <div class="w-full">
                    <strong class="block">Indexing and summarising in the background</strong>
                    This can take a few minutes for large PDFs. The page refreshes automatically.
                    <div class="doc-progress" aria-hidden="true"></div>
                </div>
            </div>
        @endif

        @if ($status === 'failed' && $document->processing_error)
            <div class="alert alert-danger mb-5 rise" role="alert">
                <div class="min-w-0">
                    <strong class="block">Processing error</strong>
                    <span style="overflow-wrap:anywhere">{{ $document->processing_error }}</span>
                </div>
            </div>
        @endif

        @if ($document->canRetry())
            <form method="POST" action="{{ route('pdf-documents.retry', $document) }}"
                class="alert alert-warn mb-5 rise items-center justify-between flex-wrap">
                @csrf
                <span>
                    @if ($document->isStale())
                        Processing has not finished after
                        {{ \App\Models\PdfDocument::STALE_AFTER_MINUTES }} minutes. Make sure the
                        queue worker is running, then try again.
                    @else
                        Processing did not complete. Check the AI services, then retry.
                    @endif
                </span>
                <button type="submit" class="btn btn-primary btn-sm">Retry processing</button>
            </form>
        @endif

        <div class="doc-grid">
            <div class="min-w-0 grid gap-5">
                @if ($document->summary)
                    <section class="card rise rise-3" aria-labelledby="summaryTitle">
                        <div class="card-head">
                            <h2 class="card-title" id="summaryTitle">AI summary</h2>
                            <span class="tag">Generated</span>
                        </div>
                        <div class="card-pad doc-summary">{{ $document->summary }}</div>
                    </section>
                @else
                    <section class="card rise rise-3">
                        <div class="card-pad muted text-sm">
                            {{ $inFlight ? 'The summary will appear here once processing finishes.' : 'No summary is available for this document.' }}
                        </div>
                    </section>
                @endif
            </div>

            <aside class="doc-aside min-w-0">
                @if ($status === 'completed')
                    <section class="card rise rise-3" aria-labelledby="askTitle">
                        <div class="card-head">
                            <h2 class="card-title" id="askTitle">Ask this document</h2>
                        </div>

                        <div class="card-pad">
                            <p class="muted text-sm mb-4">
                                Answers are drawn from retrieved excerpts of this PDF and cite their source pages.
                            </p>

                            <form id="questionForm" class="grid gap-3">
                                <div class="field">
                                    <label for="question" class="label">Your question</label>
                                    <textarea id="question" name="question" rows="3" maxlength="4000" required
                                        placeholder="What are the main requirements in this document?" class="textarea"></textarea>
                                </div>

                                <button id="askButton" type="submit" class="btn btn-primary">Ask AI</button>
                            </form>

                            <p id="questionError" role="alert" class="field-error mt-3" hidden></p>

                            <div id="answerPanel" class="doc-answer mt-5" hidden aria-live="polite">
                                <div class="stat-label mb-2">AI answer</div>
                                <div id="answerText" class="doc-answer-text"></div>
                                <div id="sourceBlock" class="mt-4" hidden>
                                    <div class="stat-label mb-2">Sources</div>
                                    <ul id="sourceList" class="doc-sources"></ul>
                                </div>
                            </div>
                        </div>
                    </section>
                @else
                    <section class="card rise rise-3">
                        <div class="card-head">
                            <h2 class="card-title">Ask this document</h2>
                        </div>
                        <div class="card-pad muted text-sm">
                            Questions open once the document has finished processing.
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    @if (in_array($document->status, ['pending', 'processing'], true) && !$document->isStale())
        <script>
            setTimeout(() => window.location.reload(), 5000);
        </script>
    @endif

    <script>
        (function() {
            'use strict';

            const questionForm = document.getElementById('questionForm');
            if (!questionForm) return;

            const askButton = document.getElementById('askButton');
            const questionInput = document.getElementById('question');
            const answerPanel = document.getElementById('answerPanel');
            const answerText = document.getElementById('answerText');
            const sourceBlock = document.getElementById('sourceBlock');
            const sourceList = document.getElementById('sourceList');
            const questionError = document.getElementById('questionError');

            questionInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
                    event.preventDefault();
                    questionForm.requestSubmit ? questionForm.requestSubmit() : questionForm.submit();
                }
            });

            questionForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const question = questionInput.value.trim();
                if (!question) return;

                askButton.disabled = true;
                askButton.textContent = 'Thinking…';
                questionError.hidden = true;
                answerPanel.hidden = true;

                try {
                    const response = await fetch(@json(route('pdf-documents.ask', $document)), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            question
                        }),
                    });

                    let data;

                    try {
                        data = await response.json();
                    } catch {
                        throw new Error(`The server returned an unexpected response (HTTP ${response.status}).`);
                    }

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to answer this question.');
                    }

                    // Model output is rendered as text only — never as HTML.
                    answerText.textContent = data.answer;
                    sourceList.replaceChildren();

                    for (const source of (data.sources || [])) {
                        const item = document.createElement('li');

                        const page = document.createElement('span');
                        page.className = 'tag';
                        page.textContent = `p. ${source.page ?? '?'}`;

                        const label = document.createElement('span');
                        label.textContent = `${source.title || 'Document'}` +
                            (source.filename ? ` — ${source.filename}` : '');

                        item.append(page, label);
                        sourceList.appendChild(item);
                    }

                    sourceBlock.hidden = sourceList.children.length === 0;
                    answerPanel.hidden = false;
                } catch (error) {
                    questionError.textContent = error.message || 'A network error occurred.';
                    questionError.hidden = false;
                } finally {
                    askButton.disabled = false;
                    askButton.textContent = 'Ask AI';
                }
            });
        })();
    </script>
@endpush
