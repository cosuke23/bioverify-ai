<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($document->title); ?> - BioVerify AI</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="<?php echo e(route('pdf-documents.index')); ?>" class="font-extrabold text-blue-800">
                BioVerify AI
            </a>
            <a href="<?php echo e(route('pdf-documents.index')); ?>"
                class="text-sm font-semibold text-blue-700 hover:text-blue-900">
                All documents
            </a>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl space-y-6 px-6 py-10">

        <?php if(session('success')): ?>
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('info')): ?>
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-800">
                <?php echo e(session('info')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">
                        PDF Document
                    </p>
                    <h1 class="mt-2 break-words text-2xl font-extrabold text-slate-900">
                        <?php echo e($document->title); ?>

                    </h1>
                    <p class="mt-2 break-all text-sm text-slate-500">
                        <?php echo e($document->original_filename); ?>

                    </p>
                </div>

                <span
                    class="rounded-full px-3 py-1 text-xs font-bold
                    <?php if($document->status === 'completed'): ?> bg-green-100 text-green-800
                    <?php elseif($document->status === 'failed'): ?> bg-red-100 text-red-800
                    <?php else: ?> bg-blue-100 text-blue-800 <?php endif; ?>">
                    <?php echo e(ucfirst($document->status)); ?>

                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Pages</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900">
                        <?php echo e($document->page_count ?? '—'); ?>

                    </p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Processed at</p>
                    <p class="mt-2 font-semibold text-slate-900">
                        <?php echo e($document->processed_at?->format('M d, Y h:i A') ?? 'Not completed'); ?>

                    </p>
                </div>
            </div>

            <?php if($document->status === 'failed' && $document->processing_error): ?>
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4">
                    <h2 class="font-bold text-red-800">Processing error</h2>
                    <p class="mt-2 break-words text-sm text-red-700">
                        <?php echo e($document->processing_error); ?>

                    </p>
                </div>
            <?php endif; ?>
        </section>

        <?php if($document->summary): ?>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-extrabold text-slate-900">AI-generated summary</h2>
                <div class="mt-5 whitespace-pre-wrap leading-7 text-slate-700"><?php echo e($document->summary); ?></div>
            </section>
        <?php endif; ?>

        <?php if($document->status === 'completed'): ?>
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
        <?php endif; ?>
    </main>

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
                        <?php echo json_encode(route('pdf-documents.ask', $document), 512) ?>, {
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

                    const data = await response.json();

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
<?php /**PATH C:\xampp\htdocs\bioverify-ai\resources\views/pdf-documents/show.blade.php ENDPATH**/ ?>