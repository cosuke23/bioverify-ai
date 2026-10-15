<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pong Pagong | PCG Knowledge Assistant</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --navy: #082d55;
            --navy-dark: #061b31;
            --blue: #124b80;
            --gold: #c4a45d;
            --canvas: #f5f7fb;
            --border: #e5eaf1;
            --muted: #718096;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 78px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
        }

        body {
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background: var(--canvas);
            color: #1e293b;
        }

        button,
        textarea {
            font: inherit;
        }

        button:focus-visible,
        a:focus-visible,
        textarea:focus-visible {
            outline: 3px solid #8db9ed;
            outline-offset: 3px;
        }

        .app-shell {
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: relative;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(165deg, #0b345d 0%, #071f39 58%, #051729 100%);
            box-shadow: 8px 0 35px rgb(15 35 60 / 5%);
            transition: width .25s ease, min-width .25s ease, transform .25s ease;
            z-index: 50;
        }

        .sidebar-collapsed {
            width: var(--sidebar-collapsed-width) !important;
            min-width: var(--sidebar-collapsed-width) !important;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border: 1px solid rgb(196 164 93 / 80%);
            border-radius: 14px;
            background: rgb(255 255 255 / 7%);
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .gold-line {
            height: 2px;
            flex-shrink: 0;
            background: linear-gradient(90deg, var(--gold), rgb(196 164 93 / 35%), transparent);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 46px;
            padding: 11px 13px;
            border: 1px solid transparent;
            border-radius: 12px;
            color: #c7d5e5;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }

        .nav-link:hover {
            background: rgb(255 255 255 / 7%);
            color: white;
        }

        .nav-link.active {
            border-color: rgb(196 164 93 / 38%);
            background: linear-gradient(100deg, rgb(255 255 255 / 12%), rgb(255 255 255 / 5%));
            color: white;
        }

        .nav-icon {
            display: flex;
            width: 23px;
            height: 23px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            color: #c5d6e9;
            font-size: 18px;
        }

        .nav-link.active .nav-icon {
            color: #f0d391;
        }

        .section-label {
            color: #92abc7;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .17em;
            text-transform: uppercase;
        }

        .service-card {
            border: 1px solid rgb(255 255 255 / 10%);
            background: rgb(255 255 255 / 5%);
        }

        .service-dot {
            position: relative;
            display: inline-flex;
            width: 8px;
            height: 8px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #34d399;
        }

        .service-dot::after {
            position: absolute;
            inset: -4px;
            border: 1px solid rgb(52 211 153 / 30%);
            border-radius: 50%;
            content: "";
        }

        .sidebar-label {
            min-width: 0;
            transition: opacity .15s ease;
        }

        .sidebar-collapsed .sidebar-label,
        .sidebar-collapsed .nav-badge {
            display: none !important;
        }

        .sidebar-collapsed .sidebar-brand {
            padding: 18px 0;
        }

        .sidebar-collapsed .sidebar-brand>div {
            justify-content: center;
        }

        .sidebar-collapsed .sidebar-workspace {
            justify-content: center;
            padding: 14px 0;
        }

        .sidebar-collapsed .nav-link {
            justify-content: center;
            gap: 0;
            padding: 12px 0;
            margin-bottom: 4px;
        }

        .sidebar-collapsed .sidebar-nav {
            padding-right: 10px;
            padding-left: 10px;
        }

        .sidebar-collapsed .service-card {
            padding: 14px 0;
            border-color: transparent;
            background: transparent;
            display: flex;
            justify-content: center;
        }

        .sidebar-collapsed .sidebar-footer {
            padding: 14px 0;
        }

        .sidebar-collapsed .sidebar-footer>div {
            justify-content: center;
        }

        .sidebar-collapsed .sidebar-toggle-icon {
            transform: rotate(180deg);
        }

        .topbar {
            background: rgb(255 255 255 / 94%);
            backdrop-filter: blur(16px);
        }

        .eyebrow {
            color: var(--blue);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .17em;
            text-transform: uppercase;
        }

        .hero-card {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 8px 30px rgb(15 35 60 / 3%);
        }

        .hero-decoration {
            position: absolute;
            top: -100px;
            right: -70px;
            width: 280px;
            height: 280px;
            border: 1px solid rgb(18 75 128 / 7%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-decoration::after {
            position: absolute;
            inset: 28px;
            border: 1px solid rgb(18 75 128 / 7%);
            border-radius: 50%;
            content: "";
        }

        .hero-icon {
            display: flex;
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid rgb(196 164 93 / 45%);
            border-radius: 19px;
            background: linear-gradient(145deg, #103f70, #082747);
            box-shadow: 0 8px 20px rgb(8 45 85 / 15%);
            color: white;
        }

        .suggestion-card {
            display: block;
            width: 100%;
            min-height: 120px;
            border: 1px solid var(--border);
            border-radius: 17px;
            background: white;
            padding: 18px;
            text-align: left;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .suggestion-card:hover:not(:disabled) {
            transform: translateY(-3px);
            border-color: #b9cadd;
            box-shadow: 0 12px 26px rgb(8 45 85 / 7%);
        }

        .suggestion-card:disabled {
            cursor: not-allowed;
            opacity: .55;
        }

        .suggestion-icon {
            display: flex;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            font-size: 20px;
        }

        .composer {
            border: 1px solid #dce3ec;
            border-radius: 20px;
            background: white;
            box-shadow: 0 8px 30px rgb(15 35 60 / 5%);
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .composer:focus-within {
            border-color: #8ca9c8;
            box-shadow: 0 0 0 4px rgb(18 75 128 / 7%);
        }

        .composer textarea {
            display: block;
            width: 100%;
            resize: vertical;
            border: 0;
            outline: 0;
            background: transparent;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.7;
        }

        .composer textarea::placeholder {
            color: #9aa7b8;
        }

        .send-button {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 0;
            border-radius: 12px;
            background: var(--navy);
            padding: 0 18px;
            color: white;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: background .2s ease, transform .2s ease;
        }

        .send-button:hover:not(:disabled) {
            transform: translateY(-1px);
            background: var(--blue);
        }

        .send-button:disabled {
            cursor: not-allowed;
            opacity: .6;
        }

        .message-row {
            display: flex;
            width: 100%;
            max-width: 850px;
            gap: 12px;
            margin: 0 auto 24px;
            animation: message-in .2s ease-out;
        }

        .message-avatar {
            display: flex;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            font-size: 10px;
            font-weight: 900;
        }

        .assistant-avatar {
            border: 1px solid rgb(196 164 93 / 40%);
            background: var(--navy);
            color: white;
        }

        .user-avatar {
            background: #e2e8f0;
            color: #334155;
        }

        .message-bubble {
            min-width: 0;
            flex: 1;
            border: 1px solid var(--border);
            border-radius: 4px 18px 18px 18px;
            background: white;
            padding: 18px 20px;
            box-shadow: 0 3px 12px rgb(15 35 60 / 3%);
        }

        .user-bubble {
            border-color: var(--blue);
            border-radius: 18px 4px 18px 18px;
            background: var(--blue);
            color: white;
        }

        .message-content {
            overflow-wrap: anywhere;
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.85;
        }

        .typing-dot {
            display: inline-block;
            padding: 0 2px;
            animation: typing 1.2s infinite ease-in-out;
        }

        .typing-dot:nth-child(2) {
            animation-delay: .15s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: .3s;
        }

        #sidebarOverlay {
            visibility: hidden;
            opacity: 0;
            transition: opacity .25s ease, visibility .25s ease;
        }

        #sidebarOverlay.overlay-visible {
            visibility: visible;
            opacity: 1;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                opacity: .3;
                transform: translateY(0);
            }

            30% {
                opacity: 1;
                transform: translateY(-4px);
            }
        }

        @keyframes message-in {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Desktop layout */
        @media (min-width: 768px) {
            .sidebar {
                position: sticky;
                top: 0;
                align-self: flex-start;
                transform: none !important;
            }

            #sidebarOverlay {
                display: none !important;
            }

            #mobileMenuButton {
                display: none !important;
            }
        }

        /* Mobile drawer */
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: min(300px, 86vw);
                min-width: min(300px, 86vw);
                height: 100dvh;
                transform: translateX(-105%);
                z-index: 50;
            }

            .sidebar.sidebar-open {
                transform: translateX(0) !important;
            }

            .sidebar-collapsed {
                width: min(300px, 86vw) !important;
                min-width: min(300px, 86vw) !important;
            }

            /* Always show labels in the mobile drawer. */
            .sidebar .sidebar-label,
            .sidebar .nav-badge {
                display: block !important;
            }

            .sidebar .sidebar-brand {
                padding: 24px 20px;
            }

            .sidebar .sidebar-brand>div {
                justify-content: flex-start;
            }

            .sidebar .sidebar-workspace {
                justify-content: space-between;
                padding: 20px 20px 8px;
            }

            .sidebar .nav-link {
                justify-content: flex-start;
                gap: 12px;
                padding: 11px 13px;
            }

            .sidebar .sidebar-nav {
                padding-right: 12px;
                padding-left: 12px;
            }

            .sidebar .service-card {
                display: block;
                padding: 16px;
                border-color: rgb(255 255 255 / 10%);
                background: rgb(255 255 255 / 5%);
            }

            .sidebar .sidebar-footer {
                padding: 16px 20px;
            }

            .sidebar .sidebar-footer>div {
                justify-content: flex-start;
            }

            .sidebar .sidebar-toggle-icon {
                transform: none;
            }

            .hero-card {
                border-radius: 19px;
            }

            .suggestion-card {
                min-height: 104px;
                padding: 14px;
            }

            .message-bubble {
                padding: 15px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body>
    <div class="app-shell flex">

        <!-- One overlay only -->
        <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-slate-950/60 md:hidden" aria-hidden="true"></div>

        <!-- Sidebar -->
        <aside id="pcgSidebar" class="sidebar text-white">

            <div class="sidebar-brand px-5 py-6">
                <div class="flex items-center gap-3">
                    <div class="brand-mark">PCG</div>
                    <div class="sidebar-label min-w-0">
                        <h1 class="whitespace-nowrap text-sm font-black tracking-[.13em]">Pong Pagong</h1>
                        <p class="mt-1 whitespace-nowrap text-xs text-blue-200">Knowledge Assistant</p>
                        <p
                            class="mt-2 whitespace-nowrap text-[9px] font-bold uppercase tracking-[.13em] text-amber-300">
                            Personnel Support Workspace
                        </p>
                    </div>
                </div>
            </div>

            <div class="gold-line"></div>

            <div class="sidebar-workspace flex items-center justify-between px-5 pb-2 pt-5">
                <p class="sidebar-label section-label">Workspace</p>

                <button id="sidebarToggle" type="button"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-blue-100 transition hover:bg-white/10 hover:text-white"
                    aria-label="Collapse sidebar" aria-expanded="true" title="Collapse sidebar">
                    <svg class="sidebar-toggle-icon h-4 w-4 transition-transform duration-300" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav flex-1 space-y-1 overflow-x-hidden overflow-y-auto px-3 pb-5">
                <a href="{{ route('ai-chat.index') }}" class="nav-link active" aria-current="page" title="Pong Pagong">
                    <span class="nav-icon">✳</span>
                    <span class="sidebar-label nav-text">Pong Pagong</span>
                    <span
                        class="nav-badge ml-auto rounded-md bg-white/10 px-2 py-1 text-[9px] font-bold text-amber-200">AI</span>
                </a>
                <a href="{{ route('biometric.login') }}" class="nav-link" title="Biometric Login">
                    <span class="nav-icon">⌘</span>
                    <span class="sidebar-label nav-text">Biometric Login</span>
                </a>

                <a href="{{ route('face.recognition.index') }}" class="nav-link" title="Face Recognition">
                    <span class="nav-icon">◎</span>
                    <span class="sidebar-label nav-text">Face Recognition</span>
                </a>

                <a href="{{ route('people.index') }}" class="nav-link" title="Personnel Records">
                    <span class="nav-icon">♙</span>
                    <span class="sidebar-label nav-text">Personnel Records</span>
                </a>

                <div class="sidebar-label px-3 pb-3 pt-9">
                    <p class="section-label">Environment</p>
                </div>

                <div class="service-card mx-1 rounded-2xl p-4">
                    <div class="flex items-center gap-3">
                        <span class="service-dot"></span>
                        <div class="sidebar-label min-w-0">
                            <p class="whitespace-nowrap text-xs font-bold text-white">AI Workspace</p>
                            <p class="mt-1 whitespace-nowrap text-[10px] text-blue-200">Configured service</p>
                        </div>
                    </div>

                    <p class="sidebar-label mt-4 text-[11px] leading-5 text-blue-100/70">
                        Pong Pagong uses your configured AI service and searchable knowledge base.
                    </p>

                    <div class="sidebar-label mt-4 border-t border-white/10 pt-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-200">Security reminder</p>
                        <p class="mt-2 text-[11px] leading-5 text-blue-100/70">
                            Never submit passwords, classified material, or unnecessary personal and biometric data.
                        </p>
                    </div>
                </div>
            </nav>

            <div class="sidebar-footer border-t border-white/10 px-5 py-4">
                <div class="flex items-center gap-2">
                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-xs font-bold text-amber-200">P</span>
                    <div class="sidebar-label min-w-0">
                        <p class="whitespace-nowrap text-[11px] font-bold text-white">Pong Pagong</p>
                        <p class="mt-1 whitespace-nowrap text-[9px] text-blue-200/60">Knowledge &amp; technical support
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main workspace -->
        <main class="flex min-h-screen min-w-0 flex-1 flex-col">

            <header class="topbar sticky top-0 z-30 border-b border-slate-200/80">
                <div class="flex min-h-[76px] items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-9">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="mobileMenuButton" type="button" aria-label="Open navigation" aria-expanded="false"
                            aria-controls="pcgSidebar"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-lg text-slate-700 md:hidden">
                            <span id="menuIcon" aria-hidden="true">☰</span>
                        </button>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-black tracking-tight text-slate-900 sm:text-lg">Pong Pagong
                                </h2>
                                <span
                                    class="hidden rounded-md border border-blue-100 bg-blue-50 px-2 py-1 text-[9px] font-extrabold uppercase tracking-wider text-blue-800 sm:inline-block">
                                    Knowledge Retrieval
                                </span>
                            </div>
                            <p class="mt-1 truncate text-xs text-slate-500 sm:text-sm">
                                Your technical and knowledge support workspace
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex shrink-0 items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <div class="hidden sm:block">
                            <p class="text-[11px] font-extrabold text-emerald-800">Local AI</p>
                            <p class="mt-0.5 text-[9px] text-emerald-700">Configured environment</p>
                        </div>
                    </div>
                </div>
                <div class="gold-line"></div>
            </header>

            <!-- Conversation -->
            <section id="chatMessages" class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 sm:py-8 lg:px-9"
                aria-label="Conversation" aria-live="polite" aria-relevant="additions text">

                <div id="welcomePanel" class="mx-auto max-w-[850px]">
                    <div class="hero-card">
                        <div class="hero-decoration"></div>

                        <div class="relative border-b border-slate-100 px-5 py-7 sm:px-8 sm:py-9 lg:px-10">
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                                <div class="hero-icon">
                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3l1.9 5.8L20 11l-6.1 2.2L12 19l-2-5.8L4 11l6-2.2L12 3z" />
                                        <path d="M19 15l.9 2.1L22 18l-2.1.9L19 21l-.9-2.1L16 18l2.1-.9L19 15z" />
                                    </svg>
                                </div>

                                <div class="relative min-w-0">
                                    <p class="eyebrow">Your knowledge, one conversation away</p>
                                    <h3
                                        class="mt-3 text-2xl font-black leading-tight tracking-tight text-slate-900 sm:text-3xl lg:text-[38px]">
                                        What can I help you solve?
                                    </h3>
                                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-500">
                                        Welcome to Pong Pagong. Explore technical documentation, understand your
                                        application
                                        architecture, search your knowledge base, and work through development
                                        questions.
                                    </p>

                                    <div class="mt-5 flex flex-wrap gap-2">
                                        <span
                                            class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-600">Technical
                                            guidance</span>
                                        <span
                                            class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-600">Knowledge
                                            search</span>
                                        <span
                                            class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-600">Development
                                            support</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-6 sm:px-8 sm:py-8 lg:px-10">
                            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                                <div>
                                    <h4 class="text-sm font-extrabold text-slate-900">Start a conversation</h4>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Select a topic or type your own
                                        question below.</p>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-[.12em] text-slate-400">Quick
                                    prompts</span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <button type="button" class="suggestion-card"
                                    data-prompt="Explain the architecture and technologies used in the BioVerify AI project.">
                                    <div class="flex items-start gap-3">
                                        <span class="suggestion-icon bg-blue-50 text-blue-800">⌘</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-extrabold text-slate-800">Understand the
                                                System</span>
                                            <span class="mt-2 block text-xs leading-5 text-slate-500">Architecture,
                                                Laravel, Python, and integrations</span>
                                        </span>
                                        <span class="pt-1 text-slate-300">↗</span>
                                    </div>
                                </button>

                                <button type="button" class="suggestion-card"
                                    data-prompt="Explain how retrieval-augmented generation (RAG) works in my knowledge base, including document ingestion, retrieval, and answer generation.">
                                    <div class="flex items-start gap-3">
                                        <span class="suggestion-icon bg-amber-50 text-amber-800">▤</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-extrabold text-slate-800">Explore the
                                                Knowledge Base</span>
                                            <span class="mt-2 block text-xs leading-5 text-slate-500">Document search,
                                                RAG, and answer generation</span>
                                        </span>
                                        <span class="pt-1 text-slate-300">↗</span>
                                    </div>
                                </button>

                                <button type="button" class="suggestion-card"
                                    data-prompt="Explain the face and voice recognition components in BioVerify AI, their workflows, and important biometric security considerations.">
                                    <div class="flex items-start gap-3">
                                        <span class="suggestion-icon bg-violet-50 text-violet-800">◎</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-extrabold text-slate-800">Biometric
                                                Workflows</span>
                                            <span class="mt-2 block text-xs leading-5 text-slate-500">Face recognition,
                                                voice matching, and security</span>
                                        </span>
                                        <span class="pt-1 text-slate-300">↗</span>
                                    </div>
                                </button>

                                <button type="button" class="suggestion-card"
                                    data-prompt="Review the next development priorities for BioVerify AI, including reliability, access control, audit logging, testing, and deployment readiness.">
                                    <div class="flex items-start gap-3">
                                        <span class="suggestion-icon bg-emerald-50 text-emerald-800">✓</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-extrabold text-slate-800">Plan the Next
                                                Step</span>
                                            <span class="mt-2 block text-xs leading-5 text-slate-500">Reliability,
                                                security, and improvements</span>
                                        </span>
                                        <span class="pt-1 text-slate-300">↗</span>
                                    </div>
                                </button>
                            </div>

                            <div
                                class="mt-6 flex items-start gap-3 rounded-xl border border-amber-200/80 bg-amber-50/60 p-4">
                                <span
                                    class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-sm text-amber-800">i</span>
                                <div>
                                    <p class="text-xs font-extrabold text-slate-800">Responsible AI use</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-600">
                                        Verify important answers against authoritative references and applicable
                                        policies.
                                        Do not submit credentials, classified information, or unnecessary personal and
                                        biometric data.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="mt-5 text-center text-[10px] leading-5 text-slate-400">
                        AI-generated information may require independent verification.
                    </p>
                </div>
            </section>

            <!-- Composer -->
            <footer class="border-t border-slate-200 bg-white/95 px-4 py-4 sm:px-6 sm:py-5 lg:px-9">
                <div class="mx-auto max-w-[850px]">
                    <div id="errorBox"
                        class="mb-3 hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-700"
                        role="alert"></div>

                    <form id="chatForm" class="composer p-2 sm:p-3">
                        <label for="messageInput" class="sr-only">Pong Pagong a question</label>
                        <textarea id="messageInput" name="message" rows="2" maxlength="4000"
                            placeholder="Ask Pong Pagong anything..." class="max-h-40 min-h-[56px] px-3 py-3 sm:px-4" required></textarea>

                        <div class="mt-2 flex flex-wrap items-center justify-between gap-3 px-2 pb-1">
                            <p class="text-[10px] leading-5 text-slate-400 sm:text-[11px]">
                                <span class="font-semibold text-slate-500">Enter</span> to send
                                <span class="mx-1">·</span>
                                <span class="font-semibold text-slate-500">Shift + Enter</span> for a new line
                            </p>

                            <button id="sendButton" type="submit" class="send-button">
                                <span id="sendButtonText">Send message</span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path d="M22 2L11 13" />
                                    <path d="M22 2L15 22l-4-9-9-4 20-7z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <p class="mt-3 text-center text-[10px] leading-5 text-slate-400">
                        Pong Pagong · AI-assisted information retrieval · Verify important information independently
                    </p>
                </div>
            </footer>
        </main>
    </div>

    <script>
        (() => {
            'use strict';

            const sidebar = document.getElementById('pcgSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const desktopToggle = document.getElementById('sidebarToggle');
            const mobileToggle = document.getElementById('mobileMenuButton');
            const menuIcon = document.getElementById('menuIcon');

            const desktopMedia = window.matchMedia('(min-width: 768px)');
            const storageKey = 'askPongSidebarCollapsed';

            let mobileOpen = false;

            function isDesktop() {
                return desktopMedia.matches;
            }

            function applyDesktopState(collapsed) {
                sidebar.classList.toggle('sidebar-collapsed', collapsed);

                desktopToggle.setAttribute('aria-expanded', String(!collapsed));
                desktopToggle.setAttribute(
                    'aria-label',
                    collapsed ? 'Expand sidebar' : 'Collapse sidebar'
                );
                desktopToggle.setAttribute(
                    'title',
                    collapsed ? 'Expand sidebar' : 'Collapse sidebar'
                );

                try {
                    localStorage.setItem(storageKey, collapsed ? 'true' : 'false');
                } catch (error) {
                    // Sidebar still works when browser storage is unavailable.
                }
            }

            function toggleDesktopSidebar() {
                if (!isDesktop()) return;

                const collapsed = !sidebar.classList.contains('sidebar-collapsed');
                applyDesktopState(collapsed);
            }

            function openMobileSidebar() {
                if (isDesktop()) return;

                mobileOpen = true;
                sidebar.classList.add('sidebar-open');
                overlay.classList.add('overlay-visible');

                mobileToggle.setAttribute('aria-expanded', 'true');
                mobileToggle.setAttribute('aria-label', 'Close navigation');
                menuIcon.textContent = '✕';
                document.body.style.overflow = 'hidden';
            }

            function closeMobileSidebar() {
                mobileOpen = false;
                sidebar.classList.remove('sidebar-open');
                overlay.classList.remove('overlay-visible');

                mobileToggle.setAttribute('aria-expanded', 'false');
                mobileToggle.setAttribute('aria-label', 'Open navigation');
                menuIcon.textContent = '☰';
                document.body.style.overflow = '';
            }

            desktopToggle.addEventListener('click', toggleDesktopSidebar);

            mobileToggle.addEventListener('click', () => {
                if (mobileOpen) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            });

            overlay.addEventListener('click', closeMobileSidebar);

            sidebar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (!isDesktop()) closeMobileSidebar();
                });
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && mobileOpen) {
                    closeMobileSidebar();
                }
            });

            function handleBreakpointChange() {
                if (isDesktop()) {
                    closeMobileSidebar();
                    sidebar.classList.remove('sidebar-open');
                    overlay.classList.remove('overlay-visible');
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                }
            }

            if (desktopMedia.addEventListener) {
                desktopMedia.addEventListener('change', handleBreakpointChange);
            } else {
                desktopMedia.addListener(handleBreakpointChange);
            }

            // Restore the desktop collapsed preference.
            try {
                applyDesktopState(
                    isDesktop() && localStorage.getItem(storageKey) === 'true'
                );
            } catch (error) {
                applyDesktopState(false);
            }

            handleBreakpointChange();

            // =====================================================
            // Pong Pagong CHAT
            // =====================================================

            const form = document.getElementById('chatForm');
            const input = document.getElementById('messageInput');
            const messages = document.getElementById('chatMessages');
            const sendButton = document.getElementById('sendButton');
            const sendButtonText = document.getElementById('sendButtonText');
            const errorBox = document.getElementById('errorBox');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const sendUrl = @json(route('ai-chat.send'));

            let busy = false;

            function scrollToBottom() {
                messages.scrollTop = messages.scrollHeight;
            }

            function addMessage(role, text, sources = []) {
                document.getElementById('welcomePanel')?.remove();

                const isUser = role === 'user';

                const wrapper = document.createElement('div');
                wrapper.className = 'message-row';

                const avatar = document.createElement('div');
                avatar.className = 'message-avatar ' +
                    (isUser ? 'user-avatar' : 'assistant-avatar');
                avatar.textContent = isUser ? 'YOU' : 'P';
                avatar.setAttribute('aria-hidden', 'true');

                const content = document.createElement('div');
                content.className = 'message-bubble ' + (isUser ? 'user-bubble' : '');

                const label = document.createElement('p');
                label.className = 'mb-2 text-xs font-extrabold ' +
                    (isUser ? 'text-blue-100' : 'text-blue-800');
                label.textContent = isUser ? 'You' : 'Pong Pagong';

                const body = document.createElement('div');
                body.className = 'message-content';
                body.textContent = String(text ?? '');

                content.append(label, body);

                if (!isUser && Array.isArray(sources) && sources.length > 0) {
                    const sourceWrap = document.createElement('div');
                    sourceWrap.className = 'mt-4 border-t border-slate-100 pt-3';

                    const sourceTitle = document.createElement('p');
                    sourceTitle.className = 'mb-2 text-xs font-extrabold text-slate-500';
                    sourceTitle.textContent = 'Knowledge sources';

                    const sourceList = document.createElement('ul');
                    sourceList.className =
                        'list-inside list-disc space-y-1 text-xs leading-5 text-slate-500';

                    sources.forEach(source => {
                        const item = document.createElement('li');
                        item.textContent = String(source);
                        sourceList.appendChild(item);
                    });

                    sourceWrap.append(sourceTitle, sourceList);
                    content.appendChild(sourceWrap);
                }

                wrapper.append(avatar, content);
                messages.appendChild(wrapper);
                scrollToBottom();

                return wrapper;
            }

            function showError(message) {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            }

            async function sendMessage(message) {
                message = String(message ?? '').trim();

                if (busy || !message) return;

                busy = true;
                errorBox.textContent = '';
                errorBox.classList.add('hidden');

                sendButton.disabled = true;
                sendButtonText.textContent = 'Thinking...';

                document.querySelectorAll('[data-prompt]').forEach(button => {
                    button.disabled = true;
                });

                addMessage('user', message);

                const loadingMessage = addMessage('assistant', '');
                const loadingBody = loadingMessage.querySelector('.message-content');

                const loadingText = document.createElement('span');
                loadingText.textContent = 'Pong Pagong is thinking';

                const dot1 = document.createElement('span');
                dot1.className = 'typing-dot';
                dot1.textContent = ' ·';

                loadingBody.append(loadingText, dot1, dot1.cloneNode(true), dot1.cloneNode(true));

                input.value = '';

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
                        throw new Error('You have been signed out. Sign in again to use Pong.');
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
                    }
                } finally {
                    busy = false;
                    sendButton.disabled = false;
                    sendButtonText.textContent = 'Send message';

                    document.querySelectorAll('[data-prompt]').forEach(button => {
                        button.disabled = false;
                    });

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

            document.querySelectorAll('[data-prompt]').forEach(button => {
                button.addEventListener('click', () => {
                    if (busy) return;

                    input.value = button.dataset.prompt || '';
                    sendMessage(input.value);
                });
            });
        })();
    </script>
</body>

</html>
