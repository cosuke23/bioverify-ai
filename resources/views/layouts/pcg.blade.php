<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BioVerify AI') · Philippine Coast Guard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    {{-- Tailwind stays available for page-level utilities; pcg.css loads after it so the design system wins. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/pcg.css') }}">

    @stack('head')
</head>

@php
    $nav = [
        ['section' => 'Operations'],
        ['route' => 'people.index', 'match' => ['people.*', 'recognized.person'], 'label' => 'Personnel Records',
         'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['route' => 'face.recognition.index', 'match' => ['face.recognition.*'], 'label' => 'Recognition Console',
         'icon' => '<path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><circle cx="12" cy="11" r="3"/><path d="M7.5 17.5a5 5 0 0 1 9 0"/>'],
        ['route' => 'biometric.login', 'match' => ['biometric.login'], 'label' => 'Verification Kiosk', 'external' => true,
         'icon' => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8"/><path d="M12 16v4"/><circle cx="12" cy="10" r="2.5"/>'],
        ['section' => 'Knowledge'],
        ['route' => 'pdf-documents.index', 'match' => ['pdf-documents.*'], 'label' => 'Documents',
         'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h5"/>'],
        ['route' => 'ai-chat.index', 'match' => ['ai-chat.*'], 'label' => 'Ask Pong AI',
         'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8"/><path d="M8 13h5"/>'],
    ];

    $admin = auth()->user();
    $initials = $admin
        ? collect(explode(' ', $admin->name))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')
        : 'PCG';
@endphp

<body class="pcg">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 btn btn-gold">Skip to content</a>

    <aside class="rail" id="pcg-rail" aria-label="Main navigation">
        <a href="{{ route('people.index') }}" class="rail-brand">
            <span class="rail-crest" aria-hidden="true">
                {{-- Generic shield-and-anchor mark (not the official PCG seal). --}}
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2.5 4 5.5v6c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10v-6z" />
                    <circle cx="12" cy="7.8" r="1.4" />
                    <path d="M12 9.2v8" />
                    <path d="M9.5 11.5h5" />
                    <path d="M8 14.6c.6 1.6 2.2 2.6 4 2.6s3.4-1 4-2.6" />
                </svg>
            </span>
            <span class="rail-text">
                <span class="rail-title block">BioVerify AI</span>
                <span class="rail-sub block">Philippine Coast Guard</span>
            </span>
        </a>

        <nav class="pb-2">
            @foreach ($nav as $item)
                @if (isset($item['section']))
                    <div class="rail-section">{{ $item['section'] }}</div>
                @else
                    @php $active = request()->routeIs(...$item['match']); @endphp
                    <div class="rail-nav">
                        <a href="{{ route($item['route']) }}" class="rail-link {{ $active ? 'is-active' : '' }}"
                            title="{{ $item['label'] }}" @if ($active) aria-current="page" @endif
                            @if (!empty($item['external'])) target="_blank" rel="noopener" @endif>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['icon'] !!}</svg>
                            <span class="rail-label">{{ $item['label'] }}</span>
                        </a>
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="rail-foot">
            <div class="rail-status" id="pcg-ai-status" role="status" aria-live="polite">
                <span class="pulse-dot" aria-hidden="true"></span>
                <span class="rail-text">
                    <span class="block font-semibold text-white">AI services</span>
                    <span class="block" data-status-text>Checking…</span>
                </span>
            </div>
        </div>
    </aside>

    <div class="rail-backdrop" data-rail-close></div>

    <div class="shell">
        <header class="topbar">
            <button type="button" class="icon-btn menu-btn" data-rail-open aria-label="Open navigation"
                aria-controls="pcg-rail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>

            <button type="button" class="icon-btn collapse-btn" data-rail-collapse aria-label="Collapse navigation">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6" /></svg>
            </button>

            <nav class="topbar-crumbs" aria-label="Breadcrumb">
                @hasSection('crumbs')
                    @yield('crumbs')
                @else
                    <strong>@yield('title', 'BioVerify AI')</strong>
                @endif
            </nav>

            <div class="topbar-actions">
                @stack('topbar-actions')

                @if ($admin)
                    <div class="user-chip">
                        <span class="avatar" aria-hidden="true">{{ strtoupper($initials) }}</span>
                        <span class="hidden sm:block pr-1 leading-tight">
                            <span class="block font-semibold text-slate-800">{{ $admin->name }}</span>
                            <span class="block text-xs text-slate-500">Administrator</span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="icon-btn" title="Sign out" aria-label="Sign out"
                                style="width:32px;height:32px;border-radius:999px">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <path d="M16 17l5-5-5-5" />
                                    <path d="M21 12H9" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </header>

        <main id="main" class="page">
            @foreach (['success' => 'alert-ok', 'error' => 'alert-danger', 'info' => 'alert-info', 'warning' => 'alert-warn'] as $key => $class)
                @if (session($key))
                    <div class="alert {{ $class }} mb-5 rise" role="{{ $key === 'error' ? 'alert' : 'status' }}">
                        {{ session($key) }}
                    </div>
                @endif
            @endforeach

            @yield('content')
        </main>
    </div>

    @auth
        @include('partials.pong-widget')
    @endauth

    <script>
        (function () {
            'use strict';

            const body = document.body;

            // Sidebar: collapse on desktop (remembered), drawer on mobile.
            try {
                if (localStorage.getItem('pcg-rail-collapsed') === '1') body.classList.add('rail-collapsed');
            } catch (e) {}

            document.querySelector('[data-rail-collapse]')?.addEventListener('click', () => {
                body.classList.toggle('rail-collapsed');
                try {
                    localStorage.setItem('pcg-rail-collapsed', body.classList.contains('rail-collapsed') ? '1' : '0');
                } catch (e) {}
            });

            document.querySelector('[data-rail-open]')?.addEventListener('click', () => body.classList.add('rail-open'));
            document.querySelector('[data-rail-close]')?.addEventListener('click', () => body.classList.remove('rail-open'));
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') body.classList.remove('rail-open');
            });

            // AI service status in the rail.
            const status = document.getElementById('pcg-ai-status');
            const text = status?.querySelector('[data-status-text]');
            const dot = status?.querySelector('.pulse-dot');

            fetch(@json(route('ai.health')), { headers: { Accept: 'application/json' } })
                .then(r => r.json().then(data => ({ ok: r.ok && data.success, data })))
                .then(({ ok }) => {
                    if (!text) return;
                    text.textContent = ok ? 'Face & voice online' : 'Recognition offline';
                    if (!ok && dot) dot.style.background = '#f87171';
                })
                .catch(() => {
                    if (text) text.textContent = 'Recognition offline';
                    if (dot) dot.style.background = '#f87171';
                });
        })();
    </script>

    @stack('scripts')
</body>

</html>
