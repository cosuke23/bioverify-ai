<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Sign-in · BioVerify AI · Philippine Coast Guard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pcg.css') }}">

    <style>
        /* Same dark operations-console language as the verification kiosk. */
        :root {
            --k-bg: #050b1a;
            --k-panel: rgba(13, 26, 56, .72);
            --k-line: rgba(148, 178, 230, .16);
            --k-text: #e6eeff;
            --k-dim: #8ea3c8;
            --k-bad: #f87171;
        }

        body.kiosk {
            margin: 0;
            min-height: 100vh;
            color: var(--k-text);
            font-family: var(--font-body);
            background:
                radial-gradient(60% 50% at 80% 0%, rgba(0, 51, 160, .45), transparent 70%),
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

        .auth {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
            min-height: 100vh;
        }

        /* Brand panel ------------------------------------------ */
        .auth-brand {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 40px;
            padding: 44px 56px;
            border-right: 1px solid var(--k-line);
            overflow: hidden;
        }

        .auth-brand::after {
            /* gold signal line */
            content: '';
            position: absolute;
            inset: 0 0 0 auto;
            width: 2px;
            background: linear-gradient(180deg, var(--pcg-gold), rgba(244, 197, 66, 0) 70%);
            opacity: .6;
        }

        .auth-mark {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
        }

        .auth-mark .rail-crest {
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

        .k-eyebrow {
            font-family: var(--font-display);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--pcg-gold);
        }

        .auth-headline {
            margin: 10px 0 0;
            font-family: var(--font-display);
            font-size: clamp(40px, 5.2vw, 68px);
            font-weight: 700;
            line-height: .92;
            letter-spacing: .005em;
        }

        .auth-headline span {
            color: var(--pcg-gold);
        }

        .auth-lede {
            max-width: 480px;
            margin: 18px 0 0;
            color: var(--k-dim);
            font-size: 15.5px;
        }

        .auth-points {
            display: grid;
            gap: 12px;
            max-width: 480px;
            margin: 28px 0 0;
            padding: 0;
            list-style: none;
        }

        .auth-points li {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border: 1px solid var(--k-line);
            border-radius: 12px;
            background: rgba(255, 255, 255, .03);
            font-size: 14px;
            color: var(--k-text);
        }

        .auth-points svg {
            flex: 0 0 20px;
            width: 20px;
            height: 20px;
            margin-top: 1px;
            color: var(--pcg-gold);
        }

        .auth-points small {
            display: block;
            margin-top: 2px;
            color: var(--k-dim);
            font-size: 13px;
        }

        .auth-foot {
            font-family: var(--font-mono);
            font-size: 11.5px;
            letter-spacing: .04em;
            color: rgba(142, 163, 200, .75);
        }

        /* Sign-in card ----------------------------------------- */
        .auth-side {
            display: grid;
            place-items: center;
            padding: 44px 32px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            padding: 34px 32px 28px;
            border: 1px solid var(--k-line);
            border-radius: 18px;
            background: var(--k-panel);
            box-shadow: 0 30px 80px rgba(0, 0, 0, .35);
            backdrop-filter: blur(6px);
        }

        .auth-card h2 {
            margin: 6px 0 0;
            font-family: var(--font-display);
            font-size: 34px;
            font-weight: 700;
            line-height: 1;
        }

        .auth-card .sub {
            margin: 8px 0 24px;
            color: var(--k-dim);
            font-size: 14px;
        }

        .k-field {
            display: grid;
            gap: 7px;
            margin-bottom: 16px;
        }

        .k-label {
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--k-dim);
        }

        .k-input-wrap {
            position: relative;
        }

        .k-input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            width: 18px;
            height: 18px;
            transform: translateY(-50%);
            color: var(--k-dim);
            pointer-events: none;
        }

        .k-input {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px 12px 44px;
            border: 1px solid rgba(148, 178, 230, .24);
            border-radius: 10px;
            background: rgba(5, 11, 26, .55);
            color: var(--k-text);
            font: 400 15px/1.4 var(--font-body);
            transition: border-color .15s, box-shadow .15s;
        }

        .k-input::placeholder {
            color: rgba(142, 163, 200, .55);
        }

        .k-input:focus {
            outline: none;
            border-color: var(--pcg-gold);
            box-shadow: 0 0 0 4px rgba(244, 197, 66, .14);
        }

        .k-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 4px 0 22px;
            color: var(--k-dim);
            font-size: 14px;
            cursor: pointer;
        }

        .k-check input {
            width: 17px;
            height: 17px;
            accent-color: var(--pcg-gold);
        }

        .auth-submit {
            width: 100%;
            min-height: 50px;
            font-size: 15px;
        }

        .k-alert {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 18px;
            padding: 12px 14px;
            border: 1px solid rgba(248, 113, 113, .4);
            border-radius: 10px;
            background: rgba(200, 16, 46, .14);
            color: #fecaca;
            font-size: 14px;
        }

        .k-alert svg {
            flex: 0 0 18px;
            width: 18px;
            height: 18px;
            margin-top: 1px;
            color: var(--k-bad);
        }

        .auth-alt {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid var(--k-line);
            color: var(--k-dim);
            font-size: 13.5px;
        }

        .auth-alt a {
            color: var(--pcg-gold);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-alt a:hover {
            text-decoration: underline;
        }

        .auth-alt svg {
            flex: 0 0 20px;
            width: 20px;
            height: 20px;
            color: var(--k-dim);
        }

        .auth-notice {
            max-width: 420px;
            margin: 16px auto 0;
            text-align: center;
            color: rgba(142, 163, 200, .7);
            font-size: 12px;
        }

        @media (max-width: 900px) {
            .auth {
                grid-template-columns: 1fr;
            }

            .auth-brand {
                padding: 28px 20px 8px;
                border-right: 0;
                gap: 20px;
            }

            .auth-brand::after,
            .auth-points,
            .auth-foot {
                display: none;
            }

            .auth-headline {
                font-size: 40px;
            }

            .auth-side {
                padding: 20px 16px 40px;
                align-items: start;
            }

            .auth-card {
                padding: 26px 20px 22px;
            }
        }
    </style>
</head>

<body class="kiosk pcg">
    <div class="auth">
        <section class="auth-brand" aria-labelledby="brand-heading">
            <a href="{{ url('/') }}" class="auth-mark">
                <span class="rail-crest" aria-hidden="true">
                    {{-- Generic shield-and-anchor mark (not the official PCG seal). --}}
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2.5 4 5.5v6c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10v-6z" />
                        <circle cx="12" cy="7.8" r="1.4" />
                        <path d="M12 9.2v8" />
                        <path d="M9.5 11.5h5" />
                        <path d="M8 14.6c.6 1.6 2.2 2.6 4 2.6s3.4-1 4-2.6" />
                    </svg>
                </span>
                <span>
                    <span class="k-brand-title" style="display:block">BioVerify AI</span>
                    <span class="k-brand-sub" style="display:block">Philippine Coast Guard</span>
                </span>
            </a>

            <div class="rise">
                <p class="k-eyebrow">Restricted system · Administration</p>
                <h1 id="brand-heading" class="auth-headline">Authorised<br><span>personnel only.</span></h1>
                <p class="auth-lede">
                    This console manages biometric enrolment, identity verification and the knowledge base
                    for Philippine Coast Guard personnel. Access is limited to designated administrators.
                </p>

                <ul class="auth-points">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                            <path d="M8 13h8M8 17h5" />
                        </svg>
                        <span>Verification activity is audit-logged
                            <small>Every face and voice verification attempt and its outcome is recorded for review.</small></span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <span>Biometric data stays protected
                            <small>Face and voice records are only reachable from this console.</small></span>
                    </li>
                </ul>
            </div>

            <p class="auth-foot">BIOVERIFY-AI · ADMIN CONSOLE · UNAUTHORISED ACCESS IS PROHIBITED</p>
        </section>

        <main class="auth-side">
            <div style="width:100%;max-width:420px">
                <div class="auth-card rise rise-2">
                    <p class="k-eyebrow">Administrator</p>
                    <h2>Sign in</h2>
                    <p class="sub">Use your issued administrator credentials.</p>

                    @if ($errors->any())
                        <div role="alert" class="k-alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4M12 16h.01" />
                            </svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="k-field">
                            <label for="email" class="k-label">Email address</label>
                            <div class="k-input-wrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                    <path d="m22 7-10 6L2 7" />
                                </svg>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username" class="k-input" placeholder="name@coastguard.gov.ph">
                            </div>
                        </div>

                        <div class="k-field">
                            <label for="password" class="k-label">Password</label>
                            <div class="k-input-wrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                <input id="password" name="password" type="password" required
                                    autocomplete="current-password" class="k-input">
                            </div>
                        </div>

                        <label class="k-check">
                            <input type="checkbox" name="remember" value="1">
                            Keep me signed in on this device
                        </label>

                        <button type="submit" class="btn btn-gold auth-submit">
                            Sign in to console
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </button>
                    </form>

                    <div class="auth-alt">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2" />
                            <circle cx="12" cy="11" r="3" />
                            <path d="M7.5 17.5a5 5 0 0 1 9 0" />
                        </svg>
                        <span>Personnel verifying their identity should use
                            <a href="{{ route('biometric.login') }}">biometric verification</a>.</span>
                    </div>
                </div>

                <p class="auth-notice">Repeated failed sign-in attempts are rate-limited.</p>
            </div>
        </main>
    </div>
</body>

</html>
