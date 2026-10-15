@extends('layouts.pcg')

@section('title', 'Add face · ' . $person->full_name)

@php
    $initials = collect(explode(' ', (string) $person->full_name))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');

    $faceCount = $person->faces()->count();
    $captureLimits = config('services.bioverify_ai.face_capture', []);
@endphp

@section('crumbs')
    <a href="{{ route('people.index') }}">Personnel</a>
    <span aria-hidden="true">/</span>
    <a href="{{ route('people.show', $person) }}" class="truncate">{{ $person->full_name }}</a>
    <span aria-hidden="true">/</span>
    <strong>Add face</strong>
@endsection

@push('head')
    <style>
        /* Face enrolment ------------------------------------------------ */

        .enrol-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 22px;
            align-items: start;
        }

        @media (max-width: 1100px) {
            .enrol-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .side-stack {
            display: grid;
            gap: 18px;
        }

        .person-chip {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px 10px 10px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
        }

        .person-avatar {
            display: grid;
            place-items: center;
            flex: 0 0 46px;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--pcg-blue);
            box-shadow: inset 0 -3px 0 var(--pcg-gold);
            color: var(--pcg-gold);
            font-family: var(--font-display);
            font-size: 19px;
            font-weight: 700;
        }

        .person-name {
            font-weight: 700;
            line-height: 1.2;
            color: var(--ink);
        }

        /* Mode switch */
        .mode-switch {
            display: inline-flex;
            gap: 4px;
            padding: 4px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--canvas);
        }

        .mode-switch button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 36px;
            padding: 0 14px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: var(--ink-2);
            font: 600 13.5px/1 var(--font-body);
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .mode-switch button:hover {
            color: var(--pcg-blue);
        }

        .mode-switch button[aria-selected='true'] {
            background: var(--pcg-blue);
            box-shadow: inset 0 -2px 0 var(--pcg-gold);
            color: #fff;
        }

        .mode-switch svg {
            width: 17px;
            height: 17px;
        }

        /* Step tracker */
        .stepper {
            display: flex;
            gap: 10px;
            margin: 0;
            padding: 14px 22px;
            border-bottom: 1px solid var(--line-soft);
            list-style: none;
            overflow-x: auto;
        }

        .stepper li {
            display: flex;
            flex: 1 1 0;
            align-items: center;
            gap: 9px;
            min-width: 120px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .stepper li::after {
            content: '';
            flex: 1;
            height: 1px;
            min-width: 12px;
            background: var(--line);
        }

        .stepper li:last-child::after {
            display: none;
        }

        .stepper .n {
            display: grid;
            place-items: center;
            flex: 0 0 26px;
            width: 26px;
            height: 26px;
            border: 1px solid var(--line);
            border-radius: 50%;
            background: var(--surface);
            font-family: var(--font-mono);
            font-size: 12px;
            transition: background .2s, border-color .2s, color .2s;
        }

        .stepper li[data-status='active'] {
            color: var(--ink);
        }

        .stepper li[data-status='active'] .n {
            border-color: var(--pcg-gold);
            background: var(--pcg-gold-soft);
            color: var(--pcg-blue-900);
            box-shadow: 0 0 0 3px rgba(244, 197, 66, .25);
        }

        .stepper li[data-status='done'] {
            color: var(--ink-2);
        }

        .stepper li[data-status='done'] .n {
            border-color: var(--ok);
            background: var(--ok);
            color: #fff;
        }

        /* Camera stage (dark, kiosk language) */
        .stage {
            --stage-bg: #050b1a;
            --stage-line: rgba(255, 255, 255, .16);
            --stage-text: #e8eefc;
            --stage-dim: rgba(226, 236, 255, .7);
            --stage-ok: #34d399;
            --stage-warn: #fbbf24;
            --stage-bad: #f87171;

            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            border-radius: 14px;
            background: var(--stage-bg);
            color: var(--stage-text);
            isolation: isolate;
        }

        .stage video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .stage-overlay {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .oval-ring {
            fill: none;
            stroke: rgba(255, 255, 255, .7);
            stroke-width: 3;
            stroke-dasharray: 10 8;
            transition: stroke .3s;
        }

        .stage[data-state='aligning'] .oval-ring {
            stroke: var(--pcg-gold);
            stroke-dasharray: none;
        }

        .stage[data-state='ready'] .oval-ring,
        .stage[data-state='countdown'] .oval-ring {
            stroke: var(--stage-ok);
            stroke-dasharray: none;
            filter: drop-shadow(0 0 10px rgba(52, 211, 153, .8));
        }

        .stage[data-state='warn'] .oval-ring {
            stroke: var(--stage-warn);
            stroke-dasharray: none;
        }

        .bracket {
            fill: none;
            stroke: var(--pcg-gold);
            stroke-width: 4;
            stroke-linecap: round;
        }

        .scanline {
            position: absolute;
            left: 30%;
            right: 30%;
            top: 15%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--pcg-gold), transparent);
            box-shadow: 0 0 12px var(--pcg-gold);
            opacity: 0;
            pointer-events: none;
        }

        .stage[data-state='aligning'] .scanline,
        .stage[data-state='ready'] .scanline {
            opacity: .9;
            animation: enrol-scan 2.6s ease-in-out infinite;
        }

        @keyframes enrol-scan {

            0%,
            100% {
                top: 16%;
            }

            50% {
                top: 80%;
            }
        }

        /* Tracked face box (mirrored to match the preview) */
        .track-box {
            position: absolute;
            border: 2px solid var(--pcg-gold);
            border-radius: 12px;
            opacity: 0;
            pointer-events: none;
            transition: left .35s ease, top .35s ease, width .35s ease, height .35s ease, opacity .25s, border-color .25s;
        }

        .track-box.is-visible {
            opacity: .85;
        }

        .track-box.is-good {
            border-color: var(--stage-ok);
            box-shadow: 0 0 18px rgba(52, 211, 153, .45);
        }

        .track-box.is-warn {
            border-color: var(--stage-warn);
        }

        .hud-top {
            position: absolute;
            inset: 14px 14px auto 14px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            pointer-events: none;
        }

        .hud-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border: 1px solid var(--stage-line);
            border-radius: 999px;
            background: rgba(5, 11, 26, .72);
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .rec-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--stage-bad);
            animation: enrol-blink 1.2s ease-in-out infinite;
        }

        @keyframes enrol-blink {
            50% {
                opacity: .25;
            }
        }

        .guidance {
            position: absolute;
            left: 50%;
            bottom: 16px;
            max-width: calc(100% - 28px);
            padding: 11px 20px;
            overflow: hidden;
            border: 1px solid var(--stage-line);
            border-radius: 14px;
            background: rgba(5, 11, 26, .84);
            font-size: clamp(14px, 2vw, 18px);
            font-weight: 600;
            text-align: center;
            text-overflow: ellipsis;
            white-space: nowrap;
            transform: translateX(-50%);
            transition: border-color .2s, color .2s;
        }

        .guidance[data-tone='ok'] {
            border-color: rgba(52, 211, 153, .6);
            color: #bbf7d0;
        }

        .guidance[data-tone='warn'] {
            border-color: rgba(251, 191, 36, .6);
            color: #fde68a;
        }

        .countdown {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            pointer-events: none;
        }

        .countdown[hidden] {
            display: none;
        }

        .countdown-num {
            display: grid;
            place-items: center;
            width: 128px;
            height: 128px;
            border: 3px solid var(--pcg-gold);
            border-radius: 50%;
            background: rgba(5, 11, 26, .55);
            color: var(--pcg-gold);
            font-family: var(--font-display);
            font-size: 76px;
            font-weight: 700;
            line-height: 1;
            box-shadow: 0 0 40px rgba(244, 197, 66, .35);
            animation: enrol-tick 1s ease-out;
        }

        @keyframes enrol-tick {
            from {
                transform: scale(1.25);
                opacity: .2;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .flash {
            position: absolute;
            inset: 0;
            background: #fff;
            opacity: 0;
            pointer-events: none;
        }

        .flash.is-on {
            animation: enrol-flash .45s ease-out;
        }

        @keyframes enrol-flash {
            from {
                opacity: .85;
            }

            to {
                opacity: 0;
            }
        }

        .stage-idle {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            text-align: center;
            background:
                radial-gradient(circle at 50% 45%, rgba(0, 51, 160, .55), transparent 65%),
                var(--stage-bg);
        }

        .stage-idle[hidden] {
            display: none;
        }

        .idle-face {
            width: 112px;
            height: 112px;
            margin: 0 auto 14px;
            color: var(--pcg-gold);
            animation: enrol-breathe 3s ease-in-out infinite;
        }

        .stage-idle[data-tone='error'] .idle-face {
            color: var(--stage-bad);
            animation: none;
        }

        @keyframes enrol-breathe {
            50% {
                transform: scale(1.05);
                opacity: .85;
            }
        }

        .stage-eyebrow {
            margin: 0 0 6px;
            font-family: var(--font-display);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--pcg-gold);
        }

        .stage-idle p.idle-text {
            max-width: 400px;
            margin: 0 auto 18px;
            color: var(--stage-dim);
            font-size: 14px;
        }

        .stage-note {
            margin: 14px auto 0;
            max-width: 380px;
            color: rgba(226, 236, 255, .55);
            font-size: 12px;
        }

        /* Controls under the stage */
        .capture-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 14px;
        }

        .switch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink-2);
            cursor: pointer;
            user-select: none;
        }

        .switch input {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
        }

        .switch-track {
            position: relative;
            flex: 0 0 40px;
            width: 40px;
            height: 22px;
            border-radius: 999px;
            background: var(--line);
            transition: background .2s;
        }

        .switch-track::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            transition: transform .2s;
        }

        .switch input:checked+.switch-track {
            background: var(--pcg-blue);
        }

        .switch input:checked+.switch-track::after {
            transform: translateX(18px);
            background: var(--pcg-gold);
        }

        .switch input:focus-visible+.switch-track {
            outline: 3px solid var(--pcg-gold);
            outline-offset: 2px;
        }

        .streak {
            display: inline-flex;
            gap: 5px;
            margin-left: 4px;
        }

        .streak span {
            width: 18px;
            height: 6px;
            border-radius: 3px;
            background: var(--line);
            transition: background .2s;
        }

        .streak span.on {
            background: var(--ok);
        }

        .btn-capture {
            min-width: 168px;
        }

        .btn-capture.is-armed {
            box-shadow: 0 0 0 4px rgba(244, 197, 66, .3), 0 6px 18px rgba(217, 169, 28, .35);
        }

        /* Review */
        .review-frame {
            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            border-radius: 14px;
            background: #050b1a;
        }

        .review-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .review-stamp {
            position: absolute;
            left: 14px;
            top: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border: 1px solid rgba(52, 211, 153, .6);
            border-radius: 999px;
            background: rgba(5, 11, 26, .78);
            color: #bbf7d0;
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        /* Upload */
        .dropzone {
            position: relative;
            display: grid;
            place-items: center;
            gap: 6px;
            min-height: 240px;
            padding: 28px;
            border: 2px dashed var(--line);
            border-radius: 14px;
            background: var(--canvas);
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }

        .dropzone:hover,
        .dropzone.is-over {
            border-color: var(--pcg-blue);
            background: var(--info-soft);
        }

        .dropzone.is-invalid {
            border-color: var(--danger);
        }

        .dropzone:focus-within {
            outline: 3px solid var(--pcg-gold);
            outline-offset: 2px;
        }

        .dropzone input[type='file'] {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .dropzone-icon {
            display: grid;
            place-items: center;
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--surface);
            border: 1px solid var(--line);
            color: var(--pcg-blue);
        }

        .dropzone-icon svg {
            width: 26px;
            height: 26px;
        }

        .upload-preview {
            display: grid;
            grid-template-columns: 180px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        @media (max-width: 640px) {
            .upload-preview {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .upload-preview img {
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: var(--canvas);
        }

        /* Side: checks */
        .checks {
            display: grid;
            gap: 0;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 2px;
            border-bottom: 1px dashed var(--line);
            font-size: 14px;
            color: var(--ink-2);
        }

        .check:last-child {
            border-bottom: 0;
        }

        .check-state {
            display: inline-grid;
            place-items: center;
            flex: 0 0 22px;
            width: 22px;
            height: 22px;
            border: 1px solid var(--line);
            border-radius: 50%;
            color: transparent;
            font-size: 12px;
            font-weight: 700;
            transition: background .2s, border-color .2s, color .2s;
        }

        .check[data-ok='true'] .check-state {
            border-color: var(--ok);
            background: var(--ok);
            color: #fff;
        }

        .check[data-ok='false'] .check-state {
            border-color: var(--danger);
            background: var(--danger-soft);
            color: var(--danger);
        }

        .check[data-ok='false'] .check-state::before {
            content: '!';
        }

        .check[data-ok='true'] .check-state::before {
            content: '✓';
        }

        /* Side: telemetry */
        .telemetry {
            display: grid;
            gap: 12px;
            margin: 0;
        }

        .metric {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 4px 10px;
            align-items: baseline;
        }

        .metric dt {
            font-family: var(--font-display);
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .metric dd {
            margin: 0;
            font-family: var(--font-mono);
            font-size: 13px;
            color: var(--ink);
            text-align: right;
        }

        .metric .bar {
            grid-column: 1 / -1;
            position: relative;
            height: 6px;
            overflow: hidden;
            border-radius: 3px;
            background: var(--line-soft);
        }

        .metric .bar i {
            position: absolute;
            inset: 0 auto 0 0;
            width: 0;
            border-radius: 3px;
            background: var(--muted);
            transition: width .35s ease, background .2s;
        }

        .metric[data-ok='true'] .bar i {
            background: var(--ok);
        }

        .metric[data-ok='false'] .bar i {
            background: var(--danger);
        }

        .metric[data-ok='info'] .bar i {
            background: var(--pcg-blue);
        }

        .metric small {
            grid-column: 1 / -1;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--muted);
        }

        .telemetry-foot {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--line-soft);
            font-family: var(--font-mono);
            font-size: 11.5px;
            color: var(--muted);
        }

        /* Coverage pips */
        .pips {
            display: flex;
            gap: 6px;
        }

        .pips span {
            flex: 1;
            height: 8px;
            border-radius: 4px;
            background: var(--line-soft);
            border: 1px solid var(--line);
        }

        .pips span.on {
            background: var(--pcg-blue);
            border-color: var(--pcg-blue);
        }

        .pips span.target {
            border-style: dashed;
            border-color: var(--pcg-gold-600);
        }

        .tips {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .tips li {
            display: grid;
            grid-template-columns: 30px minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            font-size: 13.5px;
            color: var(--ink-2);
        }

        .tips strong {
            display: block;
            color: var(--ink);
            font-size: 14px;
        }

        .tip-icon {
            display: grid;
            place-items: center;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: var(--pcg-gold-soft);
            color: var(--pcg-blue);
        }

        .tip-icon svg {
            width: 16px;
            height: 16px;
        }

        .card-foot {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 22px;
            border-top: 1px solid var(--line-soft);
            background: #fbfcfe;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }

        .check-inline {
            display: inline-flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: var(--ink-2);
            cursor: pointer;
        }

        .check-inline input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            accent-color: var(--pcg-blue);
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: enrol-spin .7s linear infinite;
        }

        @keyframes enrol-spin {
            to {
                transform: rotate(360deg);
            }
        }

        [data-panel][hidden] {
            display: none !important;
        }

        @media (prefers-reduced-motion: reduce) {

            .scanline,
            .rec-dot,
            .idle-face,
            .countdown-num,
            .flash.is-on {
                animation: none !important;
            }
        }
    </style>
@endpush

@section('content')
    {{-- Page header --------------------------------------------------- --}}
    <header class="page-head rise">
        <div>
            <div class="eyebrow">Biometric enrolment · Face</div>
            <h1 class="page-title">Add face sample</h1>
            <p class="page-lede">
                Capture a clear, front-facing reference photo. Live guidance checks framing, light and focus
                before the shot is taken, and the AI service verifies the photo again when it is enrolled.
            </p>
        </div>

        <div class="person-chip">
            <span class="person-avatar" aria-hidden="true">{{ strtoupper($initials ?: 'PC') }}</span>
            <span class="min-w-0">
                <span class="person-name block truncate">{{ $person->full_name }}</span>
                <span class="mt-1 flex flex-wrap items-center gap-2">
                    <span class="tag" title="PCG personnel ID">{{ $person->person_id }}</span>
                    @if ($person->status)
                        <span class="badge {{ $person->status === 'Active' ? 'badge-ok' : 'badge-muted' }}">
                            {{ $person->status }}
                        </span>
                    @endif
                </span>
            </span>
        </div>
    </header>

    <div class="enrol-grid">
        {{-- Capture card --------------------------------------------- --}}
        <form method="POST" action="{{ route('people.faces.store', $person) }}" enctype="multipart/form-data"
            id="faceForm" class="card rise rise-2">
            @csrf

            <div class="card-head flex-wrap">
                <div>
                    <h2 class="card-title">Reference capture</h2>
                    <p class="muted text-sm m-0">Choose how to provide the photo.</p>
                </div>

                <div class="mode-switch" role="tablist" aria-label="Capture method">
                    <button type="button" role="tab" id="tabCamera" aria-controls="panelCamera"
                        aria-selected="true" data-mode="camera">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M23 7l-7 5 7 5V7z" />
                            <rect x="1" y="5" width="15" height="14" rx="2" />
                        </svg>
                        Live camera
                    </button>
                    <button type="button" role="tab" id="tabUpload" aria-controls="panelUpload"
                        aria-selected="false" data-mode="upload" tabindex="-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <path d="M17 8l-5-5-5 5" />
                            <path d="M12 3v12" />
                        </svg>
                        Upload photo
                    </button>
                </div>
            </div>

            <ol class="stepper" id="stepper" aria-label="Enrolment progress">
                <li data-step="1" data-status="active"><span class="n">1</span>Position</li>
                <li data-step="2" data-status="pending"><span class="n">2</span>Capture</li>
                <li data-step="3" data-status="pending"><span class="n">3</span>Review</li>
                <li data-step="4" data-status="pending"><span class="n">4</span>Enrol</li>
            </ol>

            <div class="card-pad">
                @if ($errors->any())
                    <div class="alert alert-danger mb-5" role="alert" id="serverErrors">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                            class="shrink-0 mt-0.5">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 8v4" />
                            <path d="M12 16h.01" />
                        </svg>
                        <div>
                            <p class="font-semibold m-0">The face sample was not enrolled.</p>
                            <ul class="m-0 mt-1 pl-4 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="alert alert-danger mb-5" role="alert" id="clientError" hidden></div>

                {{-- LIVE CAMERA ------------------------------------------ --}}
                <section id="panelCamera" data-panel="camera" role="tabpanel" aria-labelledby="tabCamera">
                    <div id="cameraView">
                        <div class="stage" id="stage" data-state="idle">
                            <video id="camera" autoplay muted playsinline></video>

                            <div class="scanline" aria-hidden="true"></div>
                            <div class="track-box" id="trackBox" aria-hidden="true"></div>

                            <svg class="stage-overlay" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice"
                                aria-hidden="true">
                                <defs>
                                    <mask id="enrolOvalMask">
                                        <rect width="400" height="300" fill="#fff" />
                                        <ellipse cx="200" cy="144" rx="82" ry="108" fill="#000" />
                                    </mask>
                                </defs>
                                <rect width="400" height="300" fill="rgba(3,8,20,.55)" mask="url(#enrolOvalMask)" />
                                <ellipse class="oval-ring" cx="200" cy="144" rx="82" ry="108" />
                                <path class="bracket" d="M108 50 v-14 h14" />
                                <path class="bracket" d="M292 50 v-14 h-14" />
                                <path class="bracket" d="M108 240 v14 h14" />
                                <path class="bracket" d="M292 240 v14 h-14" />
                            </svg>

                            <div class="hud-top" aria-hidden="true">
                                <span class="hud-chip"><span class="rec-dot"></span>Live</span>
                                <span class="hud-chip" id="hudState">Aligning</span>
                            </div>

                            <div class="countdown" id="countdown" hidden>
                                <div class="countdown-num" id="countdownNum">3</div>
                            </div>

                            <div class="flash" id="flash" aria-hidden="true"></div>

                            <div class="guidance" id="guidance" data-tone="action" role="status"
                                aria-live="polite">Starting camera…</div>

                            <div class="stage-idle" id="stageIdle" data-tone="idle">
                                <div>
                                    <svg class="idle-face" viewBox="0 0 120 120" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                        <path
                                            d="M16 36V22a6 6 0 0 1 6-6h14M84 16h14a6 6 0 0 1 6 6v14M104 84v14a6 6 0 0 1-6 6H84M36 104H22a6 6 0 0 1-6-6V84" />
                                        <ellipse cx="60" cy="56" rx="22" ry="27" />
                                        <path d="M35 98c4-12 14-18 25-18s21 6 25 18" />
                                    </svg>
                                    <p class="stage-eyebrow" id="idleTitle">Guided face capture</p>
                                    <p class="idle-text" id="idleText">
                                        Seat the person about an arm's length from the camera, facing a light source.
                                        Remove caps, sunglasses and face coverings.
                                    </p>
                                    <div class="flex flex-wrap justify-center gap-3">
                                        <button type="button" class="btn btn-gold" id="startCamera">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M23 7l-7 5 7 5V7z" />
                                                <rect x="1" y="5" width="15" height="14" rx="2" />
                                            </svg>
                                            <span id="startCameraLabel">Start camera</span>
                                        </button>
                                        <button type="button" class="btn btn-ghost" id="idleUpload" hidden>
                                            Upload a photo instead
                                        </button>
                                    </div>
                                    <p class="stage-note">
                                        Preview frames are analysed for quality only. Only the photo you confirm is
                                        enrolled.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="capture-bar">
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="switch" for="autoCapture">
                                    <input type="checkbox" id="autoCapture" checked>
                                    <span class="switch-track" aria-hidden="true"></span>
                                    Auto-capture when ready
                                </label>
                                <span class="flex items-center gap-1 text-xs muted" title="Consecutive good frames">
                                    Stable
                                    <span class="streak" id="streak" aria-hidden="true"><span></span><span></span></span>
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" class="btn btn-ghost btn-sm" id="stopCamera" hidden>
                                    Stop camera
                                </button>
                                <button type="button" class="btn btn-gold btn-capture" id="captureBtn" disabled
                                    aria-describedby="guidance">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path
                                            d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                        <circle cx="12" cy="13" r="4" />
                                    </svg>
                                    <span id="captureLabel">Capture photo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Review --}}
                    <div id="reviewView" hidden>
                        <div class="review-frame">
                            <img id="reviewImage" alt="Captured face photo for review">
                            <span class="review-stamp">Captured · review</span>
                        </div>

                        <div class="meta-row" id="reviewMeta" aria-label="Capture details"></div>

                        <p class="muted text-sm mt-3 mb-0">
                            Check that the eyes are open and in focus, the whole face is visible and nothing covers it.
                        </p>

                        <div class="capture-bar">
                            <button type="button" class="btn btn-ghost" id="retakeBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 4v6h6" />
                                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
                                </svg>
                                Retake
                            </button>
                            <button type="button" class="btn btn-primary" id="confirmBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>
                                <span id="confirmLabel">Use this photo</span>
                            </button>
                        </div>
                    </div>
                </section>

                {{-- UPLOAD ----------------------------------------------- --}}
                <section id="panelUpload" data-panel="upload" role="tabpanel" aria-labelledby="tabUpload" hidden>
                    <label class="dropzone {{ $errors->has('face_image') ? 'is-invalid' : '' }}" id="dropzone"
                        for="face_image">
                        <input type="file" name="face_image" id="face_image" required
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            @error('face_image') aria-invalid="true" @enderror aria-describedby="uploadHint">
                        <span class="dropzone-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <path d="M21 15l-5-5L5 21" />
                            </svg>
                        </span>
                        <span class="font-semibold text-slate-800">Drop a photo here or <span
                                class="text-[color:var(--pcg-blue)] underline">browse</span></span>
                        <span class="muted text-sm" id="uploadHint">JPG, PNG or WEBP · up to 5 MB · one face,
                            front-facing</span>
                    </label>

                    @error('face_image')
                        <p class="field-error mt-2">{{ $message }}</p>
                    @enderror
                    <p class="field-error mt-2" id="uploadError" hidden></p>

                    <div class="upload-preview mt-5" id="uploadPreview" hidden>
                        <img id="uploadImage" alt="Selected face photo preview">
                        <div>
                            <p class="font-semibold m-0 truncate" id="uploadName"></p>
                            <p class="mono muted m-0 mt-1" id="uploadMeta"></p>
                            <div class="alert alert-info mt-4" id="precheck" role="status" aria-live="polite">
                                Running quality pre-check…
                            </div>
                            <button type="button" class="btn btn-ghost btn-sm mt-3" id="uploadClear">
                                Choose a different photo
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <div class="card-foot">
                <label class="check-inline">
                    <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary'))>
                    <span>
                        <span class="font-semibold text-slate-800 block">Set as primary face sample</span>
                        <span class="muted text-xs">Shown on the personnel record and preferred for matching.</span>
                    </span>
                </label>

                <div class="flex flex-wrap items-center gap-3">
                    <span class="muted text-sm" id="footHint">Capture and confirm a photo to enrol it.</span>
                    <a href="{{ route('people.show', $person) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="uploadSubmit" hidden disabled>
                        <span id="uploadSubmitLabel">Enrol face sample</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Side column ------------------------------------------------ --}}
        <aside class="side-stack rise rise-3" aria-label="Capture quality">
            <section class="card">
                <div class="card-head">
                    <h2 class="card-title">Capture checks</h2>
                    <span class="badge badge-muted" id="overallBadge">Idle</span>
                </div>
                <div class="card-pad" style="padding-top:8px;padding-bottom:8px">
                    <ul class="checks" id="checks">
                        <li class="check" data-check="face"><span>Face detected</span><span class="check-state"></span></li>
                        <li class="check" data-check="single"><span>One face only</span><span class="check-state"></span></li>
                        <li class="check" data-check="centered"><span>Centred in the oval</span><span class="check-state"></span></li>
                        <li class="check" data-check="distance"><span>Distance</span><span class="check-state"></span></li>
                        <li class="check" data-check="lighting"><span>Lighting</span><span class="check-state"></span></li>
                        <li class="check" data-check="focus"><span>Image sharpness</span><span class="check-state"></span></li>
                        <li class="check" data-check="facing"><span>Facing the camera</span><span class="check-state"></span></li>
                    </ul>
                </div>
            </section>

            <section class="card">
                <div class="card-head">
                    <h2 class="card-title">Quality telemetry</h2>
                    <span class="tag" id="telemetrySource">—</span>
                </div>
                <div class="card-pad">
                    <dl class="telemetry">
                        <div class="metric" data-metric="detection">
                            <dt>Detection score</dt><dd>—</dd><span class="bar"><i></i></span>
                        </div>
                        <div class="metric" data-metric="brightness">
                            <dt>Brightness</dt><dd>—</dd><span class="bar"><i></i></span>
                            <small>target {{ $captureLimits['min_brightness'] ?? 90 }}–{{ $captureLimits['max_brightness'] ?? 225 }}</small>
                        </div>
                        <div class="metric" data-metric="sharpness">
                            <dt>Sharpness</dt><dd>—</dd><span class="bar"><i></i></span>
                            <small>min {{ $captureLimits['min_sharpness'] ?? 60 }}</small>
                        </div>
                        <div class="metric" data-metric="size">
                            <dt>Face size</dt><dd>—</dd><span class="bar"><i></i></span>
                            <small>target {{ round(($captureLimits['min_face_width'] ?? 0.18) * 100) }}–{{ round(($captureLimits['max_face_width'] ?? 0.6) * 100) }}% of frame width</small>
                        </div>
                        <div class="metric" data-metric="yaw">
                            <dt>Head yaw</dt><dd>—</dd><span class="bar"><i></i></span>
                            <small>|yaw| ≤ 0.15 for enrolment</small>
                        </div>
                        <div class="metric" data-metric="live">
                            <dt>Live score</dt><dd>—</dd><span class="bar"><i></i></span>
                        </div>
                    </dl>
                    <div class="telemetry-foot">
                        <span id="telemetryLatency">latency —</span>
                        <span id="telemetryFrames">frames 0</span>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-head">
                    <h2 class="card-title">Enrolment coverage</h2>
                    <span class="badge {{ $faceCount >= 3 ? 'badge-ok' : 'badge-warn' }}">{{ $faceCount }} on file</span>
                </div>
                <div class="card-pad">
                    <div class="pips" aria-hidden="true">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $faceCount ? 'on' : ($i >= 3 ? 'target' : '') }}"></span>
                        @endfor
                    </div>
                    <p class="muted text-sm mt-3 mb-0">
                        @if ($faceCount >= 5)
                            Coverage complete. Replace weaker samples rather than adding more.
                        @elseif ($faceCount >= 3)
                            Recommended minimum reached. Up to {{ 5 - $faceCount }} more varied samples improve robustness.
                        @else
                            Enrol {{ 3 - $faceCount }} more {{ 3 - $faceCount === 1 ? 'sample' : 'samples' }} to reach
                            the recommended 3–5.
                        @endif
                    </p>
                </div>
            </section>

            <section class="card">
                <div class="card-head">
                    <h2 class="card-title">Capture protocol</h2>
                </div>
                <div class="card-pad">
                    <ul class="tips">
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4" />
                                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
                                </svg></span>
                            <span><strong>Even, frontal lighting</strong>Face a window or lamp. Avoid strong backlight and
                                hard shadows.</span>
                        </li>
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M4.93 4.93l14.14 14.14" />
                                </svg></span>
                            <span><strong>Nothing covering the face</strong>No caps, hats, sunglasses or masks. Clear
                                glasses are fine if there is no glare.</span>
                        </li>
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 15h8" />
                                    <path d="M9 9h.01M15 9h.01" />
                                </svg></span>
                            <span><strong>Neutral expression</strong>Eyes open, mouth closed, looking straight into the
                                lens.</span>
                        </li>
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="2" y="7" width="14" height="14" rx="2" />
                                    <path d="M8 3h11a2 2 0 0 1 2 2v11" />
                                </svg></span>
                            <span><strong>Enrol 3–5 photos</strong>Vary them slightly in angle and lighting so the person is
                                recognised in different conditions.</span>
                        </li>
                    </ul>
                </div>
            </section>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';

            /* ---------------------------------------------------------------
             | Settings
             * --------------------------------------------------------------- */
            const ANALYZE_URL = @json(route('face.recognition.analyze'));
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const LIMITS = Object.assign({
                min_face_width: 0.18,
                max_face_width: 0.60,
                min_brightness: 90,
                max_brightness: 225,
                min_sharpness: 60,
            }, @json($captureLimits));

            const ANALYZE_INTERVAL_MS = 500;
            const ANALYZE_WIDTH = 640;
            const CAPTURE_MAX_WIDTH = 1280;
            const MAX_YAW = 0.15;
            const AUTO_CAPTURE_FRAMES = 2;
            const MANUAL_FALLBACK_MS = 10000;
            const REQUEST_TIMEOUT_MS = 8000;
            const MAX_UPLOAD_BYTES = 5 * 1024 * 1024;
            const ACCEPTED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
            // Kiosk liveness instructions are irrelevant to enrolment.
            const LIVENESS_CODES = ['look', 'turn', 'return', 'ready', 'verifying_liveness'];

            /* ---------------------------------------------------------------
             | Elements
             * --------------------------------------------------------------- */
            const $ = (id) => document.getElementById(id);

            const form = $('faceForm');
            const fileInput = $('face_image');
            const tabs = Array.from(document.querySelectorAll('[data-mode]'));
            const panelCamera = $('panelCamera');
            const panelUpload = $('panelUpload');
            const stepperItems = Array.from(document.querySelectorAll('#stepper li'));

            const stage = $('stage');
            const video = $('camera');
            const guidance = $('guidance');
            const hudState = $('hudState');
            const trackBox = $('trackBox');
            const countdownEl = $('countdown');
            const countdownNum = $('countdownNum');
            const flash = $('flash');
            const stageIdle = $('stageIdle');
            const idleTitle = $('idleTitle');
            const idleText = $('idleText');
            const startCameraBtn = $('startCamera');
            const startCameraLabel = $('startCameraLabel');
            const idleUploadBtn = $('idleUpload');
            const stopCameraBtn = $('stopCamera');
            const captureBtn = $('captureBtn');
            const captureLabel = $('captureLabel');
            const autoCapture = $('autoCapture');
            const streakBars = Array.from(document.querySelectorAll('#streak span'));

            const cameraView = $('cameraView');
            const reviewView = $('reviewView');
            const reviewImage = $('reviewImage');
            const reviewMeta = $('reviewMeta');
            const retakeBtn = $('retakeBtn');
            const confirmBtn = $('confirmBtn');
            const confirmLabel = $('confirmLabel');

            const dropzone = $('dropzone');
            const uploadError = $('uploadError');
            const uploadPreview = $('uploadPreview');
            const uploadImage = $('uploadImage');
            const uploadName = $('uploadName');
            const uploadMeta = $('uploadMeta');
            const precheck = $('precheck');
            const uploadClear = $('uploadClear');
            const uploadSubmit = $('uploadSubmit');
            const uploadSubmitLabel = $('uploadSubmitLabel');
            const footHint = $('footHint');

            const clientError = $('clientError');
            const overallBadge = $('overallBadge');
            const telemetrySource = $('telemetrySource');
            const telemetryLatency = $('telemetryLatency');
            const telemetryFrames = $('telemetryFrames');

            const frameCanvas = document.createElement('canvas');

            /* ---------------------------------------------------------------
             | State
             * --------------------------------------------------------------- */
            const state = {
                mode: 'camera',
                stream: null,
                running: false,
                reviewing: false,
                submitting: false,
                inFlight: false,
                loopTimer: null,
                countdownTimer: null,
                countdown: 0,
                goodStreak: 0,
                lastGood: false,
                lastData: null,
                failingSince: null,
                manualUnlocked: false,
                frames: 0,
                capturedBlob: null,
                capturedUrl: null,
                uploadUrl: null,
                precheckToken: 0,
            };

            /* ---------------------------------------------------------------
             | Small helpers
             * --------------------------------------------------------------- */
            function showError(message) {
                clientError.textContent = message;
                clientError.hidden = !message;
            }

            function setSteps(active, done) {
                stepperItems.forEach((li) => {
                    const step = Number(li.dataset.step);
                    li.dataset.status = step <= done ? 'done' : (step === active ? 'active' : 'pending');
                });
            }

            function setBadge(tone, text) {
                overallBadge.className = 'badge badge-' + tone;
                overallBadge.textContent = text;
            }

            function setGuidance(message, tone) {
                guidance.textContent = message;
                guidance.dataset.tone = tone || 'action';
            }

            function setStageState(value, label) {
                stage.dataset.state = value;
                if (label) hudState.textContent = label;
            }

            function formatBytes(bytes) {
                if (bytes >= 1024 * 1024) return (bytes / 1024 / 1024).toFixed(2) + ' MB';
                return Math.max(1, Math.round(bytes / 1024)) + ' KB';
            }

            function num(value) {
                return typeof value === 'number' && isFinite(value) ? value : null;
            }

            function canvasToBlob(canvas, quality) {
                return new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
            }

            /* ---------------------------------------------------------------
             | Checks + telemetry (shared by camera and upload pre-check)
             * --------------------------------------------------------------- */
            function resetReadout() {
                document.querySelectorAll('#checks .check').forEach((li) => delete li.dataset.ok);
                document.querySelectorAll('.metric').forEach((m) => {
                    delete m.dataset.ok;
                    m.querySelector('dd').textContent = '—';
                    m.querySelector('.bar i').style.width = '0';
                });
                telemetryLatency.textContent = 'latency —';
                telemetrySource.textContent = '—';
                setStreak(0);
            }

            function setMetric(name, text, fraction, ok) {
                const el = document.querySelector('.metric[data-metric="' + name + '"]');
                if (!el) return;
                el.querySelector('dd').textContent = text;
                el.querySelector('.bar i').style.width = Math.round(Math.max(0, Math.min(1, fraction || 0)) * 100) + '%';
                if (ok === null || ok === undefined) delete el.dataset.ok;
                else el.dataset.ok = String(ok);
            }

            function setStreak(count) {
                streakBars.forEach((bar, i) => bar.classList.toggle('on', i < count));
            }

            /**
             * Apply one analyze response to the checks and telemetry panels and
             * work out the enrolment verdict (quality + facing the camera).
             */
            function applyAnalysis(data) {
                const checks = data.checks || {};
                const face = (data.diagnostics && data.diagnostics.face) || null;
                const live = (data.diagnostics && data.diagnostics.liveness) || null;
                const hasFace = !!checks.face;

                const yaw = face ? num(face.yaw) : null;
                const yawOk = hasFace && (yaw === null ? !!data.quality_ok : Math.abs(yaw) <= MAX_YAW);

                document.querySelectorAll('#checks .check').forEach((li) => {
                    const key = li.dataset.check;
                    const ok = key === 'facing' ? yawOk : !!checks[key];
                    li.dataset.ok = String(ok);
                });

                // Telemetry ------------------------------------------------
                const det = face ? num(face.detection_score) : null;
                setMetric('detection', det === null ? '—' : (det * 100).toFixed(1) + '%', det, det === null ? null : 'info');

                const bright = face ? num(face.brightness) : null;
                setMetric('brightness', bright === null ? '—' : bright.toFixed(0), bright === null ? 0 : bright / 255,
                    bright === null ? null : (bright >= LIMITS.min_brightness && bright <= LIMITS.max_brightness));

                const sharp = face ? num(face.sharpness) : null;
                setMetric('sharpness', sharp === null ? '—' : sharp.toFixed(0),
                    sharp === null ? 0 : sharp / (LIMITS.min_sharpness * 4),
                    sharp === null ? null : sharp >= LIMITS.min_sharpness);

                const width = face ? num(face.width) : null;
                setMetric('size', width === null ? '—' : (width * 100).toFixed(0) + '%',
                    width === null ? 0 : width / Math.max(LIMITS.max_face_width, 0.01),
                    width === null ? null : (width >= LIMITS.min_face_width && width <= LIMITS.max_face_width));

                setMetric('yaw', yaw === null ? '—' : (yaw >= 0 ? '+' : '') + yaw.toFixed(2),
                    yaw === null ? 0 : Math.abs(yaw) / 0.5, yaw === null ? null : Math.abs(yaw) <= MAX_YAW);

                const liveScore = live ? num(live.live_score) : null;
                setMetric('live', liveScore === null ? '—' : (liveScore * 100).toFixed(1) + '%', liveScore,
                    liveScore === null ? null : (live.is_live === true ? 'info' : false));

                const good = !!data.quality_ok && yawOk;

                let message;
                let tone;

                if (!data.quality_ok) {
                    const g = data.guidance || {};
                    const usable = g.message && !LIVENESS_CODES.includes(g.code);
                    message = usable ? g.message : 'Adjust position inside the oval.';
                    tone = usable && g.tone ? g.tone : 'action';
                } else if (!yawOk) {
                    message = 'Face the camera directly.';
                    tone = 'action';
                } else {
                    message = 'Good capture conditions — hold still.';
                    tone = 'ok';
                }

                return { good, message, tone, hasFace, checks };
            }

            /* ---------------------------------------------------------------
             | Mode switching
             * --------------------------------------------------------------- */
            function setMode(mode) {
                if (state.submitting || mode === state.mode) return;
                state.mode = mode;

                tabs.forEach((tab) => {
                    const selected = tab.dataset.mode === mode;
                    tab.setAttribute('aria-selected', String(selected));
                    tab.tabIndex = selected ? 0 : -1;
                });

                panelCamera.hidden = mode !== 'camera';
                panelUpload.hidden = mode !== 'upload';
                uploadSubmit.hidden = mode !== 'upload';
                showError('');
                resetReadout();

                if (mode === 'upload') {
                    stopCamera();
                    discardCapture();
                    footHint.textContent = fileInput.files.length ? '' : 'Choose a photo to enrol.';
                    setBadge('muted', 'Upload');
                    setSteps(fileInput.files.length ? 4 : 2, fileInput.files.length ? 3 : 1);
                    if (fileInput.files.length) handleFile(fileInput.files[0], true);
                } else {
                    footHint.textContent = 'Capture and confirm a photo to enrol it.';
                    setBadge('muted', 'Idle');
                    setSteps(1, 0);
                    showIdle('idle');
                }
            }

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => setMode(tab.dataset.mode));
                tab.addEventListener('keydown', (e) => {
                    if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
                    e.preventDefault();
                    const next = tabs[(tabs.indexOf(tab) + 1) % tabs.length];
                    next.focus();
                    setMode(next.dataset.mode);
                });
            });

            /* ---------------------------------------------------------------
             | Camera lifecycle
             * --------------------------------------------------------------- */
            function cameraSupported() {
                return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
            }

            function showIdle(kind, title, text) {
                stageIdle.hidden = false;
                stageIdle.dataset.tone = kind === 'error' ? 'error' : 'idle';
                idleTitle.textContent = title || 'Guided face capture';
                idleText.textContent = text ||
                    "Seat the person about an arm's length from the camera, facing a light source. Remove caps, sunglasses and face coverings.";
                startCameraLabel.textContent = kind === 'error' ? 'Try again' : 'Start camera';
                startCameraBtn.hidden = kind === 'unsupported';
                idleUploadBtn.hidden = kind === 'idle';
                setStageState('idle', 'Standby');
                stopCameraBtn.hidden = true;
                updateCaptureButton();
            }

            async function startCamera() {
                showError('');

                if (!cameraSupported()) {
                    showIdle('unsupported', 'Camera not available',
                        'This browser cannot open the camera here (a secure HTTPS connection is required). Upload a photo instead.');
                    return;
                }

                startCameraBtn.disabled = true;
                startCameraLabel.textContent = 'Opening camera…';

                try {
                    state.stream = await navigator.mediaDevices.getUserMedia({
                        audio: false,
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 960 },
                        },
                    });

                    video.srcObject = state.stream;
                    await video.play().catch(() => {});

                    if (!video.videoWidth) {
                        await new Promise((resolve) => video.addEventListener('loadedmetadata', resolve, { once: true }));
                    }

                    stageIdle.hidden = true;
                    stopCameraBtn.hidden = false;
                    state.running = true;
                    state.goodStreak = 0;
                    state.failingSince = null;
                    state.manualUnlocked = false;
                    state.frames = 0;
                    telemetrySource.textContent = 'live · 2 fps';
                    setStageState('aligning', 'Aligning');
                    setGuidance('Position the face inside the oval.', 'action');
                    setBadge('info', 'Analysing');
                    setSteps(1, 0);
                    scheduleAnalyze(0);
                } catch (error) {
                    stopTracks();
                    const name = error && error.name;
                    if (name === 'NotAllowedError' || name === 'SecurityError') {
                        showIdle('error', 'Camera access blocked',
                            'Allow camera access from the browser address bar, then try again — or upload a photo instead.');
                    } else if (name === 'NotFoundError' || name === 'OverconstrainedError') {
                        showIdle('error', 'No camera found',
                            'Connect a webcam and try again, or upload a photo instead.');
                    } else if (name === 'NotReadableError' || name === 'AbortError') {
                        showIdle('error', 'Camera is busy',
                            'Another application is using the camera. Close it and try again, or upload a photo instead.');
                    } else {
                        showIdle('error', 'Camera could not start',
                            (error && error.message ? error.message + '. ' : '') + 'Try again or upload a photo instead.');
                    }
                    setBadge('danger', 'No camera');
                } finally {
                    startCameraBtn.disabled = false;
                }
            }

            function stopTracks() {
                if (state.stream) {
                    state.stream.getTracks().forEach((track) => track.stop());
                    state.stream = null;
                }
                video.srcObject = null;
            }

            function stopCamera() {
                state.running = false;
                clearTimeout(state.loopTimer);
                state.loopTimer = null;
                cancelCountdown();
                stopTracks();
                trackBox.classList.remove('is-visible');
                state.goodStreak = 0;
                state.lastGood = false;
                setStreak(0);
                if (state.mode === 'camera' && !state.reviewing && !state.submitting) {
                    showIdle('idle');
                    setBadge('muted', 'Idle');
                }
            }

            startCameraBtn.addEventListener('click', startCamera);
            stopCameraBtn.addEventListener('click', () => {
                stopCamera();
                resetReadout();
            });
            idleUploadBtn.addEventListener('click', () => setMode('upload'));

            /* ---------------------------------------------------------------
             | Live analysis loop (~2 frames per second)
             * --------------------------------------------------------------- */
            function scheduleAnalyze(delay) {
                clearTimeout(state.loopTimer);
                if (!state.running || state.reviewing || document.hidden) return;
                state.loopTimer = setTimeout(analyzeOnce, Math.max(0, delay));
            }

            async function grabFrame(maxWidth, quality) {
                const vw = video.videoWidth;
                const vh = video.videoHeight;
                if (!vw || !vh) return null;

                const scale = Math.min(1, maxWidth / vw);
                frameCanvas.width = Math.round(vw * scale);
                frameCanvas.height = Math.round(vh * scale);
                // Drawn unmirrored: the CSS mirror only affects the preview.
                frameCanvas.getContext('2d').drawImage(video, 0, 0, frameCanvas.width, frameCanvas.height);

                return canvasToBlob(frameCanvas, quality);
            }

            function postAnalyze(blob) {
                const body = new FormData();
                body.append('face_image', blob, 'frame.jpg');

                const controller = new AbortController();
                const timer = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS);

                return fetch(ANALYZE_URL, {
                    method: 'POST',
                    body,
                    credentials: 'same-origin',
                    signal: controller.signal,
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                }).finally(() => clearTimeout(timer));
            }

            async function analyzeOnce() {
                if (!state.running || state.reviewing || state.inFlight) return;

                const started = performance.now();
                let delay = ANALYZE_INTERVAL_MS;

                const blob = await grabFrame(ANALYZE_WIDTH, 0.8);
                if (!blob) {
                    scheduleAnalyze(200);
                    return;
                }

                state.inFlight = true;

                try {
                    const response = await postAnalyze(blob);

                    if (response.status === 419) {
                        stopCamera();
                        showIdle('error', 'Session expired', 'Reload the page to continue capturing.');
                        return;
                    }

                    if (response.status === 429) {
                        handleFailure('busy');
                        delay = 2000;
                    } else if (response.status === 422) {
                        delay = ANALYZE_INTERVAL_MS; // unreadable frame — just try the next one
                    } else {
                        const data = await response.json().catch(() => null);

                        if (!response.ok || !data || data.success !== true) {
                            handleFailure('unavailable');
                            delay = 1500;
                        } else if (state.running && !state.reviewing) {
                            renderLive(data, performance.now() - started);
                            delay = ANALYZE_INTERVAL_MS - (performance.now() - started);
                        }
                    }
                } catch (error) {
                    handleFailure('network');
                    delay = 1500;
                } finally {
                    state.inFlight = false;
                    scheduleAnalyze(delay);
                }
            }

            function handleFailure(kind) {
                state.goodStreak = 0;
                state.lastGood = false;
                setStreak(0);
                cancelCountdown();
                trackBox.classList.remove('is-visible');

                if (state.failingSince === null) state.failingSince = Date.now();
                const failingFor = Date.now() - state.failingSince;

                if (failingFor >= MANUAL_FALLBACK_MS) {
                    state.manualUnlocked = true;
                    setGuidance('Live analysis unavailable — manual capture enabled. The server will check the photo.', 'warn');
                    setBadge('warn', 'Manual mode');
                } else if (kind === 'busy') {
                    setGuidance('Analysis is busy — slowing down. Keep the position.', 'warn');
                    setBadge('warn', 'Rate limited');
                } else {
                    setGuidance('Quality analysis unavailable — retrying…', 'warn');
                    setBadge('warn', 'Retrying');
                }

                setStageState('warn', state.manualUnlocked ? 'Manual' : 'Offline');
                telemetryLatency.textContent = 'latency —';
                updateCaptureButton();
            }

            function renderLive(data, latency) {
                state.failingSince = null;
                state.manualUnlocked = false;
                state.lastData = data;
                state.frames++;

                const verdict = applyAnalysis(data);

                telemetryLatency.textContent = 'latency ' + Math.round(latency) + ' ms';
                telemetryFrames.textContent = 'frames ' + state.frames;

                setGuidance(verdict.message, verdict.tone);
                state.goodStreak = verdict.good ? state.goodStreak + 1 : 0;
                state.lastGood = verdict.good;
                setStreak(Math.min(state.goodStreak, AUTO_CAPTURE_FRAMES));

                if (state.countdown > 0) {
                    setStageState('countdown', 'Capturing');
                } else if (verdict.good) {
                    setStageState('ready', 'Ready');
                } else if (verdict.tone === 'warn') {
                    setStageState('warn', 'Adjust');
                } else {
                    setStageState('aligning', 'Aligning');
                }

                if (verdict.good) {
                    setBadge('ok', 'Ready');
                    setSteps(2, 1);
                } else {
                    setBadge(verdict.tone === 'warn' ? 'warn' : 'info', 'Adjusting');
                    setSteps(1, 0);
                }

                drawTrackBox(data.box, verdict);
                updateCaptureButton();

                if (!verdict.good && state.countdown > 0) {
                    cancelCountdown();
                    setGuidance('Movement detected — auto-capture paused. ' + verdict.message, verdict.tone === 'ok' ? 'action' : verdict.tone);
                } else if (verdict.good && autoCapture.checked && state.countdown === 0 &&
                    state.goodStreak >= AUTO_CAPTURE_FRAMES) {
                    startCountdown();
                }
            }

            function drawTrackBox(box, verdict) {
                const vw = video.videoWidth;
                const vh = video.videoHeight;

                if (!box || !vw || !vh) {
                    trackBox.classList.remove('is-visible');
                    return;
                }

                // Map the normalised (unmirrored) box onto the mirrored, object-fit: cover preview.
                const sw = stage.clientWidth;
                const sh = stage.clientHeight;
                const scale = Math.max(sw / vw, sh / vh);
                const dw = vw * scale;
                const dh = vh * scale;
                const ox = (sw - dw) / 2;
                const oy = (sh - dh) / 2;
                const mirroredX = 1 - (box.x + box.w);

                trackBox.style.left = (ox + mirroredX * dw) + 'px';
                trackBox.style.top = (oy + box.y * dh) + 'px';
                trackBox.style.width = (box.w * dw) + 'px';
                trackBox.style.height = (box.h * dh) + 'px';
                trackBox.classList.add('is-visible');
                trackBox.classList.toggle('is-good', verdict.good);
                trackBox.classList.toggle('is-warn', !verdict.good && verdict.tone === 'warn');
            }

            function updateCaptureButton() {
                const live = state.running && !state.reviewing && !state.submitting;
                const allowed = live && (state.lastGood || state.manualUnlocked);

                captureBtn.disabled = !allowed;
                captureBtn.classList.toggle('is-armed', allowed && state.lastGood);
                captureLabel.textContent = state.manualUnlocked && !state.lastGood ? 'Capture anyway' : 'Capture photo';
            }

            /* ---------------------------------------------------------------
             | Auto-capture countdown
             * --------------------------------------------------------------- */
            function startCountdown() {
                state.countdown = 3;
                countdownNum.textContent = '3';
                countdownEl.hidden = false;
                setStageState('countdown', 'Capturing');
                setGuidance('Hold still — capturing in 3…', 'ok');

                state.countdownTimer = setInterval(() => {
                    state.countdown--;

                    if (state.countdown <= 0) {
                        cancelCountdown();
                        capture();
                        return;
                    }

                    // Restart the tick animation for each number.
                    countdownNum.textContent = String(state.countdown);
                    countdownNum.style.animation = 'none';
                    void countdownNum.offsetWidth;
                    countdownNum.style.animation = '';
                    setGuidance('Hold still — capturing in ' + state.countdown + '…', 'ok');
                }, 1000);
            }

            function cancelCountdown() {
                clearInterval(state.countdownTimer);
                state.countdownTimer = null;
                state.countdown = 0;
                countdownEl.hidden = true;
            }

            autoCapture.addEventListener('change', () => {
                if (!autoCapture.checked && state.countdown > 0) {
                    cancelCountdown();
                    setGuidance('Auto-capture off — press Capture photo when ready.', 'action');
                }
            });

            /* ---------------------------------------------------------------
             | Capture + review
             * --------------------------------------------------------------- */
            async function capture() {
                if (!state.running || state.reviewing) return;
                cancelCountdown();

                let blob = await grabFrame(CAPTURE_MAX_WIDTH, 0.92);
                if (blob && blob.size > MAX_UPLOAD_BYTES) {
                    blob = await canvasToBlob(frameCanvas, 0.8);
                }

                if (!blob) {
                    setGuidance('The camera frame could not be captured. Try again.', 'warn');
                    return;
                }

                const width = frameCanvas.width;
                const height = frameCanvas.height;

                flash.classList.remove('is-on');
                void flash.offsetWidth;
                flash.classList.add('is-on');

                state.reviewing = true;
                clearTimeout(state.loopTimer);

                if (state.capturedUrl) URL.revokeObjectURL(state.capturedUrl);
                state.capturedBlob = blob;
                state.capturedUrl = URL.createObjectURL(blob);
                reviewImage.src = state.capturedUrl;

                renderReviewMeta(width, height, blob.size, state.lastGood ? state.lastData : null);

                cameraView.hidden = true;
                reviewView.hidden = false;
                setSteps(3, 2);
                setBadge('info', 'Review');
                footHint.textContent = 'Confirm the photo to enrol it.';
                confirmBtn.focus();
            }

            function renderReviewMeta(width, height, bytes, data) {
                const face = data && data.diagnostics ? data.diagnostics.face : null;
                const chips = [
                    width + ' × ' + height + ' px',
                    'JPEG · ' + formatBytes(bytes),
                ];

                if (face) {
                    if (num(face.detection_score) !== null) chips.push('detect ' + (face.detection_score * 100).toFixed(0) + '%');
                    if (num(face.brightness) !== null) chips.push('bright ' + face.brightness.toFixed(0));
                    if (num(face.sharpness) !== null) chips.push('sharp ' + face.sharpness.toFixed(0));
                    if (num(face.yaw) !== null) chips.push('yaw ' + face.yaw.toFixed(2));
                } else {
                    chips.push('unverified framing');
                }

                reviewMeta.replaceChildren(...chips.map((text) => {
                    const span = document.createElement('span');
                    span.className = 'tag';
                    span.textContent = text;
                    return span;
                }));
            }

            function discardCapture() {
                if (state.capturedUrl) URL.revokeObjectURL(state.capturedUrl);
                state.capturedUrl = null;
                state.capturedBlob = null;
                state.reviewing = false;
                reviewImage.removeAttribute('src');
                reviewView.hidden = true;
                cameraView.hidden = false;
            }

            function retake() {
                discardCapture();
                state.goodStreak = 0;
                state.lastGood = false;
                setStreak(0);
                footHint.textContent = 'Capture and confirm a photo to enrol it.';

                if (state.running && state.stream) {
                    setSteps(1, 0);
                    setGuidance('Position the face inside the oval.', 'action');
                    setStageState('aligning', 'Aligning');
                    setBadge('info', 'Analysing');
                    scheduleAnalyze(0);
                } else {
                    showIdle('idle');
                }
                updateCaptureButton();
            }

            function confirmCapture() {
                if (!state.capturedBlob) return;

                try {
                    const file = new File([state.capturedBlob], 'face-capture.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;

                    if (!fileInput.files || fileInput.files.length !== 1) {
                        throw new Error('File could not be attached');
                    }
                } catch (error) {
                    showError('This browser cannot attach the captured photo to the form. Use “Upload photo” instead.');
                    return;
                }

                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    beginSubmit();
                    form.submit();
                }
            }

            captureBtn.addEventListener('click', capture);
            retakeBtn.addEventListener('click', retake);
            confirmBtn.addEventListener('click', confirmCapture);

            /* ---------------------------------------------------------------
             | Upload mode
             * --------------------------------------------------------------- */
            function clearUpload() {
                state.precheckToken++;
                if (state.uploadUrl) URL.revokeObjectURL(state.uploadUrl);
                state.uploadUrl = null;
                fileInput.value = '';
                uploadPreview.hidden = true;
                uploadImage.removeAttribute('src');
                uploadSubmit.disabled = true;
                footHint.textContent = state.mode === 'upload' ? 'Choose a photo to enrol.' : footHint.textContent;
                resetReadout();
                if (state.mode === 'upload') setSteps(2, 1);
            }

            function handleFile(file, silent) {
                uploadError.hidden = true;
                dropzone.classList.remove('is-invalid');

                if (!file) {
                    clearUpload();
                    return;
                }

                const typeOk = ACCEPTED_TYPES.includes(file.type) || /\.(jpe?g|png|webp)$/i.test(file.name);

                if (!typeOk || file.size > MAX_UPLOAD_BYTES) {
                    clearUpload();
                    uploadError.textContent = !typeOk
                        ? 'Unsupported file type. Choose a JPG, PNG or WEBP image.'
                        : 'This photo is ' + formatBytes(file.size) + '. The maximum is 5 MB.';
                    uploadError.hidden = false;
                    dropzone.classList.add('is-invalid');
                    return;
                }

                if (state.uploadUrl) URL.revokeObjectURL(state.uploadUrl);
                state.uploadUrl = URL.createObjectURL(file);
                uploadImage.src = state.uploadUrl;
                uploadName.textContent = file.name;
                uploadMeta.textContent = (file.type || 'image') + ' · ' + formatBytes(file.size);
                uploadPreview.hidden = false;
                uploadSubmit.disabled = false;
                footHint.textContent = '';
                setSteps(4, 3);

                if (!silent || precheck.dataset.done !== 'true') runPrecheck(file);
            }

            async function runPrecheck(file) {
                const token = ++state.precheckToken;
                precheck.className = 'alert alert-info mt-4';
                precheck.textContent = 'Running quality pre-check…';
                precheck.dataset.done = 'false';
                telemetrySource.textContent = 'photo pre-check';
                setBadge('info', 'Checking');

                try {
                    const bitmap = await loadImage(file);
                    const scale = Math.min(1, ANALYZE_WIDTH / bitmap.width);
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(bitmap.width * scale);
                    canvas.height = Math.round(bitmap.height * scale);
                    canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

                    const blob = await canvasToBlob(canvas, 0.85);
                    const started = performance.now();
                    const response = await postAnalyze(blob);
                    const data = await response.json().catch(() => null);

                    if (token !== state.precheckToken) return;

                    if (!response.ok || !data || data.success !== true) {
                        throw new Error('analyze ' + response.status);
                    }

                    telemetryLatency.textContent = 'latency ' + Math.round(performance.now() - started) + ' ms';
                    const verdict = applyAnalysis(data);
                    const framingOnly = data.checks && data.checks.face && data.checks.single &&
                        data.checks.lighting && data.checks.focus;

                    if (verdict.good) {
                        precheck.className = 'alert alert-ok mt-4';
                        precheck.textContent = 'Pre-check passed: one clear, front-facing face.';
                        setBadge('ok', 'Looks good');
                    } else if (!verdict.hasFace || (data.checks && !data.checks.single)) {
                        precheck.className = 'alert alert-danger mt-4';
                        precheck.textContent = (verdict.hasFace ? 'More than one face was found.' : 'No face was found.') +
                            ' Enrolment requires exactly one face.';
                        setBadge('danger', 'Rejected');
                    } else if (framingOnly) {
                        precheck.className = 'alert alert-warn mt-4';
                        precheck.textContent = 'Framing or angle differs from the live guide (' + verdict.message.replace(/\.$/, '') +
                            '). Portrait photos are usually still accepted.';
                        setBadge('warn', 'Advisory');
                    } else {
                        precheck.className = 'alert alert-warn mt-4';
                        precheck.textContent = verdict.message + ' A clearer photo will match more reliably.';
                        setBadge('warn', 'Advisory');
                    }
                } catch (error) {
                    if (token !== state.precheckToken) return;
                    precheck.className = 'alert alert-info mt-4';
                    precheck.textContent = 'Quality pre-check unavailable. The photo will still be validated when it is enrolled.';
                    setBadge('muted', 'Unchecked');
                } finally {
                    if (token === state.precheckToken) precheck.dataset.done = 'true';
                }
            }

            function loadImage(file) {
                if (window.createImageBitmap) {
                    return createImageBitmap(file);
                }
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.onerror = reject;
                    img.src = URL.createObjectURL(file);
                });
            }

            fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));
            uploadClear.addEventListener('click', () => {
                clearUpload();
                fileInput.click();
            });

            ['dragenter', 'dragover'].forEach((type) => dropzone.addEventListener(type, (e) => {
                e.preventDefault();
                dropzone.classList.add('is-over');
            }));

            ['dragleave', 'dragend'].forEach((type) => dropzone.addEventListener(type, () => dropzone.classList.remove('is-over')));

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('is-over');
                const files = e.dataTransfer && e.dataTransfer.files;
                if (!files || !files.length) return;

                try {
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    fileInput.files = dt.files;
                } catch (error) {
                    uploadError.textContent = 'Drag and drop is not supported here. Click to browse instead.';
                    uploadError.hidden = false;
                    return;
                }
                handleFile(fileInput.files[0]);
            });

            /* ---------------------------------------------------------------
             | Submission
             * --------------------------------------------------------------- */
            function beginSubmit() {
                state.submitting = true;
                stopCamera();
                setSteps(4, 3);
                setBadge('info', 'Enrolling');

                [confirmBtn, retakeBtn, uploadSubmit, captureBtn, uploadClear].forEach((b) => b.disabled = true);
                tabs.forEach((t) => t.disabled = true);

                const busy = '<span class="spinner" aria-hidden="true"></span> Enrolling…';
                confirmLabel.innerHTML = busy;
                uploadSubmitLabel.innerHTML = busy;
            }

            form.addEventListener('submit', (event) => {
                if (state.submitting) {
                    event.preventDefault();
                    return;
                }

                if (!fileInput.files || fileInput.files.length !== 1) {
                    event.preventDefault();
                    showError(state.mode === 'camera'
                        ? 'Capture and confirm a photo first.'
                        : 'Choose a photo to enrol.');
                    return;
                }

                const file = fileInput.files[0];
                if (file.size > MAX_UPLOAD_BYTES) {
                    event.preventDefault();
                    showError('The photo is larger than 5 MB.');
                    return;
                }

                beginSubmit();
            });

            /* ---------------------------------------------------------------
             | Page lifecycle
             * --------------------------------------------------------------- */
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    clearTimeout(state.loopTimer);
                    cancelCountdown();
                } else if (state.running && !state.reviewing) {
                    scheduleAnalyze(0);
                }
            });

            window.addEventListener('pagehide', stopTracks);
            window.addEventListener('beforeunload', stopTracks);

            // Restore the back-forward cache cleanly (camera tracks were stopped on pagehide).
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    state.submitting = false;
                    state.running = false;
                    window.location.reload();
                }
            });

            /* ---------------------------------------------------------------
             | Init
             * --------------------------------------------------------------- */
            if (!cameraSupported()) {
                setMode('upload');
                showError('Live camera capture is unavailable in this browser or on an insecure connection. Upload a photo instead.');
            } else {
                showIdle('idle');
                setSteps(1, 0);
            }
        })();
    </script>
@endpush
