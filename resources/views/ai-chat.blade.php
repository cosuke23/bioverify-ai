@extends('layouts.pcg')

@section('title', 'Ask Pong AI')

@section('crumbs')
    <span>Knowledge</span><span aria-hidden="true">/</span><strong>Ask Pong AI</strong>
@endsection

@push('head')
    <style>
        /* The full-page assistant replaces the floating widget on this page. */
        #pong-widget {
            display: none !important;
        }

        .chat-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 22px;
            align-items: start;
        }

        /* Conversation card ------------------------------------ */
        .chat-card {
            display: flex;
            flex-direction: column;
            height: clamp(560px, calc(100vh - 230px), 900px);
            overflow: hidden;
        }

        .chat-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--line-soft);
            background: linear-gradient(180deg, #fff, #fafcff);
        }

        .pong-mark {
            display: grid;
            place-items: center;
            flex: 0 0 38px;
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: linear-gradient(160deg, var(--pcg-blue), var(--pcg-blue-800));
            border: 1px solid rgba(244, 197, 66, .55);
            color: var(--pcg-gold);
        }

        .pong-mark svg {
            width: 20px;
            height: 20px;
        }

        .chat-head-title {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        }

        .chat-head-sub {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 3px;
            font-size: 12.5px;
            color: var(--muted);
        }

        .chat-state-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--ok);
        }

        .chat-head[data-busy='true'] .chat-state-dot {
            background: var(--pcg-gold-600);
            animation: chat-blink 1s ease-in-out infinite;
        }

        .chat-head-actions {
            margin-left: auto;
        }

        .chat-log {
            flex: 1;
            overflow-y: auto;
            padding: 24px 24px 8px;
            background:
                radial-gradient(70% 40% at 100% 0%, rgba(0, 51, 160, .035), transparent 70%),
                var(--canvas);
            scroll-behavior: smooth;
        }

        /* Empty state ------------------------------------------ */
        .chat-empty {
            max-width: 760px;
            margin: 8px auto 24px;
        }

        .chat-empty[hidden] {
            display: none;
        }

        .chat-empty-head {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 22px;
        }

        .chat-empty-head .pong-mark {
            flex-basis: 52px;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(0, 51, 160, .22);
        }

        .chat-empty-head .pong-mark svg {
            width: 26px;
            height: 26px;
        }

        .chat-empty h2 {
            margin: 4px 0 0;
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
            color: var(--ink);
        }

        .chat-empty p.lede {
            margin: 8px 0 0;
            color: var(--muted);
            max-width: 560px;
        }

        .prompt-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .prompt-card {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            width: 100%;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--surface);
            color: var(--ink);
            font: inherit;
            text-align: left;
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s, transform .15s;
        }

        .prompt-card:hover:not(:disabled) {
            border-color: var(--pcg-blue);
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .prompt-card:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .prompt-icon {
            display: grid;
            place-items: center;
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--info-soft);
            color: var(--pcg-blue);
        }

        .prompt-icon.is-gold {
            background: var(--pcg-gold-soft);
            color: #8a6406;
        }

        .prompt-icon svg {
            width: 18px;
            height: 18px;
        }

        .prompt-title {
            display: block;
            font-weight: 600;
            font-size: 14.5px;
        }

        .prompt-desc {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }

        /* Messages --------------------------------------------- */
        .msg {
            display: flex;
            gap: 12px;
            max-width: 820px;
            margin: 0 auto 20px;
            animation: chat-in .2s ease-out;
        }

        .msg-user {
            flex-direction: row-reverse;
        }

        .msg-avatar {
            display: grid;
            place-items: center;
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            font-family: var(--font-display);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .msg-user .msg-avatar {
            background: var(--line-soft);
            border: 1px solid var(--line);
            color: var(--ink-2);
        }

        .msg-body {
            min-width: 0;
            max-width: min(680px, 100%);
        }

        .msg-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            font-size: 12px;
            color: var(--muted);
        }

        .msg-user .msg-meta {
            justify-content: flex-end;
        }

        .msg-meta strong {
            color: var(--ink-2);
            font-weight: 600;
        }

        .msg-meta .mono {
            font-size: 11px;
        }

        .bubble {
            padding: 14px 16px;
            border-radius: 4px 14px 14px 14px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-left: 3px solid var(--pcg-blue);
            box-shadow: var(--shadow-sm);
        }

        .msg-user .bubble {
            border: 0;
            border-radius: 14px 4px 14px 14px;
            background: var(--pcg-blue);
            color: #fff;
            box-shadow: 0 6px 16px rgba(0, 51, 160, .18);
        }

        .bubble-text {
            margin: 0;
            font-size: 14.5px;
            line-height: 1.7;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .bubble.is-loading .bubble-text {
            color: var(--muted);
        }

        .typing-dot {
            display: inline-block;
            width: 5px;
            height: 5px;
            margin-left: 4px;
            border-radius: 50%;
            background: var(--pcg-blue);
            vertical-align: middle;
            animation: chat-typing 1.2s ease-in-out infinite;
        }

        .typing-dot:nth-of-type(2) {
            animation-delay: .15s;
        }

        .typing-dot:nth-of-type(3) {
            animation-delay: .3s;
        }

        .cites {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed var(--line);
        }

        .cites-label {
            margin: 0 0 7px;
            font-family: var(--font-display);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .cite-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .cite {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--canvas);
            font-size: 12px;
            color: var(--ink-2);
        }

        .cite-kind {
            flex: 0 0 auto;
            padding: 3px 8px;
            background: var(--info-soft);
            color: var(--pcg-blue);
            font-family: var(--font-mono);
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .cite-kind.is-pdf {
            background: var(--pcg-gold-soft);
            color: #8a6406;
        }

        .cite-text {
            min-width: 0;
            padding: 3px 10px 3px 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .msg-tools {
            display: flex;
            gap: 6px;
            margin-top: 6px;
        }

        .msg-tool {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: var(--muted);
            font: 500 12px var(--font-body);
            cursor: pointer;
        }

        .msg-tool:hover {
            background: var(--line-soft);
            color: var(--pcg-blue);
        }

        .msg-tool svg {
            width: 13px;
            height: 13px;
        }

        /* Composer --------------------------------------------- */
        .composer-wrap {
            padding: 14px 20px 16px;
            border-top: 1px solid var(--line);
            background: var(--surface);
        }

        .chat-error {
            margin-bottom: 10px;
        }

        .chat-error[hidden] {
            display: none;
        }

        .composer {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
        }

        .composer:focus-within {
            border-color: var(--pcg-blue);
            box-shadow: 0 0 0 4px rgba(0, 51, 160, .1);
        }

        .composer textarea {
            display: block;
            width: 100%;
            min-height: 52px;
            max-height: 180px;
            padding: 14px 16px 6px;
            border: 0;
            outline: 0;
            resize: none;
            background: transparent;
            color: var(--ink);
            font: 400 15px/1.55 var(--font-body);
        }

        .composer textarea::placeholder {
            color: #94a3b8;
        }

        .composer-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 6px 8px 8px 16px;
        }

        .composer-hint {
            font-size: 12px;
            color: var(--muted);
        }

        .composer-hint kbd {
            display: inline-block;
            min-width: 20px;
            padding: 1px 6px;
            border: 1px solid var(--line);
            border-bottom-width: 2px;
            border-radius: 5px;
            background: var(--canvas);
            color: var(--ink-2);
            font-family: var(--font-mono);
            font-size: 11px;
        }

        .composer-count {
            margin-left: 8px;
            font-family: var(--font-mono);
            font-size: 11px;
            color: #94a3b8;
        }

        .composer-count.is-near {
            color: var(--warn);
        }

        .composer-note {
            margin: 8px 0 0;
            text-align: center;
            font-size: 11.5px;
            color: var(--muted);
        }

        /* Aside ------------------------------------------------ */
        .chat-aside {
            display: grid;
            gap: 16px;
        }

        .aside-list {
            display: grid;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 13.5px;
        }

        .aside-list li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .aside-list svg {
            flex: 0 0 18px;
            width: 18px;
            height: 18px;
            margin-top: 2px;
            color: var(--pcg-blue);
        }

        .aside-list small {
            display: block;
            color: var(--muted);
            font-size: 12.5px;
        }

        .ask-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            background: var(--surface);
            color: var(--ink-2);
            font: 500 13.5px/1.35 var(--font-body);
            text-align: left;
            cursor: pointer;
            transition: border-color .15s, color .15s;
        }

        .ask-link:hover:not(:disabled) {
            border-color: var(--pcg-blue);
            color: var(--pcg-blue);
        }

        .ask-link:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .ask-link svg {
            flex: 0 0 14px;
            width: 14px;
            height: 14px;
        }

        @keyframes chat-in {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes chat-typing {

            0%,
            60%,
            100% {
                opacity: .25;
                transform: translateY(0);
            }

            30% {
                opacity: 1;
                transform: translateY(-3px);
            }
        }

        @keyframes chat-blink {
            50% {
                opacity: .3;
            }
        }

        @media (max-width: 1100px) {
            .chat-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .chat-aside {
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .chat-card {
                height: calc(100dvh - 150px);
                min-height: 480px;
            }

            .chat-log {
                padding: 16px 12px 4px;
            }

            .prompt-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .chat-empty h2 {
                font-size: 26px;
            }

            .msg-avatar {
                display: none;
            }

            .composer-wrap {
                padding: 10px 12px 12px;
            }

            .composer-hint {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .msg,
            .typing-dot,
            .chat-head[data-busy='true'] .chat-state-dot {
                animation: none;
            }

            .chat-log {
                scroll-behavior: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-head rise">
        <div>
            <p class="eyebrow">Knowledge · AI assistant</p>
            <h1 class="page-title">Ask Pong AI</h1>
            <p class="page-lede">
                Ask about BioVerify procedures, PCG reference material and uploaded documents. Answers cite the
                notes and PDF pages they were drawn from.
            </p>
        </div>
        <span class="badge badge-info">Grounded in your knowledge base</span>
    </div>

    <div class="chat-grid">
        <section class="card chat-card rise rise-2" aria-label="Pong AI conversation">
            <div class="chat-head" id="chatHead" data-busy="false">
                <span class="pong-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2.5 4 5.5v6c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10v-6z" />
                        <circle cx="12" cy="7.8" r="1.4" />
                        <path d="M12 9.2v8" />
                        <path d="M9.5 11.5h5" />
                        <path d="M8 14.6c.6 1.6 2.2 2.6 4 2.6s3.4-1 4-2.6" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <div class="chat-head-title">Pong AI</div>
                    <div class="chat-head-sub">
                        <span class="chat-state-dot" aria-hidden="true"></span>
                        <span id="chatState" role="status">Ready · Knowledge assistant</span>
                    </div>
                </div>
                <div class="chat-head-actions">
                    <button type="button" id="clearChat" class="btn btn-ghost btn-sm" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        New conversation
                    </button>
                </div>
            </div>

            <div id="chatMessages" class="chat-log" role="log" aria-label="Conversation" aria-live="polite"
                aria-relevant="additions text">

                <div id="welcomePanel" class="chat-empty">
                    <div class="chat-empty-head">
                        <span class="pong-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                <path d="M8 9h8" />
                                <path d="M8 13h5" />
                            </svg>
                        </span>
                        <div>
                            <p class="eyebrow">Mabuhay, {{ \Illuminate\Support\Str::before(trim(auth()->user()?->name ?: 'Administrator'), ' ') }}</p>
                            <h2>What do you need to know?</h2>
                            <p class="lede">
                                Pong AI searches the BioVerify knowledge base and your uploaded PDFs, then answers with
                                citations. Pick a starting point or type your own question.
                            </p>
                        </div>
                    </div>

                    <div class="prompt-grid">
                        <button type="button" class="prompt-card"
                            data-prompt="Explain step by step how BioVerify AI verifies a Coast Guard member's identity at the kiosk, including the face liveness check and voice verification.">
                            <span class="prompt-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2" />
                                    <circle cx="12" cy="11" r="3" />
                                    <path d="M7.5 17.5a5 5 0 0 1 9 0" />
                                </svg>
                            </span>
                            <span>
                                <span class="prompt-title">How verification works</span>
                                <span class="prompt-desc">Face match, liveness check and voice verification at the
                                    kiosk.</span>
                            </span>
                        </button>

                        <button type="button" class="prompt-card"
                            data-prompt="What are the steps and quality requirements for enrolling a new PCG personnel record with face samples and a voice sample in BioVerify AI?">
                            <span class="prompt-icon is-gold" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M19 8v6M22 11h-6" />
                                </svg>
                            </span>
                            <span>
                                <span class="prompt-title">Enrolling personnel</span>
                                <span class="prompt-desc">Steps and sample quality for new face and voice
                                    records.</span>
                            </span>
                        </button>

                        <button type="button" class="prompt-card"
                            data-prompt="A Coast Guard member failed face verification at the kiosk. What are the likely causes, and what should an administrator check before re-enrolling them?">
                            <span class="prompt-icon is-gold" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m10.3 3.9-8.1 14a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3l-8.1-14a2 2 0 0 0-3.4 0z" />
                                    <path d="M12 9v4M12 17h.01" />
                                </svg>
                            </span>
                            <span>
                                <span class="prompt-title">Troubleshoot a failed match</span>
                                <span class="prompt-desc">Likely causes and what to check before
                                    re-enrolment.</span>
                            </span>
                        </button>

                        <button type="button" class="prompt-card"
                            data-prompt="Summarise the mandate and core functions of the Philippine Coast Guard and its Marine Environmental Protection Command, citing your sources.">
                            <span class="prompt-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <path d="M14 2v6h6" />
                                    <path d="M8 13h8M8 17h5" />
                                </svg>
                            </span>
                            <span>
                                <span class="prompt-title">PCG &amp; MEPCOM reference</span>
                                <span class="prompt-desc">Mandate and core functions, with citations.</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="composer-wrap">
                <div id="errorBox" class="alert alert-danger chat-error" role="alert" hidden></div>

                <form id="chatForm" class="composer">
                    <label for="messageInput" class="sr-only">Ask Pong AI a question</label>
                    <textarea id="messageInput" name="message" rows="1" maxlength="4000"
                        placeholder="Ask Pong AI about procedures, personnel records or uploaded documents…" required></textarea>

                    <div class="composer-bar">
                        <p class="composer-hint" id="composerHint">
                            <kbd>Enter</kbd> to send · <kbd>Shift</kbd> + <kbd>Enter</kbd> for a new line
                            <span class="composer-count" id="charCount" aria-hidden="true">0 / 4000</span>
                        </p>

                        <button id="sendButton" type="submit" class="btn btn-primary">
                            <span id="sendButtonText">Send</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m22 2-7 20-4-9-9-4Z" />
                                <path d="M22 2 11 13" />
                            </svg>
                        </button>
                    </div>
                </form>

                <p class="composer-note">AI-generated answers can be wrong. Verify important information against
                    official PCG references.</p>
            </div>
        </section>

        <aside class="chat-aside" aria-label="About Pong AI">
            <div class="card card-pad rise rise-3">
                <p class="eyebrow" style="margin:0 0 12px">Knowledge sources</p>
                <ul class="aside-list">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                        </svg>
                        <span>Obsidian knowledge base
                            <small>System notes and PCG reference material.</small></span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                        </svg>
                        <span>Uploaded PDF documents
                            <small>Cited by document and page number.</small></span>
                    </li>
                </ul>
                <a href="{{ route('pdf-documents.index') }}" class="btn btn-ghost btn-sm" style="width:100%;margin-top:16px">
                    Manage documents
                </a>
            </div>

            <div class="card card-pad rise rise-3">
                <p class="eyebrow" style="margin:0 0 12px">Try asking</p>
                <div style="display:grid;gap:8px">
                    <button type="button" class="ask-link"
                        data-prompt="What personal and biometric data does BioVerify AI store, and how is access to it restricted?">
                        What biometric data is stored?
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button type="button" class="ask-link"
                        data-prompt="What is recorded in the BioVerify AI recognition audit log, and how should administrators review it?">
                        What does the audit log record?
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button type="button" class="ask-link"
                        data-prompt="Summarise the key points from the uploaded PDF documents and cite the pages you used.">
                        Summarise uploaded PDFs
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="alert alert-warn rise rise-4" style="display:block">
                <strong style="display:block;margin-bottom:4px">Handling guidance</strong>
                Do not enter passwords, classified material, or personal and biometric data that the question
                does not need.
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            'use strict';

            const form = document.getElementById('chatForm');
            const input = document.getElementById('messageInput');
            const messages = document.getElementById('chatMessages');
            const welcomePanel = document.getElementById('welcomePanel');
            const sendButton = document.getElementById('sendButton');
            const sendButtonText = document.getElementById('sendButtonText');
            const errorBox = document.getElementById('errorBox');
            const clearButton = document.getElementById('clearChat');
            const chatHead = document.getElementById('chatHead');
            const chatState = document.getElementById('chatState');
            const charCount = document.getElementById('charCount');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const sendUrl = @json(route('ai-chat.send'));
            const maxLength = Number(input.getAttribute('maxlength')) || 4000;

            const timeFormat = new Intl.DateTimeFormat('en-PH', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const svgNs = 'http://www.w3.org/2000/svg';

            let busy = false;

            function scrollToBottom() {
                messages.scrollTop = messages.scrollHeight;
            }

            // Builds an SVG icon from path data only (no HTML parsing).
            function icon(paths, extra) {
                const svg = document.createElementNS(svgNs, 'svg');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('stroke-width', '1.7');
                svg.setAttribute('stroke-linecap', 'round');
                svg.setAttribute('stroke-linejoin', 'round');
                svg.setAttribute('aria-hidden', 'true');

                paths.forEach(d => {
                    const path = document.createElementNS(svgNs, 'path');
                    path.setAttribute('d', d);
                    svg.appendChild(path);
                });

                if (extra) extra(svg);

                return svg;
            }

            function shieldIcon() {
                return icon([
                    'M12 2.5 4 5.5v6c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10v-6z',
                    'M12 9.2v8',
                    'M9.5 11.5h5',
                    'M8 14.6c.6 1.6 2.2 2.6 4 2.6s3.4-1 4-2.6'
                ], svg => {
                    const c = document.createElementNS(svgNs, 'circle');
                    c.setAttribute('cx', '12');
                    c.setAttribute('cy', '7.8');
                    c.setAttribute('r', '1.4');
                    svg.appendChild(c);
                });
            }

            // "PDF: Title (file.pdf) — Page 3" / "Obsidian: note.md" -> kind + label.
            function parseSource(source) {
                let raw;

                if (source && typeof source === 'object') {
                    raw = String(source.label || source.title || source.source || '');
                } else {
                    raw = String(source ?? '');
                }

                const match = raw.match(/^\s*(PDF|Obsidian)\s*:\s*(.+)$/i);

                if (!match) return {
                    kind: 'Source',
                    text: raw
                };

                return {
                    kind: match[1].toLowerCase() === 'pdf' ? 'PDF' : 'Obsidian',
                    text: match[2]
                };
            }

            function renderSources(sources) {
                const wrap = document.createElement('div');
                wrap.className = 'cites';

                const label = document.createElement('p');
                label.className = 'cites-label';
                label.textContent = sources.length === 1 ? '1 source' : sources.length + ' sources';

                const list = document.createElement('ul');
                list.className = 'cite-list';

                sources.forEach(source => {
                    const parsed = parseSource(source);
                    if (!parsed.text) return;

                    const item = document.createElement('li');
                    item.className = 'cite';
                    item.title = parsed.kind + ': ' + parsed.text;

                    const kind = document.createElement('span');
                    kind.className = 'cite-kind' + (parsed.kind === 'PDF' ? ' is-pdf' : '');
                    kind.textContent = parsed.kind;

                    const text = document.createElement('span');
                    text.className = 'cite-text';
                    text.textContent = parsed.text;

                    item.append(kind, text);
                    list.appendChild(item);
                });

                wrap.append(label, list);

                return wrap;
            }

            function copyButton(text) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'msg-tool';

                const label = document.createElement('span');
                label.textContent = 'Copy';

                button.append(
                    icon(['M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2',
                        'M10 2h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z'
                    ]),
                    label
                );

                button.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(text);
                        label.textContent = 'Copied';
                    } catch {
                        label.textContent = 'Copy unavailable';
                    }

                    setTimeout(() => {
                        label.textContent = 'Copy';
                    }, 1600);
                });

                return button;
            }

            function addMessage(role, text, sources = [], options = {}) {
                welcomePanel.hidden = true;

                const isUser = role === 'user';

                const row = document.createElement('div');
                row.className = 'msg ' + (isUser ? 'msg-user' : 'msg-assistant');

                const avatar = document.createElement('div');
                avatar.className = 'msg-avatar' + (isUser ? '' : ' pong-mark');
                avatar.setAttribute('aria-hidden', 'true');

                if (isUser) {
                    avatar.textContent = 'YOU';
                } else {
                    avatar.appendChild(shieldIcon());
                }

                const body = document.createElement('div');
                body.className = 'msg-body';

                const meta = document.createElement('div');
                meta.className = 'msg-meta';

                const who = document.createElement('strong');
                who.textContent = isUser ? 'You' : 'Pong AI';

                const time = document.createElement('span');
                time.className = 'mono';
                time.textContent = timeFormat.format(new Date());

                meta.append(who, time);

                const bubble = document.createElement('div');
                bubble.className = 'bubble' + (options.loading ? ' is-loading' : '');

                const content = document.createElement('p');
                content.className = 'bubble-text';

                if (options.loading) {
                    const loadingText = document.createElement('span');
                    loadingText.textContent = 'Searching the knowledge base';
                    content.appendChild(loadingText);

                    for (let i = 0; i < 3; i++) {
                        const dot = document.createElement('span');
                        dot.className = 'typing-dot';
                        content.appendChild(dot);
                    }
                } else {
                    // Model output is always rendered as text, never as HTML.
                    content.textContent = String(text ?? '');
                }

                bubble.appendChild(content);

                if (!isUser && Array.isArray(sources) && sources.length > 0) {
                    bubble.appendChild(renderSources(sources));
                }

                body.append(meta, bubble);

                if (!isUser && !options.loading && text) {
                    const tools = document.createElement('div');
                    tools.className = 'msg-tools';
                    tools.appendChild(copyButton(String(text)));
                    body.appendChild(tools);
                }

                row.append(avatar, body);
                messages.appendChild(row);
                scrollToBottom();

                clearButton.disabled = busy;

                return row;
            }

            function showError(message) {
                errorBox.textContent = message;
                errorBox.hidden = false;
            }

            function hideError() {
                errorBox.textContent = '';
                errorBox.hidden = true;
            }

            function setBusy(state) {
                busy = state;

                sendButton.disabled = state;
                sendButtonText.textContent = state ? 'Thinking…' : 'Send';
                chatHead.dataset.busy = String(state);
                chatState.textContent = state ? 'Searching knowledge base…' : 'Ready · Knowledge assistant';
                clearButton.disabled = state || !messages.querySelector('.msg');

                document.querySelectorAll('[data-prompt]').forEach(button => {
                    button.disabled = state;
                });
            }

            function autosize() {
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 180) + 'px';

                const length = input.value.length;
                charCount.textContent = length + ' / ' + maxLength;
                charCount.classList.toggle('is-near', length > maxLength * 0.9);
            }

            async function sendMessage(message) {
                message = String(message ?? '').trim();

                if (busy || !message) return;

                hideError();
                setBusy(true);

                addMessage('user', message);

                const loadingMessage = addMessage('assistant', '', [], {
                    loading: true
                });

                input.value = '';
                autosize();

                try {
                    const response = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message
                        })
                    });

                    if (response.status === 419) {
                        throw new Error('Your session expired. Reload the page and try again.');
                    }

                    if (response.status === 401) {
                        throw new Error('You have been signed out. Sign in again to use Pong AI.');
                    }

                    if (response.status === 429) {
                        throw new Error('Too many messages. Please wait a minute and try again.');
                    }

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
                            data.message || 'The AI request failed. Please try again.'
                        );
                    }

                    loadingMessage.remove();

                    addMessage(
                        'assistant',
                        data.answer || 'The AI service returned an empty response.',
                        Array.isArray(data.sources) ? data.sources : []
                    );
                } catch (error) {
                    loadingMessage.remove();
                    showError(error.message || 'Unable to contact the AI service. Please try again.');

                    // Give the message back so it can be re-sent,
                    // unless something new was typed meanwhile.
                    if (!input.value.trim()) {
                        input.value = message;
                        autosize();
                    }
                } finally {
                    setBusy(false);
                    input.focus();
                }
            }

            form.addEventListener('submit', event => {
                event.preventDefault();
                sendMessage(input.value);
            });

            input.addEventListener('keydown', event => {
                if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
                    event.preventDefault();
                    form.requestSubmit();
                }
            });

            input.addEventListener('input', autosize);

            document.querySelectorAll('[data-prompt]').forEach(button => {
                button.addEventListener('click', () => {
                    if (busy) return;

                    input.value = button.dataset.prompt || '';
                    sendMessage(input.value);
                });
            });

            clearButton.addEventListener('click', () => {
                if (busy) return;

                messages.querySelectorAll('.msg').forEach(row => row.remove());
                welcomePanel.hidden = false;
                hideError();
                clearButton.disabled = true;
                messages.scrollTop = 0;
                input.focus();
            });

            autosize();
        })();
    </script>
@endpush
