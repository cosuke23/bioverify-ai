<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $document->title }} - BioVerify AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="{{ route('pdf-documents.index') }}" class="font-extrabold text-blue-800">
                BioVerify AI
            </a>
            <a href="{{ route('pdf-documents.index') }}"
                class="text-sm font-semibold text-blue-700 hover:text-blue-900">
                All documents
            </a>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl space-y-6 px-6 py-10">

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-800">
                {{ session('info') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">
                        PDF Document
                    </p>
                    <h1 class="mt-2 break-words text-2xl font-extrabold text-slate-900">
                        {{ $document->title }}
                    </h1>
                    <p class="mt-2 break-all text-sm text-slate-500">
                        {{ $document->original_filename }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span
                        class="rounded-full px-3 py-1 text-xs font-bold
                        @if ($document->status === 'completed') bg-green-100 text-green-800
                        @elseif ($document->status === 'failed') bg-red-100 text-red-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst($document->status) }}
                    </span>

                    <form method="POST" action="{{ route('pdf-documents.destroy', $document) }}"
                        onsubmit="return confirm('Delete this PDF and remove it from Pong AI\'s knowledge?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-600 hover:text-white">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Pages</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900">
                        {{ $document->page_count ?? '—' }}
                    </p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Processed at</p>
                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $document->processed_at?->format('M d, Y h:i A') ?? 'Not completed' }}
                    </p>
                </div>
            </div>

            @if (in_array($document->status, ['pending', 'processing'], true) && !$document->isStale())
                <div class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    Indexing and summarizing in the background. This can take a few minutes
                    for large PDFs; the page refreshes automatically.
                </div>
            @endif

            @if ($document->status === 'failed' && $document->processing_error)
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4">
                    <h2 class="font-bold text-red-800">Processing error</h2>
                    <p class="mt-2 break-words text-sm text-red-700">
                        {{ $document->processing_error }}
                    </p>
                </div>
            @endif

            @if ($document->canRetry())
                <form method="POST" action="{{ route('pdf-documents.retry', $document) }}" class="mt-5">
                    @csrf
                    <p class="mb-3 text-sm text-slate-600">
                        @if ($document->isStale())
                            Processing has not finished after
                            {{ \App\Models\PdfDocument::STALE_AFTER_MINUTES }} minutes. Make sure the
                            queue worker is running, then try again.
                        @endif
                    </p>
                    <button type="submit"
                        class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-800">
                        Retry processing
                    </button>
                </form>
            @endif
        </section>

        @if ($document->summary)
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-extrabold text-slate-900">AI-generated summary</h2>
                <div class="mt-5 whitespace-pre-wrap leading-7 text-slate-700">{{ $document->summary }}</div>
            </section>
        @endif

        @if ($document->status === 'completed')
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-extrabold text-slate-900">Ask this document</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Ask a question about the PDF. Answers use retrieved excerpts and include source pages.
                </p>

                <form id="questionForm" class="mt-5 space-y-4">
                    <label for="question" class="block text-sm font-semibold text-slate-700">
                        Your question
                    </label>
                    <textarea id="question" name="question" rows="3" maxlength="4000" required
                        placeholder="What are the main findings in this document?"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"></textarea>

                    <button id="askButton" type="submit"
                        class="rounded-xl bg-blue-700 px-6 py-3 font-bold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50">
                        Ask AI
                    </button>
                </form>

                <div id="answerPanel" class="mt-6 hidden rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <h3 class="font-bold text-slate-900">AI answer</h3>
                    <div id="answerText" class="mt-3 whitespace-pre-wrap leading-7 text-slate-700"></div>
                    <div id="sourceList" class="mt-5 space-y-2"></div>
                </div>

                <p id="questionError" role="alert" class="mt-4 hidden text-sm text-red-600"></p>
            </section>
        @endif
    </main>

    @if (in_array($document->status, ['pending', 'processing'], true) && !$document->isStale())
        <script>
            setTimeout(() => window.location.reload(), 5000);
        </script>
    @endif

    <script>
        const questionForm = document.getElementById('questionForm');

        if (questionForm) {
            const askButton = document.getElementById('askButton');
            const questionInput = document.getElementById('question');
            const answerPanel = document.getElementById('answerPanel');
            const answerText = document.getElementById('answerText');
            const sourceList = document.getElementById('sourceList');
            const questionError = document.getElementById('questionError');

            questionForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const question = questionInput.value.trim();

                if (!question) return;

                askButton.disabled = true;
                askButton.textContent = 'Thinking...';
                questionError.classList.add('hidden');
                answerPanel.classList.add('hidden');

                try {
                    const response = await fetch(
                        @json(route('pdf-documents.ask', $document)), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content,
                            },
                            body: JSON.stringify({
                                question
                            }),
                        }
                    );

                    let data;

                    try {
                        data = await response.json();
                    } catch {
                        throw new Error(
                            `The server returned an unexpected response (HTTP ${response.status}).`
                        );
                    }

                    if (!response.ok || !data.success) {
                        throw new Error(
                            data.message || 'Unable to answer this question.'
                        );
                    }

                    answerText.textContent = data.answer;
                    sourceList.replaceChildren();

                    for (const source of (data.sources || [])) {
                        const item = document.createElement('p');
                        item.className = 'text-sm text-slate-600';
                        item.textContent =
                            `${source.title || 'Document'} — ` +
                            `${source.filename || ''} — ` +
                            `Page ${source.page ?? 'unknown'}`;
                        sourceList.appendChild(item);
                    }

                    answerPanel.classList.remove('hidden');
                } catch (error) {
                    questionError.textContent =
                        error.message || 'A network error occurred.';
                    questionError.classList.remove('hidden');
                } finally {
                    askButton.disabled = false;
                    askButton.textContent = 'Ask AI';
                }
            });
        }
    </script>
</body>

</html>
