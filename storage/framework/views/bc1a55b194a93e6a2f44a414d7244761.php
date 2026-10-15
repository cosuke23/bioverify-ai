<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>PDF Knowledge Base - BioVerify AI</title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        :root {
            --pcg-blue: #0033a0;
            --pcg-blue-dark: #00246f;
            --pcg-gold: #f5c542;
            --sidebar-width: 280px;
            --sidebar-collapsed: 82px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding-left: var(--sidebar-width);
            transition: padding-left .25s ease;
        }

        body.sidebar-collapsed {
            padding-left: var(--sidebar-collapsed);
        }

        /* SIDEBAR */

        .pcg-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            width: var(--sidebar-width);
            overflow-x: hidden;
            overflow-y: auto;
            background: linear-gradient(180deg, #0033a0 0%, #001b55 100%);
            color: white;
            transition: width .25s ease, transform .25s ease;
            box-shadow: 5px 0 25px rgb(15 23 42 / 10%);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 88px;
            padding: 18px;
            border-bottom: 1px solid rgb(255 255 255 / 12%);
        }

        .brand-mark {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border: 1px solid rgb(255 255 255 / 35%);
            border-radius: 14px;
            background: rgb(255 255 255 / 12%);
            color: var(--pcg-gold);
        }

        .brand-copy {
            min-width: 0;
            flex: 1;
        }

        .brand-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #cbdaf8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .13em;
            white-space: nowrap;
        }

        .sidebar-toggle {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 9px;
            background: rgb(255 255 255 / 10%);
            color: white;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background: rgb(255 255 255 / 20%);
        }

        .sidebar-section-label {
            padding: 24px 20px 10px;
            color: #a9c1f4;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .16em;
            white-space: nowrap;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 0 12px;
        }

        .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 46px;
            padding: 12px 14px;
            border: 1px solid transparent;
            border-radius: 12px;
            color: #e4edff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s, color .2s, border-color .2s;
        }

        .nav-link:hover {
            border-color: rgb(255 255 255 / 8%);
            background: rgb(255 255 255 / 10%);
            color: white;
        }

        .nav-link.active {
            border-color: rgb(255 255 255 / 12%);
            background: rgb(255 255 255 / 16%);
            color: white;
            font-weight: 800;
        }

        .nav-link.active::before {
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: -12px;
            width: 4px;
            border-radius: 0 5px 5px 0;
            background: var(--pcg-gold);
            content: "";
        }

        .nav-icon {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 23px;
            height: 23px;
        }

        .nav-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 12px;
        }

        .service-card {
            padding: 14px;
            border: 1px solid rgb(255 255 255 / 12%);
            border-radius: 14px;
            background: rgb(255 255 255 / 7%);
        }

        .service-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 7px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 10px rgb(74 222 128 / 35%);
        }

        .service-copy {
            margin-top: 8px;
            color: #cbdaf8;
            font-size: 11px;
            line-height: 1.6;
        }

        .sidebar-footer {
            padding: 15px 18px 18px;
            border-top: 1px solid rgb(255 255 255 / 12%);
            color: #b7c9ef;
            font-size: 10px;
            line-height: 1.7;
        }

        /* COLLAPSED SIDEBAR */

        body.sidebar-collapsed .pcg-sidebar {
            width: var(--sidebar-collapsed);
        }

        body.sidebar-collapsed .brand-copy,
        body.sidebar-collapsed .sidebar-section-label,
        body.sidebar-collapsed .nav-text,
        body.sidebar-collapsed .service-card,
        body.sidebar-collapsed .sidebar-footer {
            display: none;
        }

        body.sidebar-collapsed .sidebar-brand {
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 14px 8px;
        }

        body.sidebar-collapsed .sidebar-toggle {
            width: 38px;
        }

        body.sidebar-collapsed .sidebar-nav {
            padding: 0 10px;
        }

        body.sidebar-collapsed .nav-link {
            justify-content: center;
            padding: 12px 8px;
        }

        body.sidebar-collapsed .nav-link.active::before {
            left: -10px;
        }

        /* NAVBAR */

        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 900;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 72px;
            padding: 14px 28px;
            border-bottom: 1px solid #e2e8f0;
            background: rgb(255 255 255 / 96%);
            backdrop-filter: blur(12px);
        }

        .navbar-left {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 14px;
        }

        .mobile-menu-button {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            background: white;
            color: var(--pcg-blue);
            cursor: pointer;
        }

        .navbar-brand {
            color: var(--pcg-blue);
            font-size: 17px;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        .navbar-context {
            padding-left: 14px;
            border-left: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
        }

        .navbar-status {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
        }

        .navbar-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
        }

        .mobile-overlay {
            display: none;
        }

        /* MAIN CONTENT */

        .page-content {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 32px;
        }

        .page-heading {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 28px;
        }

        .page-heading-icon {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border-radius: 17px;
            background: #e8efff;
            color: var(--pcg-blue);
        }

        .page-eyebrow {
            color: var(--pcg-blue);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .page-title {
            margin-top: 5px;
            color: #0f172a;
            font-size: clamp(25px, 3vw, 34px);
            font-weight: 900;
            letter-spacing: -.04em;
        }

        .page-description {
            max-width: 720px;
            margin-top: 8px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        .content-card {
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: white;
            box-shadow: 0 4px 16px rgb(15 23 42 / 3%);
        }

        .card-heading {
            padding: 22px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-heading h2 {
            color: #0f172a;
            font-size: 18px;
            font-weight: 800;
        }

        .card-heading p {
            margin-top: 5px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .flash-message {
            margin-bottom: 20px;
            padding: 14px 17px;
            border: 1px solid;
            border-radius: 12px;
            font-size: 13px;
        }

        .flash-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .flash-info {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #1e40af;
        }

        .flash-error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        /* FLOATING ASK PONG AI */

        #pong-widget {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 2000;
            pointer-events: auto;
        }

        #pong-toggle {
            display: flex;
            align-items: center;
            gap: 11px;
            min-height: 58px;
            padding: 9px 17px 9px 10px;
            border: 1px solid rgb(255 255 255 / 25%);
            border-radius: 999px;
            background: linear-gradient(135deg, #0033a0, #0055d4);
            color: white;
            box-shadow: 0 12px 35px rgb(0 51 160 / 35%);
            cursor: pointer;
            pointer-events: auto;
            transition: transform .2s, box-shadow .2s;
        }

        #pong-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 38px rgb(0 51 160 / 42%);
        }

        .pong-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 39px;
            height: 39px;
            border: 1px solid rgb(255 255 255 / 30%);
            border-radius: 50%;
            background: rgb(255 255 255 / 13%);
            color: #f5d36c;
        }

        .pong-toggle-copy {
            text-align: left;
        }

        .pong-toggle-title {
            font-size: 13px;
            font-weight: 900;
        }

        .pong-toggle-subtitle {
            margin-top: 3px;
            color: #dbeafe;
            font-size: 10px;
        }

        #pong-panel {
            position: absolute;
            right: 0;
            bottom: 72px;
            display: none;
            flex-direction: column;
            width: min(390px, calc(100vw - 28px));
            height: min(560px, calc(100dvh - 120px));
            overflow: hidden;
            border: 1px solid #dbe4f0;
            border-radius: 20px;
            background: white;
            box-shadow: 0 24px 75px rgb(15 23 42 / 24%);
            pointer-events: auto;
        }

        #pong-panel.open {
            display: flex;
        }

        .pong-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 17px;
            background: linear-gradient(120deg, #0033a0, #064fc0);
            color: white;
        }

        .pong-header-identity {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .pong-header h3 {
            font-size: 14px;
            font-weight: 900;
        }

        .pong-header p {
            margin-top: 4px;
            color: #dbeafe;
            font-size: 10px;
        }

        #pong-close {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 9px;
            background: rgb(255 255 255 / 12%);
            color: white;
            cursor: pointer;
        }

        #pong-messages {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            padding: 16px;
            background: #f8fafc;
        }

        .pong-message {
            max-width: 88%;
            padding: 11px 13px;
            border-radius: 14px;
            font-size: 12px;
            line-height: 1.7;
            overflow-wrap: anywhere;
            white-space: pre-wrap;
        }

        .pong-message.assistant {
            align-self: flex-start;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 4px;
            background: white;
            color: #334155;
        }

        .pong-message.user {
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            background: #0033a0;
            color: white;
        }

        .pong-message.error {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .pong-footer {
            padding: 12px;
            border-top: 1px solid #e2e8f0;
            background: white;
        }

        #pong-status {
            min-height: 17px;
            margin: 0 3px 7px;
            color: #64748b;
            font-size: 10px;
        }

        .pong-input-row {
            display: flex;
            align-items: flex-end;
            gap: 7px;
        }

        #pong-input {
            width: 100%;
            min-width: 0;
            max-height: 110px;
            min-height: 42px;
            resize: vertical;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 11px;
            background: #f8fafc;
            color: #0f172a;
            font-size: 12px;
            outline: none;
        }

        #pong-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgb(37 99 235 / 10%);
        }

        .pong-action-button {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 12px;
            cursor: pointer;
            transition: background .2s, opacity .2s;
        }

        #pong-mic {
            background: #eff6ff;
            color: #0033a0;
        }

        #pong-mic:hover {
            background: #dbeafe;
        }

        #pong-mic.listening {
            background: #fee2e2;
            color: #b91c1c;
            animation: mic-pulse 1.2s infinite;
        }

        #pong-send {
            background: #0033a0;
            color: white;
        }

        #pong-send:hover {
            background: #00246f;
        }

        .pong-action-button:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .pong-mic-note {
            margin-top: 7px;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.5;
        }

        @keyframes mic-pulse {
            50% {
                box-shadow: 0 0 0 5px rgb(220 38 38 / 12%);
            }
        }

        @media (max-width: 767px) {

            body,
            body.sidebar-collapsed {
                padding-left: 0;
            }

            .pcg-sidebar,
            body.sidebar-collapsed .pcg-sidebar {
                width: 280px;
                transform: translateX(-105%);
            }

            body.sidebar-mobile-open .pcg-sidebar {
                transform: translateX(0);
            }

            body.sidebar-collapsed .brand-copy,
            body.sidebar-collapsed .sidebar-section-label,
            body.sidebar-collapsed .nav-text,
            body.sidebar-collapsed .service-card,
            body.sidebar-collapsed .sidebar-footer {
                display: initial;
            }

            body.sidebar-collapsed .sidebar-brand {
                flex-wrap: nowrap;
                justify-content: flex-start;
                padding: 18px;
            }

            body.sidebar-collapsed .sidebar-toggle {
                width: 32px;
            }

            body.sidebar-collapsed .sidebar-nav {
                padding: 0 12px;
            }

            body.sidebar-collapsed .nav-link {
                justify-content: flex-start;
                padding: 12px 14px;
            }

            .mobile-overlay {
                position: fixed;
                inset: 0;
                z-index: 950;
                background: rgb(15 23 42 / 45%);
            }

            body.sidebar-mobile-open .mobile-overlay {
                display: block;
            }

            .mobile-menu-button {
                display: flex;
            }

            .top-navbar {
                min-height: 66px;
                padding: 12px 16px;
            }

            .navbar-context,
            .navbar-status {
                display: none;
            }

            .page-content {
                padding: 24px 16px 110px;
            }

            .page-heading-icon {
                width: 48px;
                height: 48px;
            }

            #pong-widget {
                right: 14px;
                bottom: 14px;
            }

            #pong-panel {
                position: fixed;
                right: 14px;
                bottom: 84px;
                width: min(390px, calc(100vw - 28px));
                height: min(560px, calc(100dvh - 105px));
            }

            #pong-toggle {
                min-height: 52px;
                padding-right: 13px;
            }

            .pong-avatar {
                width: 35px;
                height: 35px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    <!-- PCG SIDEBAR -->
    <aside id="pcgSidebar" class="pcg-sidebar">

        <div class="sidebar-brand">
            <div class="brand-mark">
                <svg width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8">
                    <path d="M12 3 4.5 6v5.5c0 4.5 3.1 7.5 7.5 9.5 4.4-2 7.5-5 7.5-9.5V6L12 3Z" />
                    <path d="m8.5 12 2.2 2.2 4.8-5" />
                </svg>
            </div>

            <div class="brand-copy">
                <div class="brand-title">BIOVERIFY AI</div>
                <div class="brand-subtitle">PCG KNOWLEDGE SYSTEM</div>
            </div>

            <button type="button" id="sidebarToggle" class="sidebar-toggle" aria-label="Collapse sidebar"
                title="Collapse sidebar">
                <svg id="sidebarToggleIcon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </button>
        </div>

        <div class="sidebar-section-label">MAIN MENU</div>

        <nav class="sidebar-nav" aria-label="Main navigation">

            <a href="<?php echo e(route('ai-chat.index')); ?>" class="nav-link" title="Pong AI Assistant">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path
                            d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z" />
                    </svg>
                </span>
                <span class="nav-text">Pong AI Assistant</span>
            </a>

            <a href="<?php echo e(route('biometric.login')); ?>" class="nav-link" title="Biometric Login">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path d="M12 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path d="M6 21v-2a6 6 0 0 1 12 0v2" />
                        <path d="M3 8V5a2 2 0 0 1 2-2h3" />
                        <path d="M16 3h3a2 2 0 0 1 2 2v3" />
                        <path d="M21 16v3a2 2 0 0 1-2 2h-3" />
                        <path d="M8 21H5a2 2 0 0 1-2-2v-3" />
                    </svg>
                </span>
                <span class="nav-text">Biometric Login</span>
            </a>

            <a href="<?php echo e(route('face.recognition.index')); ?>" class="nav-link" title="Face Recognition">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path d="M3 8V5a2 2 0 0 1 2-2h3" />
                        <path d="M16 3h3a2 2 0 0 1 2 2v3" />
                        <path d="M21 16v3a2 2 0 0 1-2 2h-3" />
                        <path d="M8 21H5a2 2 0 0 1-2-2v-3" />
                        <circle cx="12" cy="10" r="3" />
                        <path d="M7 18a5 5 0 0 1 10 0" />
                    </svg>
                </span>
                <span class="nav-text">Face Recognition</span>
            </a>

            <a href="<?php echo e(route('people.index')); ?>" class="nav-link" title="People Directory">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="10" cy="7" r="4" />
                        <path d="M20 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </span>
                <span class="nav-text">People Directory</span>
            </a>

            <a href="<?php echo e(url('/pdf-documents')); ?>" class="nav-link active" aria-current="page" title="PDF Documents">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6" />
                        <path d="M8 13h8M8 17h8M8 9h2" />
                    </svg>
                </span>
                <span class="nav-text">PDF Documents</span>
            </a>

        </nav>

        <div class="sidebar-bottom">
            <div class="service-card">
                <div class="text-xs font-bold">
                    <span class="service-indicator"></span>
                    AI SERVICES
                </div>
                <div class="service-copy">
                    Face recognition, voice biometrics, and document intelligence.
                </div>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="font-bold text-white">BioVerify AI</div>
            <div>PCG Knowledge & Biometric System</div>
            <div>Local AI environment</div>
        </div>
    </aside>

    <div id="mobileOverlay" class="mobile-overlay"></div>

    <!-- TOP NAVBAR -->
    <nav class="top-navbar">
        <div class="navbar-left">
            <button type="button" id="mobileMenuToggle" class="mobile-menu-button"
                aria-label="Open navigation menu" aria-expanded="false">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="<?php echo e(url('/')); ?>" class="navbar-brand">
                BioVerify AI
            </a>

            <span class="navbar-context">PDF Knowledge Base</span>
        </div>

        <div class="navbar-status">
            <span class="navbar-status-dot"></span>
            Local AI Workspace
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="page-content">

        <header class="page-heading">
            <div class="page-heading-icon">
                <svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <path d="M14 2v6h6" />
                    <path d="M8 13h8M8 17h8" />
                </svg>
            </div>

            <div>
                <p class="page-eyebrow">AI Document Intelligence</p>
                <h1 class="page-title">PDF Knowledge Base</h1>
                <p class="page-description">
                    Upload a PDF to extract its content, generate an AI summary,
                    and ask questions about the document using your local AI services.
                </p>
            </div>
        </header>

        <?php if(session('success')): ?>
            <div class="flash-message flash-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('info')): ?>
            <div class="flash-message flash-info">
                <?php echo e(session('info')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="flash-message flash-error">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="flash-message flash-error">
                <p class="mb-2 font-bold">Please correct the following:</p>
                <ul class="list-inside list-disc">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- UPLOAD CARD -->
        <section class="content-card mb-8">
            <div class="card-heading">
                <h2>Upload a document</h2>
                <p>
                    Add a PDF to extract its content and make it searchable.
                    Maximum file size: 20 MB.
                </p>
            </div>

            <form action="<?php echo e(route('pdf-documents.store')); ?>" method="POST" enctype="multipart/form-data"
                class="space-y-5 p-6">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="title" class="mb-2 block text-sm font-bold text-slate-700">
                        Document title
                    </label>

                    <input id="title" name="title" type="text" value="<?php echo e(old('title')); ?>" required
                        maxlength="255" placeholder="e.g. Employee Handbook 2026"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="pdf_file" class="mb-2 block text-sm font-bold text-slate-700">
                        PDF file
                    </label>

                    <input id="pdf_file" name="pdf_file" type="file" accept=".pdf,application/pdf" required
                        class="block w-full rounded-xl border border-slate-300 bg-white p-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:font-bold file:text-blue-800 hover:file:bg-blue-100">

                    <?php $__errorArgs = ['pdf_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-6 py-3 font-bold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M12 16V4m-5 5 5-5 5 5" />
                        <path d="M4 16v4h16v-4" />
                    </svg>
                    Upload and analyze PDF
                </button>

                <p class="text-xs leading-5 text-slate-500">
                    Processing uses your local AI service. Keep the PDF API
                    on port 8003 and Ollama running while the document is analyzed.
                </p>
            </form>
        </section>

        <!-- DOCUMENT LIST -->
        <section class="content-card">
            <div class="card-heading">
                <h2>Uploaded documents</h2>
                <p>
                    Browse your PDFs and review their processing status.
                </p>
            </div>

            <?php if($documents->count()): ?>
                <div class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('pdf-documents.show', $document)); ?>"
                            class="flex flex-col gap-3 px-6 py-5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex min-w-0 items-start gap-4">
                                <div
                                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.8">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <path d="M14 2v6h6M8 13h8M8 17h8" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <h3 class="truncate font-bold text-slate-900">
                                        <?php echo e($document->title); ?>

                                    </h3>

                                    <p class="mt-1 truncate text-sm text-slate-500">
                                        <?php echo e($document->original_filename); ?>

                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Uploaded
                                        <?php echo e($document->created_at?->format('M d, Y h:i A') ?? 'Unknown date'); ?>


                                        <?php if($document->page_count): ?>
                                            · <?php echo e($document->page_count); ?> pages
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-shrink-0 items-center gap-3">
                                <span
                                    class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold
                                    <?php if($document->status === 'completed'): ?> bg-green-100 text-green-800
                                    <?php elseif($document->status === 'failed'): ?>
                                        bg-red-100 text-red-800
                                    <?php elseif($document->status === 'processing'): ?>
                                        bg-blue-100 text-blue-800
                                    <?php else: ?>
                                        bg-slate-100 text-slate-700 <?php endif; ?>">
                                    <?php echo e(ucfirst($document->status ?? 'pending')); ?>

                                </span>

                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" class="text-slate-400">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="border-t border-slate-200 px-6 py-4">
                    <?php echo e($documents->links()); ?>

                </div>
            <?php else: ?>
                <div class="px-6 py-14 text-center">
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                            <path d="M8 13h8M8 17h5" />
                        </svg>
                    </div>

                    <h3 class="mt-4 font-bold text-slate-800">
                        No documents yet
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        Upload your first PDF to start building your searchable knowledge base.
                    </p>
                </div>
            <?php endif; ?>
        </section>

        <footer class="py-8 text-center text-xs text-slate-400">
            BioVerify AI · Local Document Intelligence
        </footer>

    </main>

    <!-- FLOATING ASK PONG AI -->
    <div id="pong-widget">

        <button type="button" id="pong-toggle" aria-label="Open Ask Pong AI" aria-expanded="false"
            aria-controls="pong-panel">

            <span class="pong-avatar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8">
                    <path d="M12 3 14 8l5 2-5 2-2 5-2-5-5-2 5-2 2-5Z" />
                    <path d="m19 14 1.2 2.8L23 18l-2.8 1.2L19 22l-1.2-2.8L15 18l2.8-1.2L19 14Z" />
                </svg>
            </span>

            <span class="pong-toggle-copy">
                <span class="pong-toggle-title block">Ask Pong AI</span>
                <span class="pong-toggle-subtitle block">AI Knowledge Assistant</span>
            </span>
        </button>

        <section id="pong-panel" role="dialog" aria-label="Ask Pong AI Assistant" aria-hidden="true">

            <div class="pong-header">
                <div class="pong-header-identity">
                    <span class="pong-avatar">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <path d="M12 3 14 8l5 2-5 2-2 5-2-5-5-2 5-2 2-5Z" />
                        </svg>
                    </span>

                    <div>
                        <h3>Pong Pagong AI</h3>
                        <p>BioVerify AI Knowledge Assistant</p>
                    </div>
                </div>

                <button type="button" id="pong-close" aria-label="Close assistant">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="m18 6-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="pong-messages" aria-live="polite">
                <div class="pong-message assistant">
                    Hello! I'm Pong, your BioVerify AI knowledge assistant.
                    How can I help you today?
                </div>
            </div>

            <div class="pong-footer">
                <div id="pong-status" role="status">
                    Ready to assist
                </div>

                <form id="pong-form" class="pong-input-row">
                    <textarea id="pong-input" rows="1" placeholder="Ask Pong anything..." aria-label="Message Pong AI"></textarea>

                    <button type="button" id="pong-mic" class="pong-action-button" aria-label="Start voice input"
                        title="Speak your message">
                        <svg id="pong-mic-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8">
                            <rect x="9" y="2" width="6" height="12" rx="3" />
                            <path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3m-4 0h8" />
                        </svg>
                    </button>

                    <button type="submit" id="pong-send" class="pong-action-button" aria-label="Send message"
                        title="Send message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <path d="m22 2-7 20-4-9-9-4Z" />
                            <path d="M22 2 11 13" />
                        </svg>
                    </button>
                </form>

                <p class="pong-mic-note">
                    Click the microphone to dictate a message. Voice input
                    requires a supported browser and microphone permission.
                </p>
            </div>
        </section>
    </div>

    <!-- SIDEBAR + FLOATING CHAT JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /* SIDEBAR */
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarToggleIcon = document.getElementById('sidebarToggleIcon');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');

            function isMobile() {
                return window.matchMedia('(max-width: 767px)').matches;
            }

            function updateSidebarIcon() {
                if (!sidebarToggleIcon) return;

                const collapsed = document.body.classList.contains('sidebar-collapsed');

                sidebarToggleIcon.innerHTML = collapsed ?
                    '<path d="m9 18 6-6-6-6"/>' :
                    '<path d="m15 18-6-6 6-6"/>';
            }

            function closeMobileSidebar() {
                document.body.classList.remove('sidebar-mobile-open');

                if (mobileMenuToggle) {
                    mobileMenuToggle.setAttribute('aria-expanded', 'false');
                }
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (isMobile()) {
                        closeMobileSidebar();
                        return;
                    }

                    document.body.classList.toggle('sidebar-collapsed');
                    updateSidebarIcon();
                });
            }

            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    const isOpen = document.body.classList.toggle('sidebar-mobile-open');

                    mobileMenuToggle.setAttribute('aria-expanded', String(isOpen));
                });
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', closeMobileSidebar);
            }

            document.querySelectorAll('#pcgSidebar .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (isMobile()) closeMobileSidebar();
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeMobileSidebar();
                    closePong();
                }
            });

            window.addEventListener('resize', function() {
                if (!isMobile()) closeMobileSidebar();
            });

            updateSidebarIcon();

            /* ASK PONG AI */
            const toggle = document.getElementById('pong-toggle');
            const panel = document.getElementById('pong-panel');
            const closeButton = document.getElementById('pong-close');
            const form = document.getElementById('pong-form');
            const input = document.getElementById('pong-input');
            const messages = document.getElementById('pong-messages');
            const micButton = document.getElementById('pong-mic');
            const sendButton = document.getElementById('pong-send');
            const status = document.getElementById('pong-status');

            if (!toggle || !panel || !form || !input || !messages) return;

            let isSending = false;
            let recognition = null;
            let isListening = false;
            let manuallyStopped = false;
            let finalTranscript = '';

            function openPong() {
                panel.classList.add('open');
                panel.setAttribute('aria-hidden', 'false');
                toggle.setAttribute('aria-expanded', 'true');

                window.setTimeout(function() {
                    input.focus();
                }, 50);
            }

            function closePong() {
                if (!panel) return;

                panel.classList.remove('open');
                panel.setAttribute('aria-hidden', 'true');

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }

                stopListening();
            }

            toggle.addEventListener('click', function() {
                if (panel.classList.contains('open')) {
                    closePong();
                } else {
                    openPong();
                }
            });

            closeButton.addEventListener('click', closePong);

            function setStatus(message) {
                status.textContent = message;
            }

            function appendMessage(text, sender, isError = false) {
                const bubble = document.createElement('div');

                bubble.className = 'pong-message ' + sender +
                    (isError ? ' error' : '');

                // Use textContent so server responses cannot inject HTML.
                bubble.textContent = text;

                messages.appendChild(bubble);
                messages.scrollTop = messages.scrollHeight;

                return bubble;
            }

            function getCsrfToken() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            async function sendMessage(message) {
                const text = String(message || '').trim();

                if (!text || isSending) return;

                stopListening();

                appendMessage(text, 'user');
                input.value = '';
                input.style.height = 'auto';

                isSending = true;
                sendButton.disabled = true;
                setStatus('Pong is thinking...');

                const waitingBubble = appendMessage('Thinking...', 'assistant');

                try {
                    const response = await fetch(<?php echo json_encode(route('ai-chat.send'), 15, 512) ?>, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            message: text
                        })
                    });

                    const contentType = response.headers.get('content-type') || '';
                    let data = {};

                    if (contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        const responseText = await response.text();

                        throw new Error(
                            response.ok ?
                            'The AI endpoint returned an unexpected response. Check the Laravel chat controller.' :
                            'The request failed (' + response.status +
                            '). Check the Laravel route and logs.'
                        );
                    }

                    if (!response.ok) {
                        throw new Error(
                            data.message ||
                            data.error ||
                            'The request failed (' + response.status + ').'
                        );
                    }

                    waitingBubble.remove();

                    const reply =
                        data.reply ??
                        data.response ??
                        data.answer ??
                        data.message ??
                        data.data?.reply ??
                        data.data?.response ??
                        data.data?.answer;

                    if (typeof reply === 'string' && reply.trim()) {
                        appendMessage(reply, 'assistant');
                    } else {
                        appendMessage(
                            'The request completed, but no reply text was found. Check the response format returned by your AI chat controller.',
                            'assistant',
                            true
                        );
                    }

                    setStatus('Ready to assist');
                } catch (error) {
                    waitingBubble.remove();

                    appendMessage(
                        error.message || 'Unable to reach Pong AI. Please try again.',
                        'assistant',
                        true
                    );

                    setStatus('Unable to complete request');
                    console.error('Ask Pong AI error:', error);
                } finally {
                    isSending = false;
                    sendButton.disabled = false;
                    messages.scrollTop = messages.scrollHeight;
                }
            }

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                sendMessage(input.value);
            });

            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();

                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        sendMessage(input.value);
                    }
                }
            });

            input.addEventListener('input', function() {
                input.style.height = 'auto';
                input.style.height = Math.min(input.scrollHeight, 110) + 'px';
            });

            /* MICROPHONE / SPEECH-TO-TEXT */
            const SpeechRecognition =
                window.SpeechRecognition ||
                window.webkitSpeechRecognition;

            function stopListening() {
                manuallyStopped = true;

                if (recognition && isListening) {
                    try {
                        recognition.stop();
                    } catch (error) {
                        console.debug('Speech recognition already stopped.');
                    }
                }

                isListening = false;

                if (micButton) {
                    micButton.classList.remove('listening');
                    micButton.setAttribute('aria-label', 'Start voice input');
                    micButton.title = 'Speak your message';
                }
            }

            function startListening() {
                if (!SpeechRecognition) {
                    setStatus(
                        'Voice input is not supported in this browser. Try the latest Chrome or Edge.'
                    );
                    appendMessage(
                        'Your browser does not support speech recognition. You can still type your message.',
                        'assistant',
                        true
                    );
                    return;
                }

                if (!window.isSecureContext &&
                    location.hostname !== 'localhost' &&
                    location.hostname !== '127.0.0.1') {
                    setStatus('Microphone access requires HTTPS or localhost.');
                    return;
                }

                if (isListening) {
                    stopListening();
                    setStatus('Voice input stopped');
                    return;
                }

                try {
                    recognition = new SpeechRecognition();
                    recognition.lang = 'en-US';
                    recognition.continuous = true;
                    recognition.interimResults = true;
                    recognition.maxAlternatives = 1;

                    manuallyStopped = false;
                    finalTranscript = '';

                    recognition.onstart = function() {
                        isListening = true;
                        micButton.classList.add('listening');
                        micButton.setAttribute('aria-label', 'Stop voice input');
                        micButton.title = 'Stop recording';
                        setStatus('Listening... speak now');
                        input.focus();
                    };

                    recognition.onresult = function(event) {
                        let interimTranscript = '';

                        for (let i = event.resultIndex; i < event.results.length; i++) {
                            const transcript = event.results[i][0].transcript;

                            if (event.results[i].isFinal) {
                                finalTranscript += transcript + ' ';
                            } else {
                                interimTranscript += transcript;
                            }
                        }

                        input.value = (
                            finalTranscript + interimTranscript
                        ).trimStart();

                        input.style.height = 'auto';
                        input.style.height = Math.min(input.scrollHeight, 110) + 'px';
                    };

                    recognition.onerror = function(event) {
                        console.error('Speech recognition error:', event.error);

                        if (event.error === 'not-allowed' ||
                            event.error === 'service-not-allowed') {
                            setStatus('Microphone permission denied. Allow microphone access in your browser.');
                        } else if (event.error === 'no-speech') {
                            setStatus('No speech detected. Click the microphone to try again.');
                        } else if (event.error === 'audio-capture') {
                            setStatus('No microphone was detected. Check your audio device.');
                        } else if (event.error !== 'aborted') {
                            setStatus('Voice input error: ' + event.error);
                        }
                    };

                    recognition.onend = function() {
                        isListening = false;
                        micButton.classList.remove('listening');
                        micButton.setAttribute('aria-label', 'Start voice input');
                        micButton.title = 'Speak your message';

                        if (!manuallyStopped && !finalTranscript.trim()) {
                            setStatus('Voice input ended. Try again or type your message.');
                        } else if (finalTranscript.trim()) {
                            setStatus('Voice captured. Review your message, then press Send.');
                        }
                    };

                    recognition.start();
                } catch (error) {
                    console.error('Unable to start microphone:', error);
                    setStatus('Unable to start voice input. Check microphone permission.');
                    stopListening();
                }
            }

            micButton.addEventListener('click', function() {
                if (isListening) {
                    stopListening();
                    setStatus(
                        input.value.trim() ?
                        'Voice captured. Review your message, then press Send.' :
                        'Voice input stopped'
                    );
                } else {
                    startListening();
                }
            });
        });
    </script>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\bioverify-ai\resources\views/pdf-documents/index.blade.php ENDPATH**/ ?>