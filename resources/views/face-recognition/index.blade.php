@extends('layouts.pcg')

@section('title', 'Recognition Console')

@section('crumbs')
    <span>Operations</span>
    <span aria-hidden="true">/</span>
    <strong>Recognition Console</strong>
@endsection

@push('head')
    <style>
        /* Recognition Console — page-scoped tokens (dark capture stage). */
        .rc {
            --rc-stage: #050b1a;
            --rc-stage-2: #0a1633;
            --rc-veil: rgba(3, 8, 20, .55);
            --rc-hud: rgba(5, 11, 26, .78);
            --rc-hud-line: rgba(255, 255, 255, .16);
            --rc-text: #e6edfb;
            --rc-dim: rgba(226, 236, 255, .66);
            --rc-live-ok: #34d399;
            --rc-live-warn: #fbbf24;
            --rc-live-bad: #f87171;
            --rc-ring-idle: rgba(255, 255, 255, .7);
            --rc-track: var(--line-soft);
        }

        /* .btn / utility display rules would otherwise defeat the hidden attribute. */
        .rc [hidden] {
            display: none !important;
        }

        .rc-grid {
            display: grid;
            gap: 20px;
            align-items: start;
        }

        @media (min-width: 1100px) {
            .rc-grid {
                grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
            }

            .rc-sticky {
                position: sticky;
                top: 84px;
            }
        }

        .rc-sub {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }

        @media (min-width: 760px) {
            .rc-sub {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Stage --------------------------------------------------- */
        .rc-stage-wrap {
            padding: 10px;
            background: var(--rc-stage);
        }

        .rc-stage {
            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            border-radius: 12px;
            background: var(--rc-stage);
            color: var(--rc-text);
        }

        .rc-stage video,
        .rc-stage .rc-box {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .rc-stage video {
            object-fit: cover;
            transform: scaleX(-1);
        }

        .rc-overlay {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .rc-veil {
            fill: var(--rc-veil);
        }

        .rc-ring {
            fill: none;
            stroke: var(--rc-ring-idle);
            stroke-width: 3;
            stroke-dasharray: 10 8;
            transition: stroke .3s;
        }

        .rc-stage[data-state='aligning'] .rc-ring {
            stroke: var(--pcg-gold);
            stroke-dasharray: none;
        }

        .rc-stage[data-state='ready'] .rc-ring,
        .rc-stage[data-state='scanning'] .rc-ring,
        .rc-stage[data-state='matched'] .rc-ring {
            stroke: var(--rc-live-ok);
            stroke-dasharray: none;
            filter: drop-shadow(0 0 10px var(--rc-live-ok));
        }

        .rc-stage[data-state='warn'] .rc-ring {
            stroke: var(--rc-live-warn);
            stroke-dasharray: none;
        }

        .rc-stage[data-state='nomatch'] .rc-ring {
            stroke: var(--rc-live-bad);
            stroke-dasharray: none;
        }

        .rc-bracket {
            fill: none;
            stroke: var(--pcg-gold);
            stroke-width: 4;
            stroke-linecap: round;
        }

        .rc-sweep {
            position: absolute;
            left: 50%;
            top: 48%;
            width: 70%;
            aspect-ratio: 1;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            background: conic-gradient(from 0deg, transparent 0deg, rgba(52, 211, 153, .32) 40deg, transparent 80deg);
            opacity: 0;
            pointer-events: none;
            mix-blend-mode: screen;
        }

        .rc-stage[data-state='scanning'] .rc-sweep {
            opacity: 1;
            animation: rc-sweep 1.4s linear infinite;
        }

        @keyframes rc-sweep {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .rc-scanline {
            position: absolute;
            left: 30%;
            right: 30%;
            top: 16%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--pcg-gold), transparent);
            box-shadow: 0 0 12px var(--pcg-gold);
            opacity: 0;
            pointer-events: none;
        }

        .rc-stage[data-state='aligning'] .rc-scanline,
        .rc-stage[data-state='ready'] .rc-scanline {
            opacity: .9;
            animation: rc-scan 2.6s ease-in-out infinite;
        }

        @keyframes rc-scan {

            0%,
            100% {
                top: 16%;
            }

            50% {
                top: 80%;
            }
        }

        .rc-hud {
            position: absolute;
            inset: 12px 12px auto 12px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
            pointer-events: none;
        }

        .rc-hud-group {
            display: flex;
            gap: 8px;
        }

        .rc-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 11px;
            border: 1px solid var(--rc-hud-line);
            border-radius: 999px;
            background: var(--rc-hud);
            color: var(--rc-text);
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .rc-rec {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--rc-dim);
        }

        .rc-stage[data-camera='on'] .rc-rec {
            background: var(--rc-live-bad);
            animation: rc-blink 1.2s ease-in-out infinite;
        }

        @keyframes rc-blink {
            50% {
                opacity: .25;
            }
        }

        .rc-guidance {
            position: absolute;
            left: 50%;
            bottom: 16px;
            max-width: calc(100% - 32px);
            padding: 10px 20px;
            overflow: hidden;
            border: 1px solid var(--rc-hud-line);
            border-radius: 12px;
            background: var(--rc-hud);
            color: var(--rc-text);
            font-size: clamp(14px, 1.8vw, 17px);
            font-weight: 600;
            text-align: center;
            text-overflow: ellipsis;
            white-space: nowrap;
            transform: translateX(-50%);
            transition: border-color .2s, color .2s;
        }

        .rc-guidance[data-tone='ok'] {
            border-color: var(--rc-live-ok);
            color: var(--rc-live-ok);
        }

        .rc-guidance[data-tone='warn'] {
            border-color: var(--rc-live-warn);
            color: var(--rc-live-warn);
        }

        .rc-guidance[data-tone='bad'] {
            border-color: var(--rc-live-bad);
            color: var(--rc-live-bad);
        }

        .rc-cover {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            text-align: center;
            background: radial-gradient(circle at 50% 45%, rgba(0, 51, 160, .55), transparent 65%), var(--rc-stage);
            color: var(--rc-text);
        }

        .rc-cover[hidden] {
            display: none;
        }

        .rc-cover h3 {
            margin: 0;
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 700;
        }

        .rc-cover p {
            max-width: 420px;
            margin: 8px auto 18px;
            color: var(--rc-dim);
            font-size: 14px;
        }

        .rc-cover-icon {
            width: 96px;
            height: 96px;
            margin: 0 auto 14px;
            color: var(--pcg-gold);
        }

        .rc-cover[data-tone='error'] .rc-cover-icon {
            color: var(--rc-live-bad);
        }

        .rc-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 22px;
            border-top: 1px solid var(--line-soft);
        }

        .rc-toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .rc-switch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-2);
            cursor: pointer;
            user-select: none;
        }

        .rc-switch input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .rc-switch-track {
            position: relative;
            width: 38px;
            height: 22px;
            border-radius: 999px;
            background: var(--line);
            transition: background .15s;
        }

        .rc-switch-track::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            transition: transform .15s;
        }

        .rc-switch input:checked+.rc-switch-track {
            background: var(--pcg-blue);
        }

        .rc-switch input:checked+.rc-switch-track::after {
            transform: translateX(16px);
        }

        .rc-switch input:focus-visible+.rc-switch-track {
            outline: 3px solid var(--pcg-gold);
            outline-offset: 2px;
        }

        /* Checks ---------------------------------------------------- */
        .rc-checks {
            display: grid;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .rc-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px dashed var(--line);
            font-size: 14px;
            color: var(--ink-2);
        }

        .rc-check:last-child {
            border-bottom: 0;
        }

        .rc-check-state {
            display: inline-grid;
            place-items: center;
            width: 22px;
            height: 22px;
            border: 1px solid var(--line);
            border-radius: 50%;
            color: transparent;
            font-size: 12px;
            font-weight: 700;
            transition: background .2s, border-color .2s, color .2s;
        }

        .rc-check[data-ok='true'] .rc-check-state {
            border-color: var(--ok);
            background: var(--ok);
            color: var(--surface);
        }

        .rc-check[data-ok='false'] .rc-check-state {
            border-color: var(--danger);
            color: var(--danger);
        }

        /* Upload ---------------------------------------------------- */
        .rc-drop {
            position: relative;
            display: grid;
            place-items: center;
            gap: 6px;
            min-height: 150px;
            padding: 18px;
            border: 1.5px dashed var(--line);
            border-radius: var(--radius);
            background: var(--canvas);
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }

        .rc-drop:hover,
        .rc-drop.is-over {
            border-color: var(--pcg-blue);
            background: var(--info-soft);
        }

        .rc-drop input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .rc-drop img {
            max-height: 150px;
            border-radius: var(--radius-sm);
            object-fit: contain;
        }

        /* Analysis -------------------------------------------------- */
        .rc-verdict {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .rc-verdict-name {
            margin: 4px 0 0;
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 700;
            line-height: 1.05;
            overflow-wrap: anywhere;
        }

        .rc-section {
            padding-top: 16px;
            margin-top: 16px;
            border-top: 1px solid var(--line-soft);
        }

        .rc-metric-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .rc-metric-label {
            font-family: var(--font-display);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .rc-metric-value {
            font-family: var(--font-mono);
            font-size: 20px;
            font-weight: 500;
            color: var(--ink);
        }

        .rc-gauge {
            position: relative;
            height: 12px;
            border-radius: 999px;
            background: var(--rc-track);
        }

        .rc-gauge-fill {
            position: absolute;
            inset: 0 auto 0 0;
            width: 0;
            border-radius: 999px;
            background: var(--muted);
            transition: width .5s ease, background .2s;
        }

        .rc-gauge[data-tone='ok'] .rc-gauge-fill {
            background: var(--ok);
        }

        .rc-gauge[data-tone='bad'] .rc-gauge-fill {
            background: var(--danger);
        }

        .rc-gauge-mark {
            position: absolute;
            top: -5px;
            bottom: -5px;
            left: 0;
            width: 2px;
            background: var(--ink);
            transition: left .3s;
        }

        .rc-gauge-mark[hidden] {
            display: none;
        }

        .rc-gauge-mark::after {
            content: attr(data-label);
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            font-family: var(--font-mono);
            font-size: 10.5px;
            color: var(--ink-2);
            white-space: nowrap;
        }

        .rc-gauge-scale {
            display: flex;
            justify-content: space-between;
            margin-top: 6px;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--muted);
        }

        .rc-kv {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .rc-kv div {
            padding: 10px 12px;
            border: 1px solid var(--line-soft);
            border-radius: var(--radius);
            background: var(--canvas);
            min-width: 0;
        }

        .rc-kv dt {
            font-family: var(--font-display);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .rc-kv dd {
            margin: 4px 0 0;
            overflow: hidden;
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--ink);
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rc-log {
            display: grid;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .rc-log li {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 10px 22px;
            border-bottom: 1px solid var(--line-soft);
            font-size: 13.5px;
        }

        .rc-log li:last-child {
            border-bottom: 0;
        }

        .rc-empty {
            padding: 28px 22px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 520px) {
            .rc-kv {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .rc-sweep,
            .rc-scanline,
            .rc-rec {
                animation: none !important;
            }
        }
    </style>
@endpush

@push('topbar-actions')
    <a href="{{ route('biometric.login') }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="4" width="18" height="12" rx="2" />
            <path d="M8 20h8M12 16v4" />
        </svg>
        Open kiosk
    </a>
@endpush

@section('content')
    <div class="rc">
        <header class="page-head rise">
            <div>
                <div class="eyebrow">Operations · Administrator diagnostic</div>
                <h1 class="page-title">Recognition Console</h1>
                <p class="page-lede">
                    Test face matching against enrolled personnel and inspect the scores behind every decision.
                    Administrators bypass the liveness challenge here; the public kiosk always enforces it.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge badge-info">Admin only</span>
                <span class="tag">face.recognition.recognize</span>
            </div>
        </header>

        <div class="rc-grid">
            {{-- LEFT: capture --}}
            <div class="min-w-0">
                <section class="card overflow-hidden rise rise-2" aria-labelledby="captureTitle">
                    <div class="card-head">
                        <div>
                            <h2 class="card-title" id="captureTitle">Live capture</h2>
                            <p class="muted text-sm">Frames are checked twice a second; matching runs when quality is
                                good.</p>
                        </div>
                        <span id="cameraStatus" class="badge badge-muted" role="status">
                            <span id="cameraStatusText">Standby</span>
                        </span>
                    </div>

                    <div class="rc-stage-wrap">
                        <div class="rc-stage" id="stage" data-state="idle" data-camera="off">
                            <video id="cameraVideo" autoplay muted playsinline></video>
                            <canvas class="rc-box" id="boxCanvas" aria-hidden="true"></canvas>

                            <div class="rc-sweep" aria-hidden="true"></div>
                            <div class="rc-scanline" aria-hidden="true"></div>

                            <svg class="rc-overlay" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice"
                                aria-hidden="true">
                                <defs>
                                    <mask id="rcOvalMask">
                                        <rect width="400" height="300" fill="white" />
                                        <ellipse cx="200" cy="144" rx="82" ry="108" fill="black" />
                                    </mask>
                                </defs>
                                <rect class="rc-veil" width="400" height="300" mask="url(#rcOvalMask)" />
                                <ellipse class="rc-ring" cx="200" cy="144" rx="82" ry="108" />
                                <path class="rc-bracket" d="M108 50 v-14 h14" />
                                <path class="rc-bracket" d="M292 50 v-14 h-14" />
                                <path class="rc-bracket" d="M108 240 v14 h14" />
                                <path class="rc-bracket" d="M292 240 v14 h-14" />
                            </svg>

                            <div class="rc-hud" aria-hidden="true">
                                <div class="rc-hud-group">
                                    <span class="rc-chip"><span class="rc-rec"></span><span id="hudCamera">Standby</span></span>
                                    <span class="rc-chip" id="hudQuality">QC –/6</span>
                                </div>
                                <div class="rc-hud-group">
                                    <span class="rc-chip" id="hudLive">PAD –</span>
                                    <span class="rc-chip" id="hudStage">Idle</span>
                                </div>
                            </div>

                            <div class="rc-guidance" id="guidance" data-tone="action" role="status" aria-live="polite">
                                Camera standing by
                            </div>

                            {{-- Idle / starting cover --}}
                            <div class="rc-cover" id="stageIdle">
                                <div>
                                    <svg class="rc-cover-icon" viewBox="0 0 120 120" fill="none" stroke="currentColor"
                                        stroke-width="3" stroke-linecap="round" aria-hidden="true">
                                        <path d="M20 38V26a6 6 0 0 1 6-6h12M82 20h12a6 6 0 0 1 6 6v12M100 82v12a6 6 0 0 1-6 6H82M38 100H26a6 6 0 0 1-6-6V82" />
                                        <ellipse cx="60" cy="56" rx="20" ry="26" />
                                        <path d="M36 96c4-12 13-18 24-18s20 6 24 18" />
                                    </svg>
                                    <h3 id="stageIdleTitle">Starting camera…</h3>
                                    <p id="stageIdleText">Allow camera access when your browser asks.</p>
                                    <button type="button" class="btn btn-gold" id="startCameraButton">Start camera</button>
                                </div>
                            </div>

                            {{-- Camera error cover --}}
                            <div class="rc-cover" id="cameraError" data-tone="error" role="alert" hidden>
                                <div>
                                    <svg class="rc-cover-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="M15 10l4.55-2.28A1 1 0 0 1 21 8.62v6.76a1 1 0 0 1-1.45.9L15 14" />
                                        <rect x="3" y="6" width="12" height="12" rx="2" />
                                        <path d="M3 3l18 18" />
                                    </svg>
                                    <h3>Camera unavailable</h3>
                                    <p id="cameraErrorMessage">Allow camera access to continue.</p>
                                    <button id="retryCameraButton" type="button" class="btn btn-gold">Retry camera</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rc-toolbar">
                        <p class="muted text-sm m-0" id="verificationStatus" aria-live="polite">Initialising…</p>
                        <div class="rc-toolbar-actions">
                            <label class="rc-switch" title="Match automatically when capture quality is good">
                                <input type="checkbox" id="autoScan" checked>
                                <span class="rc-switch-track" aria-hidden="true"></span>
                                Auto-match
                            </label>
                            <button type="button" class="btn btn-ghost btn-sm" id="cameraToggle" disabled>Stop camera</button>
                            <button type="button" class="btn btn-gold btn-sm" id="restartButton" hidden>Scan again</button>
                            <button type="button" class="btn btn-primary btn-sm" id="captureButton" disabled>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="13" r="4" />
                                    <path d="M5 7h3l2-3h4l2 3h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                                </svg>
                                Capture &amp; match
                            </button>
                        </div>
                    </div>
                </section>

                <div class="rc-sub">
                    {{-- Capture quality --}}
                    <section class="card rise rise-3" aria-labelledby="qualityTitle">
                        <div class="card-head">
                            <h2 class="card-title" id="qualityTitle">Capture quality</h2>
                            <span class="badge badge-muted" id="qualityBadge">Waiting</span>
                        </div>
                        <div class="card-pad" style="padding-top:8px;padding-bottom:12px">
                            <ul class="rc-checks">
                                <li class="rc-check" data-check="face"><span>Face detected</span><span class="rc-check-state" aria-hidden="true">✓</span></li>
                                <li class="rc-check" data-check="single"><span>Only one person in view</span><span class="rc-check-state" aria-hidden="true">✓</span></li>
                                <li class="rc-check" data-check="centered"><span>Centred in the oval</span><span class="rc-check-state" aria-hidden="true">✓</span></li>
                                <li class="rc-check" data-check="distance"><span>Distance</span><span class="rc-check-state" aria-hidden="true">✓</span></li>
                                <li class="rc-check" data-check="lighting"><span>Lighting</span><span class="rc-check-state" aria-hidden="true">✓</span></li>
                                <li class="rc-check" data-check="focus"><span>Image sharpness</span><span class="rc-check-state" aria-hidden="true">✓</span></li>
                            </ul>
                            <p class="muted text-xs mt-3 mb-0" id="qualityNote">
                                Live guidance uses the detection-only analysis endpoint. It never compares identities.
                            </p>
                        </div>
                    </section>

                    {{-- Upload alternative --}}
                    <section class="card rise rise-3" aria-labelledby="uploadTitle">
                        <div class="card-head">
                            <h2 class="card-title" id="uploadTitle">Match a photo</h2>
                            <span class="tag">JPG · PNG · WEBP ≤ 5 MB</span>
                        </div>
                        <div class="card-pad grid gap-3">
                            <label class="rc-drop" id="uploadDrop" for="uploadInput">
                                <input type="file" id="uploadInput" accept="image/jpeg,image/png,image/webp">
                                <img id="uploadPreview" alt="Selected photo preview" hidden>
                                <span id="uploadPlaceholder" class="grid gap-1 place-items-center">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                        class="muted" aria-hidden="true">
                                        <path d="M12 16V4m-5 5 5-5 5 5" />
                                        <path d="M4 16v4h16v-4" />
                                    </svg>
                                    <strong class="text-sm">Drop a face photo or browse</strong>
                                    <span class="muted text-xs">One clear, front-facing face works best.</span>
                                </span>
                            </label>
                            <p class="mono muted text-xs m-0" id="uploadMeta" aria-live="polite"></p>
                            <button type="button" class="btn btn-primary" id="uploadRecognize" disabled>Match photo</button>
                        </div>
                    </section>
                </div>
            </div>

            {{-- RIGHT: analysis --}}
            <div class="min-w-0 rc-sticky grid gap-5">
                <section class="card rise rise-2" aria-labelledby="analysisTitle" aria-live="polite">
                    <div class="card-head">
                        <h2 class="card-title" id="analysisTitle">Match analysis</h2>
                        <span class="tag" id="analysisSource">—</span>
                    </div>

                    <div class="card-pad">
                        {{-- Waiting --}}
                        <div id="waitingResult" class="rc-empty" style="padding:18px 0">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                                class="mx-auto mb-3" style="color:var(--pcg-blue)" aria-hidden="true">
                                <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2" />
                                <circle cx="12" cy="11" r="3" />
                                <path d="M7.5 17.5a5 5 0 0 1 9 0" />
                            </svg>
                            <strong class="block" style="color:var(--ink)">No attempt yet</strong>
                            Position a face in the oval or upload a photo. Scores appear here after each match attempt.
                        </div>

                        {{-- Result --}}
                        <div id="analysisResult" hidden>
                            <div class="rc-verdict">
                                <div class="min-w-0">
                                    <span class="badge" id="verdictBadge">—</span>
                                    <p class="rc-verdict-name" id="resultName">—</p>
                                    <p class="mono muted text-sm mt-1 mb-0">
                                        <span id="resultPersonId">—</span>
                                    </p>
                                </div>
                                <span class="mono muted text-xs whitespace-nowrap" id="resultTime">--:--:--</span>
                            </div>

                            <p class="text-sm mt-3 mb-0" id="resultMessage" style="color:var(--ink-2)"></p>

                            <div class="flex flex-wrap gap-2 mt-4" id="resultLinks" hidden>
                                <a class="btn btn-primary btn-sm" id="personLink" href="#">Open personnel record</a>
                                <a class="btn btn-ghost btn-sm" id="verifiedLink" href="#" hidden>Verified view</a>
                            </div>

                            {{-- Similarity --}}
                            <div class="rc-section">
                                <div class="rc-metric-head">
                                    <span class="rc-metric-label">Similarity score</span>
                                    <span class="rc-metric-value" id="resultScore">—</span>
                                </div>
                                <div class="rc-gauge" id="simGauge" role="meter" aria-label="Similarity score"
                                    aria-valuemin="0" aria-valuemax="1" aria-valuenow="0">
                                    <div class="rc-gauge-fill" id="simFill"></div>
                                    <div class="rc-gauge-mark" id="simMark" data-label="THR" hidden></div>
                                </div>
                                <div class="rc-gauge-scale"><span>0.00</span><span>0.50</span><span>1.00</span></div>
                                <p class="muted text-xs mt-2 mb-0">
                                    Match threshold <span class="mono" id="resultThreshold">—</span>
                                    · margin <span class="mono" id="resultMargin">—</span>
                                </p>
                            </div>

                            {{-- Liveness --}}
                            <div class="rc-section">
                                <div class="rc-metric-head">
                                    <span class="rc-metric-label">Passive liveness</span>
                                    <span class="badge badge-muted" id="liveBadge">—</span>
                                </div>
                                <p class="muted text-xs mt-0 mb-3">Admin diagnostic — the kiosk enforces it.</p>
                                <div class="rc-metric-head" style="margin-bottom:6px">
                                    <span class="muted text-xs">Live score</span>
                                    <span class="mono text-sm" id="liveScore">—</span>
                                </div>
                                <div class="rc-gauge" id="liveGauge" role="meter" aria-label="Passive liveness score"
                                    aria-valuemin="0" aria-valuemax="1" aria-valuenow="0">
                                    <div class="rc-gauge-fill" id="liveFill"></div>
                                    <div class="rc-gauge-mark" id="liveMark" data-label="THR" hidden></div>
                                </div>
                                <div class="rc-gauge-scale"><span>0.00</span><span>0.50</span><span>1.00</span></div>
                            </div>

                            {{-- Model diagnostics --}}
                            <div class="rc-section">
                                <dl class="rc-kv m-0">
                                    <div>
                                        <dt>Detection</dt>
                                        <dd id="resultDetection">—</dd>
                                    </div>
                                    <div>
                                        <dt>Model</dt>
                                        <dd id="resultModel" title="">—</dd>
                                    </div>
                                    <div>
                                        <dt>Time</dt>
                                        <dd id="resultTimeMs">—</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card rise rise-3" aria-labelledby="logTitle">
                    <div class="card-head">
                        <h2 class="card-title" id="logTitle">Session log</h2>
                        <span class="mono muted text-xs" id="logCount">0 attempts</span>
                    </div>
                    <ol class="rc-log" id="attemptLog"></ol>
                    <p class="rc-empty" id="attemptLogEmpty">Attempts made on this page are listed here. Every attempt is
                        also written to the recognition audit log.</p>
                </section>
            </div>
        </div>

        <canvas id="captureCanvas" hidden></canvas>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';

            const routes = {
                recognize: @json(route('face.recognition.recognize')),
                analyze: @json(route('face.recognition.analyze')),
                people: @json(url('/people')),
            };

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const $ = (id) => document.getElementById(id);

            const stage = $('stage');
            const video = $('cameraVideo');
            const canvas = $('captureCanvas');
            const boxCanvas = $('boxCanvas');
            const guidance = $('guidance');
            const statusLine = $('verificationStatus');
            const autoScan = $('autoScan');
            const cameraToggle = $('cameraToggle');
            const captureButton = $('captureButton');
            const restartButton = $('restartButton');

            const ANALYZE_EVERY_MS = 500; // ~2 fps live guidance
            const READY_FRAMES = 2; // consecutive good frames before auto-match
            const NO_MATCH_COOLDOWN_MS = 3000; // pause after a miss so the audit log is not flooded
            const FALLBACK_EVERY_MS = 2500; // timed capture when live guidance is unavailable

            let stream = null;
            let loopTimer = null;
            let analyzing = false;
            let recognizing = false;
            let paused = false;
            let readyStreak = 0;
            let cooldownUntil = 0;
            let analyzeFailures = 0;
            let lastFallbackAt = 0;
            let lastBox = null;
            let uploadFile = null;
            let attempts = 0;

            /* ------------------------------------------------------------
             * Small UI helpers
             * ---------------------------------------------------------- */

            function setStage(state) {
                stage.dataset.state = state;
            }

            function setGuidance(text, tone = 'action') {
                guidance.textContent = text;
                guidance.dataset.tone = tone;
            }

            function setStatus(text) {
                statusLine.textContent = text;
            }

            function setCameraStatus(text, tone) {
                $('cameraStatus').className = 'badge badge-' + tone;
                $('cameraStatusText').textContent = text;
                $('hudCamera').textContent = text;
            }

            function setHudStage(text) {
                $('hudStage').textContent = text;
            }

            function cssVar(name) {
                return getComputedStyle(stage).getPropertyValue(name).trim();
            }

            function clamp01(value) {
                return Math.max(0, Math.min(1, value));
            }

            function isNum(value) {
                return typeof value === 'number' && Number.isFinite(value);
            }

            function fmt(value, digits) {
                return isNum(value) ? value.toFixed(digits) : '—';
            }

            /* ------------------------------------------------------------
             * Networking
             * ---------------------------------------------------------- */

            async function postForm(url, form) {
                const response = await fetch(url, {
                    method: 'POST',
                    body: form,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });

                const type = response.headers.get('content-type') || '';
                let data = {};

                if (type.includes('application/json')) {
                    try {
                        data = await response.json();
                    } catch (e) {
                        data = {};
                    }
                } else if (response.status === 419) {
                    data = { message: 'Your session expired. Reload the page and try again.' };
                } else {
                    data = { message: 'Unexpected response from the server (HTTP ' + response.status + ').' };
                }

                return { response, data };
            }

            /* ------------------------------------------------------------
             * Camera
             * ---------------------------------------------------------- */

            function cameraErrorMessage(error) {
                if (!error) return 'Unable to access the camera.';
                switch (error.name) {
                    case 'NotAllowedError':
                        return 'Camera permission was denied. Allow camera access in your browser and try again.';
                    case 'NotFoundError':
                        return 'No camera was found on this device. You can still match an uploaded photo.';
                    case 'NotReadableError':
                        return 'The camera is already in use by another application.';
                    case 'SecurityError':
                        return 'Camera access is blocked by browser security settings (HTTPS or localhost required).';
                    default:
                        return error.message || 'Unable to access the camera.';
                }
            }

            async function startCamera() {
                stopCamera(false);

                $('cameraError').hidden = true;
                $('stageIdle').hidden = false;
                $('stageIdleTitle').textContent = 'Starting camera…';
                $('stageIdleText').textContent = 'Allow camera access when your browser asks.';
                $('startCameraButton').hidden = true;
                setCameraStatus('Starting', 'warn');
                setStatus('Requesting camera access…');

                try {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        throw new Error('Camera access is not supported by this browser.');
                    }

                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'user' },
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                        },
                        audio: false,
                    });

                    video.srcObject = stream;
                    await video.play();

                    $('stageIdle').hidden = true;
                    stage.dataset.camera = 'on';
                    setCameraStatus('Live', 'ok');
                    cameraToggle.textContent = 'Stop camera';
                    cameraToggle.disabled = false;
                    captureButton.disabled = false;

                    resumeScanning();
                } catch (error) {
                    showCameraError(cameraErrorMessage(error));
                }
            }

            function stopCamera(showIdle = true) {
                stopLoop();

                if (stream) {
                    stream.getTracks().forEach((track) => track.stop());
                    stream = null;
                }

                video.srcObject = null;
                stage.dataset.camera = 'off';
                lastBox = null;
                drawBox(null);
                setStage('idle');
                setHudStage('Idle');
                setCameraStatus('Standby', 'muted');
                cameraToggle.textContent = 'Start camera';
                captureButton.disabled = true;
                restartButton.hidden = true;

                if (showIdle) {
                    $('stageIdle').hidden = false;
                    $('stageIdleTitle').textContent = 'Camera off';
                    $('stageIdleText').textContent = 'Start the camera to run live capture, or match an uploaded photo.';
                    $('startCameraButton').hidden = false;
                    setGuidance('Camera standing by');
                    setStatus('Camera stopped.');
                }
            }

            function showCameraError(message) {
                stopCamera(false);
                $('stageIdle').hidden = true;
                $('cameraErrorMessage').textContent = message;
                $('cameraError').hidden = false;
                setCameraStatus('Unavailable', 'danger');
                cameraToggle.disabled = false;
                setStatus('Camera could not be started.');
            }

            /* ------------------------------------------------------------
             * Loop: live guidance (~2 fps) + gated auto-match
             * ---------------------------------------------------------- */

            function startLoop() {
                stopLoop();
                loopTimer = setInterval(tick, ANALYZE_EVERY_MS);
                tick();
            }

            function stopLoop() {
                if (loopTimer !== null) {
                    clearInterval(loopTimer);
                    loopTimer = null;
                }
            }

            function resumeScanning() {
                paused = false;
                readyStreak = 0;
                cooldownUntil = 0;
                restartButton.hidden = true;
                setStage('aligning');
                setHudStage('Aligning');
                setGuidance('Position the face inside the oval.');
                setStatus(autoScan.checked ?
                    'Live. Matching runs automatically when capture quality is good.' :
                    'Live. Auto-match is off — use Capture & match.');
                startLoop();
            }

            function tick() {
                if (!stream || paused || recognizing) return;

                if (analyzeFailures < 3) {
                    analyzeFrame();
                    return;
                }

                // Live guidance unavailable: fall back to timed capture.
                const now = Date.now();
                if (autoScan.checked && now >= cooldownUntil && now - lastFallbackAt >= FALLBACK_EVERY_MS) {
                    lastFallbackAt = now;
                    recognizeCamera();
                }
            }

            function grabFrame(maxWidth) {
                const vw = video.videoWidth;
                const vh = video.videoHeight;
                if (!vw || !vh) return Promise.resolve(null);

                const scale = Math.min(1, maxWidth / vw);
                canvas.width = Math.round(vw * scale);
                canvas.height = Math.round(vh * scale);
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                return new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.88));
            }

            async function analyzeFrame() {
                if (analyzing) return;
                analyzing = true;

                try {
                    const blob = await grabFrame(640);
                    if (!blob || !stream || paused || recognizing) return;

                    const form = new FormData();
                    form.append('face_image', blob, 'frame.jpg');

                    const { response, data } = await postForm(routes.analyze, form);
                    if (!stream || paused || recognizing) return;

                    if (!response.ok || !data.success) {
                        analyzeFailures++;
                        setStage('warn');
                        setGuidance(
                            analyzeFailures >= 3 ?
                            'Live guidance offline — timed capture active' :
                            (data.message || 'Face analysis unavailable. Retrying…'),
                            'warn'
                        );
                        $('qualityBadge').className = 'badge badge-warn';
                        $('qualityBadge').textContent = 'Offline';
                        return;
                    }

                    analyzeFailures = 0;
                    renderChecks(data);

                    lastBox = data.box || null;
                    drawBox(lastBox, data.quality_ok);

                    const tone = data.guidance?.tone === 'warn' ? 'warn' : (data.quality_ok ? 'ok' : 'action');
                    setGuidance(
                        data.quality_ok ?
                        (autoScan.checked ? 'Quality good — hold still' : 'Quality good — ready to capture') :
                        (data.guidance?.message || 'Position the face inside the oval.'),
                        tone
                    );
                    setStage(data.quality_ok ? 'ready' : (tone === 'warn' ? 'warn' : 'aligning'));
                    setHudStage(data.quality_ok ? 'Ready' : 'Aligning');

                    readyStreak = data.quality_ok ? readyStreak + 1 : 0;

                    if (autoScan.checked && readyStreak >= READY_FRAMES && Date.now() >= cooldownUntil) {
                        recognizeCamera();
                    }
                } catch (error) {
                    analyzeFailures++;
                    setGuidance('Connection problem. Retrying…', 'warn');
                } finally {
                    analyzing = false;
                }
            }

            function renderChecks(data) {
                const checks = data.checks || {};
                let passed = 0;
                let total = 0;

                document.querySelectorAll('.rc-check[data-check]').forEach((row) => {
                    const key = row.dataset.check;
                    if (!(key in checks)) {
                        delete row.dataset.ok;
                        return;
                    }
                    total++;
                    if (checks[key]) passed++;
                    row.dataset.ok = String(Boolean(checks[key]));
                    row.querySelector('.rc-check-state').textContent = checks[key] ? '✓' : '✕';
                });

                $('hudQuality').textContent = 'QC ' + passed + '/' + (total || 6);
                $('qualityBadge').className = 'badge ' + (data.quality_ok ? 'badge-ok' : 'badge-warn');
                $('qualityBadge').textContent = data.quality_ok ? 'Good' : 'Adjust';

                const live = data.diagnostics?.liveness;
                if (live && isNum(live.live_score)) {
                    $('hudLive').textContent = 'PAD ' + live.live_score.toFixed(2);
                } else {
                    $('hudLive').textContent = 'PAD –';
                }
            }

            function drawBox(box, good) {
                const dpr = window.devicePixelRatio || 1;
                const w = boxCanvas.clientWidth;
                const h = boxCanvas.clientHeight;

                if (boxCanvas.width !== Math.round(w * dpr) || boxCanvas.height !== Math.round(h * dpr)) {
                    boxCanvas.width = Math.round(w * dpr);
                    boxCanvas.height = Math.round(h * dpr);
                }

                const ctx = boxCanvas.getContext('2d');
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                ctx.clearRect(0, 0, w, h);

                const vw = video.videoWidth;
                const vh = video.videoHeight;
                if (!box || !vw || !vh || !w || !h) return;

                // Map normalised frame coordinates onto the mirrored, object-fit: cover video.
                const scale = Math.max(w / vw, h / vh);
                const dw = vw * scale;
                const dh = vh * scale;
                const ox = (w - dw) / 2;
                const oy = (h - dh) / 2;

                const x = (1 - box.x - box.w) * dw + ox;
                const y = box.y * dh + oy;
                const bw = box.w * dw;
                const bh = box.h * dh;
                const t = Math.min(bw, bh) * 0.22;

                ctx.strokeStyle = cssVar(good ? '--rc-live-ok' : '--rc-live-warn');
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.beginPath();
                [
                    [x, y + t, x, y, x + t, y],
                    [x + bw - t, y, x + bw, y, x + bw, y + t],
                    [x + bw, y + bh - t, x + bw, y + bh, x + bw - t, y + bh],
                    [x + t, y + bh, x, y + bh, x, y + bh - t],
                ].forEach(([ax, ay, bx, by, cx, cy]) => {
                    ctx.moveTo(ax, ay);
                    ctx.lineTo(bx, by);
                    ctx.lineTo(cx, cy);
                });
                ctx.stroke();
            }

            window.addEventListener('resize', () => drawBox(lastBox, stage.dataset.state === 'ready'));

            /* ------------------------------------------------------------
             * Recognition
             * ---------------------------------------------------------- */

            async function recognizeCamera() {
                if (recognizing || !stream) return;

                recognizing = true;
                setStage('scanning');
                setHudStage('Matching');
                setGuidance('Hold still — matching against enrolled personnel…', 'ok');
                setStatus('Matching…');
                captureButton.disabled = true;

                try {
                    const blob = await grabFrame(1280);
                    if (!blob) throw new Error('Unable to capture a camera frame.');
                    await recognizeBlob(blob, 'Camera', 'console-capture.jpg');
                } catch (error) {
                    setGuidance(error.message || 'Capture failed.', 'warn');
                    setStage('warn');
                } finally {
                    recognizing = false;
                    captureButton.disabled = !stream;
                }
            }

            async function recognizeBlob(blob, source, filename) {
                const form = new FormData();
                form.append('face_image', blob, filename);

                let response;
                let data;

                try {
                    ({ response, data } = await postForm(routes.recognize, form));
                } catch (error) {
                    response = { ok: false, status: 0 };
                    data = { message: 'Network error — the request did not reach the server.' };
                }

                const matched = data.matched === true && !!data.person;
                renderAnalysis(data, response, source, matched);
                logAttempt(data, response, source, matched);

                if (source !== 'Camera') return matched;

                readyStreak = 0;

                if (matched) {
                    paused = true;
                    stopLoop();
                    setStage('matched');
                    setHudStage('Matched');
                    setGuidance('Match: ' + (data.person.name || data.person.person_id || 'enrolled person'), 'ok');
                    setStatus('Match found. Scanning paused — review the analysis, then scan again.');
                    restartButton.hidden = false;
                } else {
                    cooldownUntil = Date.now() + NO_MATCH_COOLDOWN_MS;
                    setStage('nomatch');
                    setHudStage(response.ok ? 'No match' : 'Error');
                    setGuidance(data.message || (response.ok ? 'No match.' : 'Recognition failed.'), 'bad');
                    setStatus(autoScan.checked ?
                        'No match. Auto-match resumes in a few seconds.' :
                        'No match. Adjust and capture again.');
                }

                return matched;
            }

            function setGauge(prefix, value, threshold) {
                const gauge = $(prefix + 'Gauge');
                const fill = $(prefix + 'Fill');
                const mark = $(prefix + 'Mark');

                if (isNum(value)) {
                    fill.style.width = (clamp01(value) * 100).toFixed(1) + '%';
                    gauge.setAttribute('aria-valuenow', clamp01(value).toFixed(3));
                    gauge.dataset.tone = isNum(threshold) ? (value >= threshold ? 'ok' : 'bad') : '';
                } else {
                    fill.style.width = '0%';
                    gauge.setAttribute('aria-valuenow', '0');
                    gauge.dataset.tone = '';
                }

                if (isNum(threshold)) {
                    mark.hidden = false;
                    mark.style.left = 'calc(' + (clamp01(threshold) * 100).toFixed(1) + '% - 1px)';
                    mark.dataset.label = 'THR ' + threshold.toFixed(2);
                } else {
                    mark.hidden = true;
                }
            }

            function renderAnalysis(data, response, source, matched) {
                $('waitingResult').hidden = true;
                $('analysisResult').hidden = false;
                $('analysisSource').textContent = source;
                $('resultTime').textContent = new Date().toLocaleTimeString();

                const hasScores = isNum(data.similarity_score);
                const badge = $('verdictBadge');

                if (matched) {
                    badge.className = 'badge badge-ok';
                    badge.textContent = 'Matched';
                } else if (response.ok || hasScores) {
                    badge.className = 'badge badge-danger';
                    badge.textContent = 'Not matched';
                } else {
                    badge.className = 'badge badge-warn';
                    badge.textContent = 'Attempt failed';
                }

                $('resultName').textContent = matched ?
                    (data.person.name || 'Unnamed record') :
                    (response.ok || hasScores ? 'No enrolled match' : 'No result');
                $('resultPersonId').textContent = matched ?
                    (data.person.person_id || '—') :
                    (response.status ? 'HTTP ' + response.status : 'No response');
                $('resultMessage').textContent = data.message || '';

                // Links to the matched record.
                const links = $('resultLinks');
                if (matched && data.person.id !== undefined && data.person.id !== null) {
                    $('personLink').href = routes.people + '/' + encodeURIComponent(data.person.id);
                    const verified = $('verifiedLink');
                    if (typeof data.redirect_url === 'string' &&
                        (data.redirect_url.startsWith(window.location.origin + '/') || data.redirect_url.startsWith('/'))) {
                        verified.href = data.redirect_url;
                        verified.hidden = false;
                    } else {
                        verified.hidden = true;
                    }
                    links.hidden = false;
                } else {
                    links.hidden = true;
                }

                // Similarity vs threshold.
                const threshold = isNum(data.threshold) ? data.threshold : null;
                $('resultScore').textContent = fmt(data.similarity_score, 4);
                $('resultThreshold').textContent = fmt(threshold, 2);
                $('resultMargin').textContent = hasScores && threshold !== null ?
                    ((data.similarity_score - threshold >= 0 ? '+' : '') + (data.similarity_score - threshold).toFixed(4)) :
                    '—';
                setGauge('sim', data.similarity_score, threshold);

                // Passive liveness (diagnostic only for admins).
                const live = data.liveness && typeof data.liveness === 'object' ? data.liveness : null;
                const liveBadge = $('liveBadge');
                if (!live) {
                    liveBadge.className = 'badge badge-muted';
                    liveBadge.textContent = 'Not reported';
                } else if (live.is_live === true) {
                    liveBadge.className = 'badge badge-ok';
                    liveBadge.textContent = 'Live';
                } else if (live.is_live === false) {
                    liveBadge.className = 'badge badge-danger';
                    liveBadge.textContent = 'Spoof suspected';
                } else {
                    liveBadge.className = 'badge badge-muted';
                    liveBadge.textContent = live.status ? String(live.status) : 'Unavailable';
                }
                $('liveScore').textContent = live ?
                    fmt(live.live_score, 4) + (isNum(live.threshold) ? ' / ' + live.threshold.toFixed(2) : '') :
                    '—';
                setGauge('live', live ? live.live_score : null, live && isNum(live.threshold) ? live.threshold : null);

                // Model diagnostics.
                $('resultDetection').textContent = fmt(data.detection_score, 3);
                $('resultModel').textContent = data.model_version ? String(data.model_version) : '—';
                $('resultModel').title = data.model_version ? String(data.model_version) : '';
                $('resultTimeMs').textContent = isNum(data.processing_time_ms) ?
                    Math.round(data.processing_time_ms) + ' ms' :
                    '—';
            }

            function logAttempt(data, response, source, matched) {
                attempts++;
                $('attemptLogEmpty').hidden = true;
                $('logCount').textContent = attempts + (attempts === 1 ? ' attempt' : ' attempts');

                const item = document.createElement('li');

                const badge = document.createElement('span');
                badge.className = 'badge ' + (matched ? 'badge-ok' : (response.ok ? 'badge-danger' : 'badge-warn'));
                badge.textContent = matched ? 'Match' : (response.ok ? 'Miss' : 'Error');

                const label = document.createElement('span');
                label.className = 'min-w-0 truncate';
                label.textContent = (matched ? (data.person.name || data.person.person_id) : (data.message || 'No match')) +
                    ' · ' + source;

                const score = document.createElement('span');
                score.className = 'mono muted text-xs';
                score.textContent = (isNum(data.similarity_score) ? data.similarity_score.toFixed(3) + ' · ' : '') +
                    new Date().toLocaleTimeString();

                item.append(badge, label, score);
                const log = $('attemptLog');
                log.prepend(item);
                while (log.children.length > 8) log.lastElementChild.remove();
            }

            /* ------------------------------------------------------------
             * Upload alternative
             * ---------------------------------------------------------- */

            const uploadInput = $('uploadInput');
            const uploadDrop = $('uploadDrop');
            const uploadButton = $('uploadRecognize');
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
            const MAX_BYTES = 5 * 1024 * 1024;
            let previewUrl = null;

            function selectUpload(file) {
                uploadFile = null;
                uploadButton.disabled = true;

                if (!file) return;

                if (!ALLOWED.includes(file.type)) {
                    $('uploadMeta').textContent = 'Unsupported file type. Use JPG, PNG or WEBP.';
                    return;
                }

                if (file.size > MAX_BYTES) {
                    $('uploadMeta').textContent = 'File is larger than 5 MB.';
                    return;
                }

                uploadFile = file;
                if (previewUrl) URL.revokeObjectURL(previewUrl);
                previewUrl = URL.createObjectURL(file);
                $('uploadPreview').src = previewUrl;
                $('uploadPreview').hidden = false;
                $('uploadPlaceholder').hidden = true;
                $('uploadMeta').textContent = file.name + ' · ' + (file.size / 1024).toFixed(0) + ' KB';
                uploadButton.disabled = false;
            }

            uploadInput.addEventListener('change', () => selectUpload(uploadInput.files[0]));

            ['dragenter', 'dragover'].forEach((type) => uploadDrop.addEventListener(type, (event) => {
                event.preventDefault();
                uploadDrop.classList.add('is-over');
            }));

            ['dragleave', 'drop'].forEach((type) => uploadDrop.addEventListener(type, () => {
                uploadDrop.classList.remove('is-over');
            }));

            uploadDrop.addEventListener('drop', (event) => {
                event.preventDefault();
                const file = event.dataTransfer?.files?.[0];
                if (file) selectUpload(file);
            });

            uploadButton.addEventListener('click', async () => {
                if (!uploadFile || recognizing) return;

                recognizing = true;
                uploadButton.disabled = true;
                uploadButton.textContent = 'Matching…';

                try {
                    await recognizeBlob(uploadFile, 'Upload', uploadFile.name || 'upload.jpg');
                } finally {
                    recognizing = false;
                    uploadButton.disabled = !uploadFile;
                    uploadButton.textContent = 'Match photo';
                }
            });

            /* ------------------------------------------------------------
             * Controls
             * ---------------------------------------------------------- */

            $('retryCameraButton').addEventListener('click', startCamera);
            $('startCameraButton').addEventListener('click', startCamera);
            restartButton.addEventListener('click', resumeScanning);

            cameraToggle.addEventListener('click', () => {
                if (stream) stopCamera(true);
                else startCamera();
            });

            captureButton.addEventListener('click', () => {
                if (paused) {
                    paused = false;
                    restartButton.hidden = true;
                    startLoop();
                }
                cooldownUntil = 0;
                recognizeCamera();
            });

            autoScan.addEventListener('change', () => {
                readyStreak = 0;
                if (stream && !paused) {
                    setStatus(autoScan.checked ?
                        'Live. Matching runs automatically when capture quality is good.' :
                        'Live. Auto-match is off — use Capture & match.');
                }
            });

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) stopLoop();
                else if (stream && !paused) startLoop();
            });

            window.addEventListener('beforeunload', () => stopCamera(false));

            startCamera();
        })();
    </script>
@endpush
