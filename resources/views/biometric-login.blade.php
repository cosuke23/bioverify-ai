<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Identity Verification · BioVerify AI · Philippine Coast Guard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pcg.css') }}">

    <style>
        /* Operations console: dark canvas, PCG blue depth, gold signals. */
        :root {
            --k-bg: #050b1a;
            --k-panel: rgba(13, 26, 56, .72);
            --k-line: rgba(148, 178, 230, .16);
            --k-text: #e6eeff;
            --k-dim: #8ea3c8;
            --k-ok: #34d399;
            --k-warn: #fbbf24;
            --k-bad: #f87171;
        }

        body.kiosk {
            margin: 0;
            min-height: 100vh;
            color: var(--k-text);
            font-family: var(--font-body);
            background:
                radial-gradient(60% 50% at 75% 0%, rgba(0, 51, 160, .45), transparent 70%),
                radial-gradient(50% 40% at 0% 100%, rgba(244, 197, 66, .08), transparent 70%),
                var(--k-bg);
            overflow-x: hidden;
        }

        body.kiosk::before {
            /* chart-grid overlay */
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(148, 178, 230, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 178, 230, .05) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse at center, #000 30%, transparent 80%);
        }

        .k-top {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 28px;
            border-bottom: 1px solid var(--k-line);
            background: rgba(5, 11, 26, .6);
            backdrop-filter: blur(8px);
        }

        .k-top .rail-crest {
            background: rgba(0, 51, 160, .55);
        }

        .k-brand-title {
            font-family: var(--font-display);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: .02em;
            line-height: 1;
        }

        .k-brand-sub {
            margin-top: 4px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--k-dim);
        }

        .k-clock {
            margin-left: auto;
            text-align: right;
        }

        .k-clock-time {
            font-family: var(--font-mono);
            font-size: 20px;
            letter-spacing: .04em;
        }

        .k-clock-date {
            font-size: 12px;
            color: var(--k-dim);
        }

        .k-admin {
            padding: 8px 14px;
            border: 1px solid var(--k-line);
            border-radius: 999px;
            color: var(--k-dim);
            font-size: 13px;
            text-decoration: none;
            white-space: nowrap;
            transition: color .15s, border-color .15s;
        }

        .k-admin:hover {
            border-color: var(--pcg-gold);
            color: var(--pcg-gold);
        }

        .k-main {
            position: relative;
            z-index: 1;
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px 24px 40px;
        }

        .k-hero {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        .k-eyebrow {
            font-family: var(--font-display);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--pcg-gold);
        }

        .k-title {
            margin: 6px 0 0;
            font-family: var(--font-display);
            font-size: clamp(34px, 5vw, 52px);
            font-weight: 700;
            line-height: .95;
        }

        .k-lede {
            max-width: 560px;
            margin: 10px 0 0;
            color: var(--k-dim);
        }

        /* Method switch */
        .k-switch {
            display: inline-flex;
            padding: 4px;
            border: 1px solid var(--k-line);
            border-radius: 12px;
            background: rgba(255, 255, 255, .04);
        }

        .k-switch button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 18px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: var(--k-dim);
            font: 600 14px var(--font-body);
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .k-switch button[aria-selected='true'] {
            background: var(--pcg-blue);
            color: #fff;
            box-shadow: inset 0 -2px 0 var(--pcg-gold);
        }

        .k-switch svg {
            width: 18px;
            height: 18px;
        }

        .k-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 22px;
        }

        .k-grid[hidden] {
            display: none;
        }

        .k-panel {
            border: 1px solid var(--k-line);
            border-radius: 18px;
            background: var(--k-panel);
            box-shadow: 0 30px 80px rgba(0, 0, 0, .35);
            backdrop-filter: blur(6px);
        }

        /* Camera stage */
        .stage {
            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            border-radius: 14px;
            background: #000;
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
        .stage[data-state='scanning'] .oval-ring {
            stroke: var(--k-ok);
            stroke-dasharray: none;
            filter: drop-shadow(0 0 10px rgba(52, 211, 153, .8));
        }

        .stage[data-state='warn'] .oval-ring {
            stroke: var(--k-warn);
            stroke-dasharray: none;
        }

        .bracket {
            fill: none;
            stroke: var(--pcg-gold);
            stroke-width: 4;
            stroke-linecap: round;
        }

        /* Radar sweep while matching */
        .sweep {
            position: absolute;
            left: 50%;
            top: 48%;
            width: 70%;
            aspect-ratio: 1;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            background: conic-gradient(from 0deg, rgba(52, 211, 153, 0) 0deg, rgba(52, 211, 153, .32) 40deg, rgba(52, 211, 153, 0) 80deg);
            opacity: 0;
            pointer-events: none;
            mix-blend-mode: screen;
        }

        .stage[data-state='scanning'] .sweep {
            opacity: 1;
            animation: sweep 1.4s linear infinite;
        }

        @keyframes sweep {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
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
        }

        .stage[data-state='aligning'] .scanline,
        .stage[data-state='ready'] .scanline {
            opacity: .9;
            animation: scan 2.6s ease-in-out infinite;
        }

        @keyframes scan {

            0%,
            100% {
                top: 16%;
            }

            50% {
                top: 80%;
            }
        }

        .hud-top {
            position: absolute;
            inset: 14px 14px auto 14px;
            display: flex;
            justify-content: space-between;
            pointer-events: none;
        }

        .hud-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border: 1px solid var(--k-line);
            border-radius: 999px;
            background: rgba(5, 11, 26, .7);
            font-family: var(--font-mono);
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .rec-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--k-bad);
            animation: blink 1.2s ease-in-out infinite;
        }

        @keyframes blink {
            50% {
                opacity: .25;
            }
        }

        .guidance {
            position: absolute;
            left: 50%;
            bottom: 18px;
            max-width: calc(100% - 32px);
            padding: 12px 22px;
            overflow: hidden;
            border: 1px solid var(--k-line);
            border-radius: 14px;
            background: rgba(5, 11, 26, .82);
            font-size: clamp(15px, 2.2vw, 20px);
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

        /* Idle / start state over the stage */
        .stage-idle {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            text-align: center;
            background:
                radial-gradient(circle at 50% 45%, rgba(0, 51, 160, .55), transparent 65%),
                #050b1a;
        }

        .stage-idle[hidden] {
            display: none;
        }

        .idle-face {
            width: 132px;
            height: 132px;
            margin: 0 auto 18px;
            color: var(--pcg-gold);
            animation: breathe 3s ease-in-out infinite;
        }

        @keyframes breathe {
            50% {
                transform: scale(1.05);
                opacity: .85;
            }
        }

        .k-btn-start {
            min-height: 52px;
            padding: 0 28px;
            font-size: 16px;
        }

        /* Side panel */
        .side {
            padding: 20px;
        }

        .side-title {
            margin: 0 0 14px;
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--k-dim);
        }

        .steps {
            display: grid;
            gap: 10px;
            margin: 0 0 22px;
            padding: 0;
            list-style: none;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--k-line);
            border-radius: 12px;
            color: var(--k-dim);
            transition: border-color .2s, background .2s, color .2s;
        }

        .step-num {
            display: grid;
            place-items: center;
            flex: 0 0 30px;
            width: 30px;
            height: 30px;
            border: 1px solid var(--k-line);
            border-radius: 50%;
            font-family: var(--font-mono);
            font-size: 13px;
        }

        .step[data-status='active'] {
            border-color: rgba(244, 197, 66, .55);
            background: rgba(244, 197, 66, .08);
            color: var(--k-text);
        }

        .step[data-status='active'] .step-num {
            border-color: var(--pcg-gold);
            color: var(--pcg-gold);
        }

        .step[data-status='done'] {
            color: var(--k-text);
        }

        .step[data-status='done'] .step-num {
            border-color: var(--k-ok);
            background: var(--k-ok);
            color: #052e1c;
        }

        .step strong {
            display: block;
            font-size: 14px;
        }

        .step small {
            display: block;
            font-size: 12px;
            color: var(--k-dim);
        }

        .checks {
            display: grid;
            gap: 2px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 4px;
            border-bottom: 1px dashed var(--k-line);
            font-size: 14px;
        }

        .check-state {
            display: inline-grid;
            place-items: center;
            width: 22px;
            height: 22px;
            border: 1px solid var(--k-line);
            border-radius: 50%;
            color: transparent;
            font-size: 13px;
            transition: background .2s, border-color .2s;
        }

        .check[data-ok='true'] .check-state {
            border-color: var(--k-ok);
            background: var(--k-ok);
            color: #052e1c;
        }

        .check[data-ok='false'] .check-state {
            border-color: rgba(248, 113, 113, .6);
        }

        .live-meter {
            display: flex;
            gap: 6px;
        }

        .live-meter span {
            width: 22px;
            height: 6px;
            border-radius: 3px;
            background: var(--k-line);
            transition: background .25s;
        }

        .live-meter span.on {
            background: var(--k-ok);
            box-shadow: 0 0 8px rgba(52, 211, 153, .7);
        }

        .notice {
            margin: 18px 0 0;
            padding: 12px 14px;
            border: 1px solid var(--k-line);
            border-radius: 12px;
            background: rgba(255, 255, 255, .04);
            font-size: 12.5px;
            color: var(--k-dim);
        }

        /* Result overlay */
        .result {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: grid;
            place-items: center;
            padding: 24px;
            background: rgba(3, 8, 20, .82);
            backdrop-filter: blur(6px);
        }

        .result[hidden] {
            display: none;
        }

        .result-card {
            width: min(460px, 100%);
            padding: 32px 28px;
            border: 1px solid var(--k-line);
            border-radius: 20px;
            background: linear-gradient(180deg, #0d1c40, #081330);
            text-align: center;
            box-shadow: 0 40px 100px rgba(0, 0, 0, .5);
            animation: pop .35s ease both;
        }

        @keyframes pop {
            from {
                opacity: 0;
                transform: scale(.94) translateY(8px);
            }
        }

        .result-icon {
            display: grid;
            place-items: center;
            width: 84px;
            height: 84px;
            margin: 0 auto 16px;
            border-radius: 50%;
        }

        .result-icon svg {
            width: 44px;
            height: 44px;
        }

        .result.ok .result-icon {
            background: rgba(52, 211, 153, .14);
            color: var(--k-ok);
            box-shadow: 0 0 0 10px rgba(52, 211, 153, .06);
        }

        .result.bad .result-icon {
            background: rgba(248, 113, 113, .14);
            color: var(--k-bad);
        }

        .result-title {
            margin: 0;
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 700;
        }

        .result-name {
            margin: 10px 0 4px;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .result-id {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            background: rgba(244, 197, 66, .12);
            color: var(--pcg-gold);
            font-family: var(--font-mono);
            font-size: 13px;
        }

        .result-msg {
            margin: 12px 0 0;
            color: var(--k-dim);
        }

        /* Voice */
        .voice-stage {
            display: grid;
            place-items: center;
            gap: 18px;
            min-height: 420px;
            padding: 34px 24px;
            text-align: center;
        }

        .phrase {
            max-width: 560px;
            margin: 0;
            padding: 16px 20px;
            border-left: 3px solid var(--pcg-gold);
            border-radius: 0 12px 12px 0;
            background: rgba(244, 197, 66, .06);
            font-size: 18px;
            font-style: italic;
            text-align: left;
        }

        .mic-btn {
            display: grid;
            place-items: center;
            width: 112px;
            height: 112px;
            border: 0;
            border-radius: 50%;
            background: var(--pcg-blue);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 0 0 10px rgba(0, 51, 160, .25), 0 20px 40px rgba(0, 0, 0, .4);
            transition: transform .15s, background .15s;
        }

        .mic-btn:hover {
            transform: scale(1.04);
        }

        .mic-btn.is-recording {
            background: #b91c1c;
            box-shadow: 0 0 0 10px rgba(185, 28, 28, .25), 0 0 40px rgba(248, 113, 113, .5);
        }

        .mic-btn svg {
            width: 44px;
            height: 44px;
        }

        .wave {
            width: min(520px, 100%);
            height: 70px;
        }

        .timer {
            font-family: var(--font-mono);
            font-size: 22px;
            letter-spacing: .06em;
        }

        @media (max-width: 960px) {
            .k-grid {
                grid-template-columns: 1fr;
            }

            .k-top {
                padding: 14px 16px;
            }

            .k-clock {
                display: none;
            }

            .k-admin {
                margin-left: auto;
            }

            .k-main {
                padding: 20px 16px 32px;
            }

            .guidance {
                white-space: normal;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .sweep,
            .scanline,
            .idle-face,
            .rec-dot {
                animation: none !important;
            }
        }
    </style>
</head>

<body class="kiosk pcg">
    <header class="k-top">
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
        <div>
            <div class="k-brand-title">BioVerify AI</div>
            <div class="k-brand-sub">Philippine Coast Guard · Identity Verification</div>
        </div>

        <div class="k-clock" aria-hidden="true">
            <div class="k-clock-time" id="clockTime">--:--:--</div>
            <div class="k-clock-date" id="clockDate">&nbsp;</div>
        </div>

        <a href="{{ route('login') }}" class="k-admin">Administrator sign-in</a>
    </header>

    <main class="k-main">
        @if (session('error'))
            <div class="alert alert-warn rise" role="alert" style="margin-bottom:18px">{{ session('error') }}</div>
        @endif

        <section class="k-hero rise">
            <div>
                <div class="k-eyebrow">Personnel Access Control</div>
                <h1 class="k-title">Verify your identity</h1>
                <p class="k-lede">
                    Look into the camera and follow the on-screen guide. A short liveness
                    check confirms a real person is present before your record opens.
                </p>
            </div>

            <div class="k-switch" role="tablist" aria-label="Verification method">
                <button type="button" role="tab" id="tabFace" aria-selected="true" aria-controls="facePanel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2" />
                        <circle cx="12" cy="11" r="3" />
                        <path d="M7.5 17.5a5 5 0 0 1 9 0" />
                    </svg>
                    Face
                </button>
                <button type="button" role="tab" id="tabVoice" aria-selected="false" aria-controls="voicePanel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="9" y="2" width="6" height="12" rx="3" />
                        <path d="M5 10a7 7 0 0 0 14 0" />
                        <path d="M12 19v3" />
                    </svg>
                    Voice
                </button>
            </div>
        </section>

        {{-- FACE ------------------------------------------------------ --}}
        <section id="facePanel" role="tabpanel" aria-labelledby="tabFace" class="k-grid rise rise-2">
            <div class="k-panel" style="padding:10px">
                <div class="stage" id="stage" data-state="idle">
                    <video id="camera" autoplay muted playsinline></video>
                    <canvas id="frameCanvas" hidden></canvas>

                    <div class="sweep" aria-hidden="true"></div>
                    <div class="scanline" aria-hidden="true"></div>

                    <svg class="stage-overlay" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice"
                        aria-hidden="true">
                        <defs>
                            <mask id="ovalMask">
                                <rect width="400" height="300" fill="#fff" />
                                <ellipse cx="200" cy="144" rx="82" ry="108" fill="#000" />
                            </mask>
                        </defs>
                        <rect width="400" height="300" fill="rgba(3,8,20,.55)" mask="url(#ovalMask)" />
                        <ellipse class="oval-ring" cx="200" cy="144" rx="82" ry="108" />
                        <path class="bracket" d="M108 50 v-14 h14" />
                        <path class="bracket" d="M292 50 v-14 h-14" />
                        <path class="bracket" d="M108 240 v14 h14" />
                        <path class="bracket" d="M292 240 v14 h-14" />
                    </svg>

                    <div class="hud-top" aria-hidden="true">
                        <span class="hud-chip"><span class="rec-dot"></span>Live</span>
                        <span class="hud-chip" id="hudStage">Position</span>
                    </div>

                    <div class="guidance" id="guidance" data-tone="action" role="status" aria-live="polite">
                        Starting camera…
                    </div>

                    <div class="stage-idle" id="stageIdle">
                        <div>
                            <svg class="idle-face" viewBox="0 0 120 120" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                                <path d="M16 36V22a6 6 0 0 1 6-6h14M84 16h14a6 6 0 0 1 6 6v14M104 84v14a6 6 0 0 1-6 6H84M36 104H22a6 6 0 0 1-6-6V84" />
                                <ellipse cx="60" cy="56" rx="22" ry="27" />
                                <path d="M35 98c4-12 14-18 25-18s21 6 25 18" />
                            </svg>
                            <p class="k-eyebrow" style="margin:0 0 6px">Face verification</p>
                            <p style="margin:0 auto 20px;color:var(--k-dim);max-width:360px">
                                Remove sunglasses or face coverings and stand about an arm's length from the camera.
                            </p>
                            <button type="button" class="btn btn-gold k-btn-start" id="startFace">
                                Start face verification
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="k-panel side" aria-label="Verification progress">
                <h2 class="side-title">Progress</h2>
                <ol class="steps">
                    <li class="step" id="step1" data-status="active">
                        <span class="step-num">1</span>
                        <span><strong>Position</strong><small>Face centred, good light</small></span>
                    </li>
                    <li class="step" id="step2" data-status="pending">
                        <span class="step-num">2</span>
                        <span><strong>Liveness check</strong><small>Look, turn, look back</small></span>
                    </li>
                    <li class="step" id="step3" data-status="pending">
                        <span class="step-num">3</span>
                        <span><strong>Identity match</strong><small>Compared with enrolled records</small></span>
                    </li>
                </ol>

                <h2 class="side-title">Live checks</h2>
                <ul class="checks">
                    <li class="check" data-check="single"><span>One face in view</span><span class="check-state">✓</span></li>
                    <li class="check" data-check="centered"><span>Centred in the oval</span><span class="check-state">✓</span></li>
                    <li class="check" data-check="distance"><span>Distance</span><span class="check-state">✓</span></li>
                    <li class="check" data-check="lighting"><span>Lighting</span><span class="check-state">✓</span></li>
                    <li class="check" data-check="focus"><span>Image sharpness</span><span class="check-state">✓</span></li>
                    <li class="check" id="liveCheck">
                        <span>Live presence</span>
                        <span class="live-meter" id="liveMeter" aria-hidden="true"></span>
                    </li>
                </ul>

                <p class="notice">
                    Your image is analysed on Philippine Coast Guard systems to confirm your identity.
                    Every verification attempt is recorded in the audit log.
                </p>
            </aside>
        </section>

        {{-- VOICE ----------------------------------------------------- --}}
        <section id="voicePanel" role="tabpanel" aria-labelledby="tabVoice" class="k-grid" hidden>
            <div class="k-panel voice-stage">
                <div class="k-eyebrow">Voice verification</div>
                <p class="phrase">
                    “I am verifying my identity for the Philippine Coast Guard BioVerify system.”
                </p>

                <button type="button" class="mic-btn" id="micButton" aria-label="Start recording">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="9" y="2" width="6" height="12" rx="3" />
                        <path d="M5 10a7 7 0 0 0 14 0" />
                        <path d="M12 19v3" />
                    </svg>
                </button>

                <canvas class="wave" id="wave" width="1040" height="140" aria-hidden="true"></canvas>
                <div class="timer" id="voiceTimer">00.0 s</div>
                <p id="voiceStatus" role="status" aria-live="polite" style="margin:0;color:var(--k-dim)">
                    Tap the microphone and read the sentence above (at least 3 seconds).
                </p>

                <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;align-items:center">
                    <audio id="voicePreview" controls hidden></audio>
                    <button type="button" class="btn btn-gold" id="verifyVoice" disabled>Verify voice</button>
                </div>
            </div>

            <aside class="k-panel side">
                <h2 class="side-title">Tips</h2>
                <ul class="checks">
                    <li class="check"><span>Quiet surroundings</span></li>
                    <li class="check"><span>Speak at a normal pace</span></li>
                    <li class="check"><span>Hold the device 20–30 cm away</span></li>
                    <li class="check"><span>Record 3–10 seconds</span></li>
                </ul>
                <p class="notice">
                    Your recording is processed on Philippine Coast Guard systems and every attempt is logged.
                </p>
            </aside>
        </section>
    </main>

    {{-- Result overlay --}}
    <div class="result" id="result" hidden role="dialog" aria-modal="true" aria-labelledby="resultTitle">
        <div class="result-card">
            <div class="result-icon" id="resultIcon"></div>
            <h2 class="result-title" id="resultTitle"></h2>
            <p class="result-name" id="resultName" hidden></p>
            <span class="result-id" id="resultId" hidden></span>
            <p class="result-msg" id="resultMsg"></p>
            <div style="margin-top:22px;display:flex;gap:10px;justify-content:center">
                <button type="button" class="btn btn-gold" id="resultRetry" hidden>Try again</button>
                <a class="btn btn-primary" id="resultOpen" hidden>Open my record</a>
            </div>
        </div>
    </div>

    <script>
        (() => {
            'use strict';

            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const routes = {
                analyze: @json(route('face.recognition.analyze')),
                recognize: @json(route('face.recognition.recognize')),
                voice: @json(route('voice.recognition.recognize')),
            };

            const $ = (id) => document.getElementById(id);

            /* Clock (Philippine time) ----------------------------------- */
            const tick = () => {
                const now = new Date();
                $('clockTime').textContent = now.toLocaleTimeString('en-PH', {
                    timeZone: 'Asia/Manila',
                    hour12: false,
                }) + ' PHT';
                $('clockDate').textContent = now.toLocaleDateString('en-PH', {
                    timeZone: 'Asia/Manila',
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                });
            };
            tick();
            setInterval(tick, 1000);

            /* Result overlay -------------------------------------------- */
            const icons = {
                ok: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
                bad: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>',
            };

            function showResult({ ok, title, message, name, id, url, retry }) {
                const box = $('result');
                box.className = 'result ' + (ok ? 'ok' : 'bad');
                $('resultIcon').innerHTML = ok ? icons.ok : icons.bad;
                $('resultTitle').textContent = title;
                $('resultMsg').textContent = message || '';
                $('resultName').hidden = !name;
                $('resultName').textContent = name || '';
                $('resultId').hidden = !id;
                $('resultId').textContent = id ? 'PCG ID · ' + id : '';
                $('resultOpen').hidden = !url;
                if (url) $('resultOpen').href = url;
                $('resultRetry').hidden = !retry;
                box.hidden = false;
                (url ? $('resultOpen') : $('resultRetry')).focus();
            }

            $('resultRetry').addEventListener('click', () => {
                $('result').hidden = true;
                if (!$('facePanel').hidden) startFace();
            });

            function succeed(data, label) {
                const person = data.person || {};
                let seconds = 3;
                const msg = () => `${label} verified. Opening your record in ${seconds}…`;

                showResult({
                    ok: true,
                    title: 'Identity verified',
                    name: person.name,
                    id: person.person_id,
                    message: msg(),
                    url: data.redirect_url,
                });

                const timer = setInterval(() => {
                    seconds--;
                    $('resultMsg').textContent = msg();
                    if (seconds <= 0) {
                        clearInterval(timer);
                        window.location.href = data.redirect_url;
                    }
                }, 1000);
            }

            async function postForm(url, form) {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        Accept: 'application/json',
                    },
                    body: form,
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    data = { message: `Unexpected server response (HTTP ${response.status}).` };
                }

                if (response.status === 419) {
                    data.message = 'This page expired. Reloading…';
                    setTimeout(() => location.reload(), 1200);
                }

                if (response.status === 429) {
                    data.message = 'Too many attempts. Please wait a minute and try again.';
                }

                return { response, data };
            }

            /* Tabs ------------------------------------------------------- */
            function selectTab(face) {
                $('tabFace').setAttribute('aria-selected', String(face));
                $('tabVoice').setAttribute('aria-selected', String(!face));
                $('facePanel').hidden = !face;
                $('voicePanel').hidden = face;
                if (!face) stopFace();
            }

            $('tabFace').addEventListener('click', () => selectTab(true));
            $('tabVoice').addEventListener('click', () => selectTab(false));

            /* FACE ------------------------------------------------------- */
            const stage = $('stage');
            const video = $('camera');
            const canvas = $('frameCanvas');
            const guidance = $('guidance');
            const meter = $('liveMeter');

            let stream = null;
            let loopTimer = null;
            let inFlight = false;
            let readyStreak = 0;
            let verifying = false;

            function setSteps(statuses) {
                ['step1', 'step2', 'step3'].forEach((id, i) => {
                    $(id).dataset.status = statuses[i];
                });
            }

            function setGuidance(text, tone = 'action') {
                guidance.textContent = text;
                guidance.dataset.tone = tone;
            }

            function renderLive(have, need) {
                meter.innerHTML = '';
                for (let i = 0; i < need; i++) {
                    const bar = document.createElement('span');
                    if (i < have) bar.className = 'on';
                    meter.appendChild(bar);
                }
                $('liveCheck').dataset.ok = String(have >= need);
            }

            renderLive(0, 2);

            async function startFace() {
                if (verifying) return;

                $('stageIdle').hidden = true;
                stage.dataset.state = 'aligning';
                setGuidance('Starting camera…');
                setSteps(['active', 'pending', 'pending']);
                readyStreak = 0;
                renderLive(0, 2);

                try {
                    if (!navigator.mediaDevices?.getUserMedia) {
                        throw new Error('This browser cannot open the camera.');
                    }

                    stream = stream || await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                        },
                        audio: false,
                    });

                    video.srcObject = stream;
                    await video.play();

                    setGuidance('Position your face inside the oval.');
                    clearInterval(loopTimer);
                    loopTimer = setInterval(analyzeFrame, 500);
                } catch (error) {
                    stopFace();
                    showResult({
                        ok: false,
                        title: 'Camera unavailable',
                        message: error.name === 'NotAllowedError'
                            ? 'Allow camera access in your browser, then try again.'
                            : (error.message || 'The camera could not be started.'),
                        retry: true,
                    });
                }
            }

            function stopFace() {
                clearInterval(loopTimer);
                loopTimer = null;
                if (stream) stream.getTracks().forEach(t => t.stop());
                stream = null;
                video.srcObject = null;
                stage.dataset.state = 'idle';
                $('stageIdle').hidden = false;
            }

            function grabFrame(width) {
                const vw = video.videoWidth;
                const vh = video.videoHeight;
                if (!vw || !vh) return Promise.resolve(null);

                const scale = Math.min(1, width / vw);
                canvas.width = Math.round(vw * scale);
                canvas.height = Math.round(vh * scale);
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                return new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.88));
            }

            async function analyzeFrame() {
                if (inFlight || verifying || !stream) return;
                inFlight = true;

                try {
                    const blob = await grabFrame(640);
                    if (!blob) return;

                    const form = new FormData();
                    form.append('face_image', blob, 'frame.jpg');

                    const { response, data } = await postForm(routes.analyze, form);

                    if (!response.ok || !data.success) {
                        setGuidance(data.message || 'Face analysis is unavailable. Retrying…', 'warn');
                        stage.dataset.state = 'warn';
                        return;
                    }

                    Object.entries(data.checks).forEach(([key, ok]) => {
                        const row = document.querySelector(`.check[data-check="${key}"]`);
                        if (row) row.dataset.ok = String(ok);
                    });

                    const live = data.liveness;
                    renderLive(live.live_frames, live.live_frames_needed);

                    const stageLabel = {
                        look: 'Liveness · Look',
                        turn: 'Liveness · Turn',
                        return: 'Liveness · Return',
                        done: 'Liveness · Confirming',
                        passed: 'Ready',
                    }[live.stage] || 'Position';

                    $('hudStage').textContent = data.quality_ok ? stageLabel : 'Position';

                    if (!data.quality_ok) {
                        setSteps(['active', live.passed ? 'done' : 'pending', 'pending']);
                    } else if (!live.passed) {
                        setSteps(['done', 'active', 'pending']);
                    } else {
                        setSteps(['done', 'done', 'active']);
                    }

                    setGuidance(data.guidance.message, data.guidance.tone);
                    stage.dataset.state = data.ready
                        ? 'ready'
                        : (data.guidance.tone === 'warn' ? 'warn' : 'aligning');

                    // Auto-capture after two consecutive ready frames.
                    readyStreak = data.ready ? readyStreak + 1 : 0;
                    if (readyStreak >= 2) verifyFace();
                } catch (error) {
                    setGuidance('Connection problem. Retrying…', 'warn');
                } finally {
                    inFlight = false;
                }
            }

            async function verifyFace() {
                if (verifying) return;
                verifying = true;
                clearInterval(loopTimer);

                stage.dataset.state = 'scanning';
                $('hudStage').textContent = 'Matching';
                setGuidance('Hold still — matching your identity…', 'ok');
                setSteps(['done', 'done', 'active']);

                try {
                    const blob = await grabFrame(1280);
                    const form = new FormData();
                    form.append('face_image', blob, 'face.jpg');

                    const { response, data } = await postForm(routes.recognize, form);

                    if (data.matched === true && data.redirect_url) {
                        setSteps(['done', 'done', 'done']);
                        stopFace();
                        succeed(data, 'Face');
                        return;
                    }

                    stopFace();
                    showResult({
                        ok: false,
                        title: response.status === 428 ? 'Liveness not confirmed' : 'Not recognised',
                        message: data.message || 'No matching enrolled record was found.',
                        retry: true,
                    });
                } catch (error) {
                    stopFace();
                    showResult({
                        ok: false,
                        title: 'Verification failed',
                        message: 'The verification service could not be reached. Please try again.',
                        retry: true,
                    });
                } finally {
                    verifying = false;
                }
            }

            $('startFace').addEventListener('click', startFace);

            document.addEventListener('visibilitychange', () => {
                if (document.hidden && stream && !verifying) stopFace();
            });

            /* VOICE ------------------------------------------------------ */
            const mic = $('micButton');
            const wave = $('wave');
            const wctx = wave.getContext('2d');
            const MIN_SECONDS = 3;
            const MAX_SECONDS = 10;

            let recorder = null;
            let micStream = null;
            let chunks = [];
            let voiceBlob = null;
            let audioCtx = null;
            let analyser = null;
            let raf = null;
            let startedAt = 0;
            let voiceUrl = null;

            function drawWave() {
                const data = new Uint8Array(analyser.fftSize);
                analyser.getByteTimeDomainData(data);

                wctx.clearRect(0, 0, wave.width, wave.height);
                wctx.lineWidth = 4;
                wctx.strokeStyle = '#f4c542';
                wctx.beginPath();

                const step = wave.width / data.length;
                data.forEach((v, i) => {
                    const y = (v / 255) * wave.height;
                    if (i === 0) wctx.moveTo(0, y);
                    else wctx.lineTo(i * step, y);
                });
                wctx.stroke();

                const elapsed = (performance.now() - startedAt) / 1000;
                $('voiceTimer').textContent = elapsed.toFixed(1).padStart(4, '0') + ' s';

                if (elapsed >= MAX_SECONDS) {
                    stopRecording();
                    return;
                }

                raf = requestAnimationFrame(drawWave);
            }

            function cleanupMic() {
                cancelAnimationFrame(raf);
                if (micStream) micStream.getTracks().forEach(t => t.stop());
                micStream = null;
                if (audioCtx) audioCtx.close();
                audioCtx = null;
                mic.classList.remove('is-recording');
                mic.setAttribute('aria-label', 'Start recording');
            }

            async function startRecording() {
                voiceBlob = null;
                chunks = [];
                $('verifyVoice').disabled = true;
                $('voicePreview').hidden = true;
                if (voiceUrl) URL.revokeObjectURL(voiceUrl);

                try {
                    if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
                        throw new Error('Audio recording is not supported in this browser.');
                    }

                    micStream = await navigator.mediaDevices.getUserMedia({ audio: true });

                    const options = {};
                    if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
                        options.mimeType = 'audio/webm;codecs=opus';
                    } else if (MediaRecorder.isTypeSupported('audio/ogg;codecs=opus')) {
                        options.mimeType = 'audio/ogg;codecs=opus';
                    }

                    recorder = new MediaRecorder(micStream, options);
                    recorder.ondataavailable = e => {
                        if (e.data && e.data.size) chunks.push(e.data);
                    };
                    recorder.onstop = finishRecording;

                    audioCtx = new AudioContext();
                    analyser = audioCtx.createAnalyser();
                    analyser.fftSize = 1024;
                    audioCtx.createMediaStreamSource(micStream).connect(analyser);

                    recorder.start();
                    startedAt = performance.now();
                    mic.classList.add('is-recording');
                    mic.setAttribute('aria-label', 'Stop recording');
                    $('voiceStatus').textContent = 'Recording… read the sentence clearly, then tap to stop.';
                    drawWave();
                } catch (error) {
                    cleanupMic();
                    $('voiceStatus').textContent = error.name === 'NotAllowedError'
                        ? 'Allow microphone access in your browser, then try again.'
                        : (error.message || 'The microphone could not be started.');
                }
            }

            function stopRecording() {
                if (recorder && recorder.state !== 'inactive') recorder.stop();
            }

            function finishRecording() {
                const seconds = (performance.now() - startedAt) / 1000;
                cleanupMic();

                if (seconds < MIN_SECONDS) {
                    $('voiceStatus').textContent =
                        `Too short (${seconds.toFixed(1)} s). Record at least ${MIN_SECONDS} seconds.`;
                    return;
                }

                voiceBlob = new Blob(chunks, { type: recorder.mimeType || 'audio/webm' });

                if (!voiceBlob.size) {
                    voiceBlob = null;
                    $('voiceStatus').textContent = 'No audio was captured. Please record again.';
                    return;
                }

                voiceUrl = URL.createObjectURL(voiceBlob);
                $('voicePreview').src = voiceUrl;
                $('voicePreview').hidden = false;
                $('verifyVoice').disabled = false;
                $('voiceStatus').textContent = 'Recording ready. Replay it if you like, then verify.';
            }

            mic.addEventListener('click', () => {
                if (recorder && recorder.state === 'recording') stopRecording();
                else startRecording();
            });

            $('verifyVoice').addEventListener('click', async () => {
                if (!voiceBlob) return;

                const button = $('verifyVoice');
                button.disabled = true;
                button.textContent = 'Verifying…';

                try {
                    const type = voiceBlob.type;
                    const ext = type.includes('ogg') ? 'ogg'
                        : type.includes('wav') ? 'wav'
                        : type.includes('mp4') ? 'm4a'
                        : 'webm';

                    const form = new FormData();
                    form.append('voice_audio', voiceBlob, 'voice-sample.' + ext);

                    const { data } = await postForm(routes.voice, form);

                    if (data.matched === true && data.redirect_url) {
                        succeed(data, 'Voice');
                        return;
                    }

                    showResult({
                        ok: false,
                        title: 'Not recognised',
                        message: data.message || 'No matching enrolled voice was found.',
                        retry: true,
                    });
                } catch (error) {
                    showResult({
                        ok: false,
                        title: 'Verification failed',
                        message: 'The verification service could not be reached. Please try again.',
                        retry: true,
                    });
                } finally {
                    button.disabled = false;
                    button.textContent = 'Verify voice';
                }
            });

            window.addEventListener('beforeunload', () => {
                stopFace();
                cleanupMic();
                if (voiceUrl) URL.revokeObjectURL(voiceUrl);
            });
        })();
    </script>
</body>

</html>
