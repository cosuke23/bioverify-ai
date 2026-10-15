<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioVerify AI · Philippine Coast Guard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pcg.css') }}">

    <style>
        /* Public entry screen: same dark operations console as the verification kiosk. */
        :root {
            --k-bg: #050b1a;
            --k-panel: rgba(13, 26, 56, .72);
            --k-line: rgba(148, 178, 230, .16);
            --k-text: #e6eeff;
            --k-dim: #8ea3c8;
            --k-ok: #34d399;
        }

        body.kiosk {
            display: flex;
            flex-direction: column;
            margin: 0;
            min-height: 100vh;
            color: var(--k-text);
            font-family: var(--font-body);
            background:
                radial-gradient(60% 50% at 75% 0%, rgba(0, 51, 160, .5), transparent 70%),
                radial-gradient(50% 40% at 0% 100%, rgba(244, 197, 66, .08), transparent 70%),
                var(--k-bg);
            overflow-x: hidden;
        }

        body.kiosk::before {
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
            gap: 14px;
            padding: 18px 28px;
            border-bottom: 1px solid var(--k-line);
            background: rgba(5, 11, 26, .6);
            backdrop-filter: blur(8px);
        }

        .k-top .rail-crest {
            background: rgba(0, 51, 160, .55);
        }

        .k-brand-title {
            display: block;
            font-family: var(--font-display);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: .02em;
            line-height: 1;
        }

        .k-brand-sub {
            display: block;
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

        .k-main {
            position: relative;
            z-index: 1;
            flex: 1;
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 64px 24px 40px;
        }

        .k-eyebrow {
            font-family: var(--font-display);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--pcg-gold);
        }

        .hero-title {
            max-width: 880px;
            margin: 10px 0 0;
            font-family: var(--font-display);
            font-size: clamp(44px, 7vw, 86px);
            font-weight: 700;
            line-height: .9;
            letter-spacing: .005em;
        }

        .hero-title span {
            color: var(--pcg-gold);
        }

        .hero-lede {
            max-width: 620px;
            margin: 20px 0 0;
            color: var(--k-dim);
            font-size: 17px;
        }

        /* Entry tiles ------------------------------------------ */
        .tiles {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
            gap: 20px;
            margin-top: 44px;
        }

        .tile {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 18px;
            min-height: 250px;
            padding: 28px;
            border: 1px solid var(--k-line);
            border-radius: 18px;
            background: var(--k-panel);
            box-shadow: 0 30px 80px rgba(0, 0, 0, .35);
            backdrop-filter: blur(6px);
            color: inherit;
            text-decoration: none;
            overflow: hidden;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .tile:hover {
            transform: translateY(-3px);
            border-color: rgba(244, 197, 66, .55);
        }

        .tile:focus-visible {
            outline: 3px solid var(--pcg-gold);
            outline-offset: 3px;
        }

        .tile-primary {
            border-color: rgba(244, 197, 66, .45);
            background:
                radial-gradient(90% 120% at 100% 0%, rgba(244, 197, 66, .16), transparent 60%),
                linear-gradient(160deg, rgba(0, 51, 160, .75), rgba(10, 22, 51, .85));
        }

        .tile-primary:hover {
            box-shadow: 0 30px 80px rgba(0, 0, 0, .35), 0 0 0 1px rgba(244, 197, 66, .35), 0 0 60px rgba(244, 197, 66, .12);
        }

        .tile-icon {
            display: grid;
            place-items: center;
            width: 56px;
            height: 56px;
            border-radius: 14px;
            border: 1px solid var(--k-line);
            background: rgba(255, 255, 255, .06);
            color: var(--k-text);
        }

        .tile-primary .tile-icon {
            border-color: rgba(244, 197, 66, .5);
            background: var(--pcg-gold);
            color: var(--pcg-blue-900);
        }

        .tile-icon svg {
            width: 28px;
            height: 28px;
        }

        .tile-kicker {
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--k-dim);
        }

        .tile h2 {
            margin: 4px 0 0;
            font-family: var(--font-display);
            font-size: clamp(30px, 3.4vw, 42px);
            font-weight: 700;
            line-height: 1;
        }

        .tile p {
            max-width: 440px;
            margin: 8px 0 0;
            color: var(--k-dim);
            font-size: 15px;
        }

        .tile-primary p {
            color: rgba(230, 238, 255, .82);
        }

        .tile-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: auto;
            font-weight: 600;
            font-size: 15px;
        }

        .tile-primary .tile-cta {
            align-self: flex-start;
            min-height: 46px;
            padding: 0 20px;
            border-radius: 10px;
            background: var(--pcg-gold);
            color: var(--pcg-blue-900);
        }

        .tile:not(.tile-primary) .tile-cta {
            color: var(--pcg-gold);
        }

        .tile-cta svg {
            width: 18px;
            height: 18px;
            transition: transform .2s ease;
        }

        .tile:hover .tile-cta svg {
            transform: translateX(3px);
        }

        /* Capability strip ------------------------------------- */
        .caps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 28px;
            border: 1px solid var(--k-line);
            border-radius: 14px;
            background: rgba(255, 255, 255, .03);
        }

        .cap {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
        }

        .cap+.cap {
            border-left: 1px solid var(--k-line);
        }

        .cap svg {
            flex: 0 0 22px;
            width: 22px;
            height: 22px;
            color: var(--pcg-gold);
        }

        .cap strong {
            display: block;
            font-size: 14px;
            font-weight: 600;
        }

        .cap span {
            display: block;
            font-size: 12.5px;
            color: var(--k-dim);
        }

        .k-foot {
            position: relative;
            z-index: 1;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px 24px;
            padding: 18px 28px;
            border-top: 1px solid var(--k-line);
            color: rgba(142, 163, 200, .8);
            font-size: 12.5px;
        }

        .k-foot .mono {
            letter-spacing: .06em;
        }

        @media (max-width: 860px) {
            .k-top {
                padding: 14px 16px;
            }

            .k-clock {
                display: none;
            }

            .k-main {
                padding: 36px 16px 32px;
            }

            .tiles,
            .caps {
                grid-template-columns: 1fr;
            }

            .tile {
                min-height: 0;
                padding: 22px;
            }

            .cap+.cap {
                border-left: 0;
                border-top: 1px solid var(--k-line);
            }

            .k-foot {
                padding: 16px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .tile,
            .tile-cta svg {
                transition: none;
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
        <span>
            <span class="k-brand-title">BioVerify AI</span>
            <span class="k-brand-sub">Philippine Coast Guard</span>
        </span>

        <div class="k-clock" aria-hidden="true">
            <div class="k-clock-time" data-clock-time>--:--:--</div>
            <div class="k-clock-date" data-clock-date>&nbsp;</div>
        </div>
    </header>

    <main class="k-main">
        <div class="rise">
            <p class="k-eyebrow">Personnel identity verification</p>
            <h1 class="hero-title">Verify. Authenticate.<br><span>Stand watch.</span></h1>
            <p class="hero-lede">
                BioVerify AI confirms the identity of Philippine Coast Guard personnel using face and voice
                biometrics. Choose how you want to continue.
            </p>
        </div>

        <div class="tiles">
            <a href="{{ route('biometric.login') }}" class="tile tile-primary rise rise-2">
                <span class="tile-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                        <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                        <circle cx="12" cy="11" r="3" />
                        <path d="M7.5 17.5a5 5 0 0 1 9 0" />
                    </svg>
                </span>
                <div>
                    <span class="tile-kicker">For personnel</span>
                    <h2>Verify identity</h2>
                    <p>Look into the camera or read the on-screen phrase aloud. Face verification
                        includes a short look-and-turn liveness check.</p>
                </div>
                <span class="tile-cta">
                    Start verification
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 5l7 7-7 7" />
                    </svg>
                </span>
            </a>

            @php $signedIn = auth()->check(); @endphp
            <a href="{{ $signedIn ? route('people.index') : route('login') }}" class="tile rise rise-3">
                    <span class="tile-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2.5 4 5.5v6c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10v-6z" />
                            <rect x="9" y="10" width="6" height="5" rx="1" />
                            <path d="M10.5 10V8.5a1.5 1.5 0 0 1 3 0V10" />
                        </svg>
                    </span>
                    <div>
                        <span class="tile-kicker">Restricted</span>
                        <h2>{{ $signedIn ? 'Open admin console' : 'Administrator sign-in' }}</h2>
                        <p>Manage personnel records, biometric enrolment, documents and the Pong AI knowledge
                            assistant.</p>
                    </div>
                    <span class="tile-cta">
                        {{ $signedIn ? 'Continue' : 'Sign in' }}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </span>
                </a>
        </div>

        <ul class="caps rise rise-4" aria-label="System capabilities" style="list-style:none;padding:0">
            <li class="cap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="9" r="4" />
                    <path d="M4 21a8 8 0 0 1 16 0" />
                </svg>
                <div><strong>Face verification</strong><span>With liveness check</span></div>
            </li>
            <li class="cap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 10v4M6 6v12M10 3v18M14 8v8M18 5v14M22 10v4" />
                </svg>
                <div><strong>Voice verification</strong><span>Speaker matching</span></div>
            </li>
            <li class="cap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <path d="M14 2v6h6" />
                    <path d="m9 15 2 2 4-4" />
                </svg>
                <div><strong>Audit-logged</strong><span>Every attempt is recorded</span></div>
            </li>
        </ul>
    </main>

    <footer class="k-foot">
        <span>Authorised use only. Biometric data is processed solely for personnel identity verification.</span>
        <span class="mono">BIOVERIFY AI · PCG</span>
    </footer>

    <script>
        (function () {
            'use strict';
            const t = document.querySelector('[data-clock-time]');
            const d = document.querySelector('[data-clock-date]');
            if (!t || !d) return;
            const tf = new Intl.DateTimeFormat('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Manila' });
            const df = new Intl.DateTimeFormat('en-PH', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric', timeZone: 'Asia/Manila' });
            function tick() {
                const now = new Date();
                t.textContent = tf.format(now);
                d.textContent = df.format(now) + ' · PHT';
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
</body>

</html>
