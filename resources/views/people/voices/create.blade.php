@extends('layouts.pcg')

@section('title', 'Record voice · ' . $person->full_name)

@php
    $initials = collect(explode(' ', (string) $person->full_name))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');

    $voiceCount = $person->voices()->count();

    // Client-side limits. The AI service rejects audio under 2 s or over 30 s.
    $minSeconds = 3;
    $maxSeconds = 30;
    $idealSeconds = 10;
@endphp

@section('crumbs')
    <a href="{{ route('people.index') }}">Personnel</a>
    <span aria-hidden="true">/</span>
    <a href="{{ route('people.show', $person) }}" class="truncate">{{ $person->full_name }}</a>
    <span aria-hidden="true">/</span>
    <strong>Add voice</strong>
@endsection

@push('head')
    <style>
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

        /* Reading prompt */
        .prompt {
            position: relative;
            margin: 0 0 22px;
            padding: 18px 20px 18px 24px;
            border: 1px solid rgba(244, 197, 66, .55);
            border-radius: 14px;
            background: var(--pcg-gold-soft);
        }

        .prompt::before {
            content: '';
            position: absolute;
            inset: 12px auto 12px 0;
            width: 4px;
            border-radius: 0 4px 4px 0;
            background: var(--pcg-gold);
        }

        .prompt-label {
            font-family: var(--font-display);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--pcg-blue);
        }

        .prompt-text {
            margin: 6px 0 0;
            font-family: var(--font-display);
            font-size: clamp(22px, 2.6vw, 28px);
            font-weight: 600;
            line-height: 1.2;
            color: var(--pcg-blue-900);
        }

        /* Recorder console */
        .recorder {
            padding: 28px 24px 24px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background:
                radial-gradient(90% 70% at 50% 0%, rgba(0, 51, 160, .06), transparent 70%),
                var(--canvas);
            text-align: center;
        }

        .mic-disc {
            display: grid;
            place-items: center;
            width: 112px;
            height: 112px;
            margin: 0 auto;
            border-radius: 50%;
            background: radial-gradient(circle at 50% 35%, var(--pcg-blue), var(--pcg-blue-800));
            color: var(--pcg-gold);
            box-shadow: 0 0 0 6px var(--surface), 0 0 0 8px rgba(244, 197, 66, .7), var(--shadow);
            transition: background .2s, color .2s, box-shadow .2s;
        }

        .mic-disc svg {
            width: 44px;
            height: 44px;
        }

        .mic-disc.recording-pulse {
            background: radial-gradient(circle at 50% 35%, #e3344f, var(--danger));
            color: #fff;
            box-shadow: 0 0 0 6px var(--surface), 0 0 0 8px rgba(200, 16, 46, .45), 0 0 34px rgba(200, 16, 46, .35);
        }

        .recording-pulse {
            animation: recordingPulse 1.2s infinite;
        }

        @keyframes recordingPulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.08);
                opacity: .75;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .rec-status {
            margin: 18px 0 0;
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--ink);
        }

        .rec-instruction {
            margin: 6px auto 0;
            max-width: 460px;
            font-size: 14px;
            color: var(--muted);
        }

        .rec-timer {
            margin-top: 14px;
            font-family: var(--font-mono);
            font-size: 44px;
            font-weight: 500;
            letter-spacing: .04em;
            line-height: 1;
            color: var(--pcg-blue-900);
        }

        /* Input level */
        .level {
            display: flex;
            gap: 3px;
            justify-content: center;
            height: 26px;
            margin: 18px auto 0;
            max-width: 420px;
        }

        .level span {
            flex: 1;
            border-radius: 2px;
            background: var(--line);
            transition: background .08s;
        }

        .level span.on {
            background: var(--ok);
        }

        .level span.on.hot {
            background: var(--pcg-gold-600);
        }

        .level span.on.clip {
            background: var(--danger);
        }

        /* Duration track */
        .duration {
            max-width: 420px;
            margin: 16px auto 0;
            text-align: left;
        }

        .duration-track {
            position: relative;
            height: 8px;
            border-radius: 4px;
            background: var(--line-soft);
            border: 1px solid var(--line);
        }

        .duration-fill {
            position: absolute;
            inset: 0 auto 0 0;
            width: 0;
            border-radius: 4px;
            background: var(--pcg-gold);
            transition: width .3s linear, background .2s;
        }

        .duration-fill.is-enough {
            background: var(--ok);
        }

        .duration-mark {
            position: absolute;
            top: -4px;
            bottom: -4px;
            width: 2px;
            background: var(--ink-2);
            opacity: .45;
        }

        .duration-labels {
            position: relative;
            height: 16px;
            margin-top: 6px;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--muted);
        }

        .duration-labels span {
            position: absolute;
            top: 0;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .duration-labels span:first-child {
            transform: none;
        }

        .duration-labels span:last-child {
            transform: translateX(-100%);
        }

        .rec-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 22px;
        }

        .btn-record {
            min-height: 48px;
            padding: 0 24px;
            font-size: 15px;
        }

        .btn-stop {
            min-height: 48px;
            padding: 0 24px;
            font-size: 15px;
            background: var(--danger);
            color: #fff;
        }

        .btn-stop:hover:not(:disabled) {
            background: #a00d25;
        }

        button:disabled {
            cursor: not-allowed;
        }

        .preview {
            margin-top: 22px;
            padding: 16px 18px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--surface);
            text-align: left;
        }

        .preview-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .preview audio {
            width: 100%;
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

        .spec {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 8px 14px;
            margin: 0;
            font-size: 13.5px;
        }

        .spec dt {
            font-family: var(--font-display);
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .spec dd {
            margin: 0;
            font-family: var(--font-mono);
            font-size: 12.5px;
            color: var(--ink);
            text-align: right;
        }

        @media (prefers-reduced-motion: reduce) {
            .recording-pulse {
                animation: none;
            }
        }
    </style>
@endpush

@section('content')
    <header class="page-head rise">
        <div>
            <div class="eyebrow">Biometric enrolment · Voice</div>
            <h1 class="page-title">Record voice sample</h1>
            <p class="page-lede">
                Record the person reading the sentence below. Listen to the complete recording before
                saving. The AI service then generates the voice print used for verification.
            </p>
        </div>

        <div class="person-chip">
            <span class="person-avatar" aria-hidden="true">{{ strtoupper($initials ?: 'PC') }}</span>
            <span class="min-w-0">
                <span class="block truncate font-bold text-slate-900 leading-tight">{{ $person->full_name }}</span>
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
        {{-- Recorder ----------------------------------------------------- --}}
        <form method="POST" action="{{ route('people.voices.store', $person) }}" enctype="multipart/form-data"
            id="voiceForm" class="card rise rise-2">
            @csrf

            <div class="card-head">
                <div>
                    <h2 class="card-title">Voice recorder</h2>
                    <p class="muted text-sm m-0">Check the microphone is connected before you start.</p>
                </div>
                <span class="tag">WAV · 16 kHz · mono</span>
            </div>

            <div class="card-pad">
                @if ($errors->any())
                    <div class="alert alert-danger mb-5" role="alert">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                            class="shrink-0 mt-0.5">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 8v4" />
                            <path d="M12 16h.01" />
                        </svg>
                        <div>
                            <p class="font-semibold m-0">The voice sample was not enrolled.</p>
                            <ul class="m-0 mt-1 pl-4 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="prompt">
                    <div class="prompt-label">Read this sentence aloud</div>
                    <p class="prompt-text">
                        “I am verifying my identity for the Philippine Coast Guard BioVerify system.”
                    </p>
                </div>

                <div class="recorder">
                    <div id="microphoneIcon" class="mic-disc" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="2" width="6" height="12" rx="3" />
                            <path d="M5 10a7 7 0 0 0 14 0" />
                            <path d="M12 19v3" />
                        </svg>
                    </div>

                    <h3 id="recordingStatus" class="rec-status" role="status" aria-live="polite">
                        Ready to Record
                    </h3>

                    <p id="recordingInstruction" class="rec-instruction" aria-live="polite">
                        Click Start Recording and speak clearly into the microphone.
                    </p>

                    <div id="recordingTimer" class="rec-timer">00:00</div>

                    <div class="level" id="levelMeter" aria-hidden="true" title="Input level"></div>

                    <div class="duration" aria-hidden="true">
                        <div class="duration-track">
                            <div class="duration-fill" id="durationFill"></div>
                            <span class="duration-mark" style="left: {{ $minSeconds / $maxSeconds * 100 }}%"></span>
                            <span class="duration-mark" style="left: {{ $idealSeconds / $maxSeconds * 100 }}%"></span>
                        </div>
                        <div class="duration-labels">
                            <span style="left:0">0 s</span>
                            <span style="left: {{ $minSeconds / $maxSeconds * 100 }}%">{{ $minSeconds }} s min</span>
                            <span style="left: {{ $idealSeconds / $maxSeconds * 100 }}%">{{ $idealSeconds }} s ideal</span>
                            <span style="left:100%">{{ $maxSeconds }} s max</span>
                        </div>
                    </div>

                    <div class="rec-actions">
                        <button type="button" id="startRecording" class="btn btn-primary btn-record">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="9" y="2" width="6" height="12" rx="3" />
                                <path d="M5 10a7 7 0 0 0 14 0" />
                                <path d="M12 19v3" />
                            </svg>
                            Start Recording
                        </button>

                        <button type="button" id="stopRecording" disabled class="btn btn-stop">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <rect x="6" y="6" width="12" height="12" rx="2" />
                            </svg>
                            Stop Recording
                        </button>
                    </div>

                    <div id="audioPreviewContainer" class="hidden">
                        <div class="preview">
                            <div class="preview-head">
                                <span class="font-semibold text-slate-800">Recorded voice</span>
                                <button type="button" id="recordAgain" class="btn btn-ghost btn-sm">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M1 4v6h6" />
                                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
                                    </svg>
                                    Record Again
                                </button>
                            </div>
                            <audio id="audioPreview" controls preload="metadata"></audio>
                            <p class="muted text-xs mt-2 mb-0">Saving unlocks after the recording has played to the end.</p>
                        </div>
                    </div>
                </div>

                {{-- Recorded WAV is attached here on submit. --}}
                <input type="file" name="voice_audio" id="voice_audio" class="hidden"
                    accept="audio/webm,audio/ogg,audio/*">
            </div>

            <div class="card-foot">
                <label class="check-inline">
                    <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary'))>
                    <span>
                        <span class="font-semibold text-slate-800 block">Set as primary voice sample</span>
                        <span class="muted text-xs">Preferred when matching this person's voice.</span>
                    </span>
                </label>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('recognized.person', $person) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" id="saveVoice" disabled class="btn btn-primary">
                        Save Voice Sample
                    </button>
                </div>
            </div>
        </form>

        {{-- Side column -------------------------------------------------- --}}
        <aside class="side-stack rise rise-3">
            <section class="card">
                <div class="card-head">
                    <h2 class="card-title">Recording tips</h2>
                </div>
                <div class="card-pad">
                    <ul class="tips">
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M11 5L6 9H2v6h4l5 4V5z" />
                                    <path d="M23 9l-6 6M17 9l6 6" />
                                </svg></span>
                            <span><strong>Quiet room</strong>Close doors and windows. Turn off fans, radios and
                                engines nearby.</span>
                        </li>
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="13" r="8" />
                                    <path d="M12 9v4l2 2" />
                                    <path d="M9 2h6" />
                                </svg></span>
                            <span><strong>3–10 seconds</strong>Read the whole sentence once. Recording stops
                                automatically at {{ $maxSeconds }} s.</span>
                        </li>
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M2 12h3l3-8 4 16 3-8h7" />
                                </svg></span>
                            <span><strong>Normal pace and volume</strong>Speak naturally, as you would on duty. Don't
                                whisper or shout.</span>
                        </li>
                        <li>
                            <span class="tip-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="9" y="2" width="6" height="12" rx="3" />
                                    <path d="M5 10a7 7 0 0 0 14 0" />
                                </svg></span>
                            <span><strong>Steady distance</strong>Keep the microphone 20–30 cm from the mouth. Watch the
                                level meter stay green.</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="card">
                <div class="card-head">
                    <h2 class="card-title">Sample specification</h2>
                    <span class="badge {{ $voiceCount >= 2 ? 'badge-ok' : 'badge-warn' }}">{{ $voiceCount }} on file</span>
                </div>
                <div class="card-pad">
                    <dl class="spec">
                        <dt>Format</dt>
                        <dd>PCM WAV, 16-bit</dd>
                        <dt>Sample rate</dt>
                        <dd>16 kHz mono</dd>
                        <dt>Duration</dt>
                        <dd>{{ $minSeconds }}–{{ $maxSeconds }} s</dd>
                        <dt>Review</dt>
                        <dd>full playback required</dd>
                    </dl>
                    <p class="muted text-sm mt-4 mb-0">
                        Enrol two or three samples on different days for more reliable voice matching.
                    </p>
                </div>
            </section>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        /*
        |--------------------------------------------------------------------------
        | WAV VOICE RECORDER
        |--------------------------------------------------------------------------
        |
        | Records microphone audio as:
        |
        | PCM WAV
        | 16-bit
        | Mono
        | 16 kHz
        |
        | This is much more suitable for voice recognition than WebM/Opus.
        |
        */

        let audioContext = null;
        let mediaStream = null;
        let sourceNode = null;
        let processorNode = null;
        let audioSamples = [];
        let isRecording = false;
        let recordingSeconds = 0;
        let timerInterval = null;
        let recordedBlob = null;
        // The user must listen to the complete recording before saving.
        let previewHasFinished = false;
        let audioObjectUrl = null;

        /*
        |--------------------------------------------------------------------------
        | DOM ELEMENTS
        |--------------------------------------------------------------------------
        */

        const startButton = document.getElementById('startRecording');
        const stopButton = document.getElementById('stopRecording');
        const recordAgainButton = document.getElementById('recordAgain');
        const audioPreview = document.getElementById('audioPreview');
        const audioPreviewContainer = document.getElementById('audioPreviewContainer');
        const saveButton = document.getElementById('saveVoice');
        const voiceAudioInput = document.getElementById('voice_audio');
        const recordingStatus = document.getElementById('recordingStatus');
        const recordingInstruction = document.getElementById('recordingInstruction');
        const recordingTimer = document.getElementById('recordingTimer');
        const microphoneIcon = document.getElementById('microphoneIcon');
        const voiceForm = document.getElementById('voiceForm');

        // Display-only helpers (level meter and duration track).
        const levelMeter = document.getElementById('levelMeter');
        const durationFill = document.getElementById('durationFill');

        /*
        |--------------------------------------------------------------------------
        | SETTINGS
        |--------------------------------------------------------------------------
        */

        const TARGET_SAMPLE_RATE = 16000;

        // The AI service rejects audio shorter than 2 s or longer than 30 s.
        const MIN_RECORDING_SECONDS = @json($minSeconds);
        const MAX_RECORDING_SECONDS = @json($maxSeconds);

        /*
        |--------------------------------------------------------------------------
        | LEVEL METER (display only)
        |--------------------------------------------------------------------------
        */

        const LEVEL_SEGMENTS = 28;
        const levelBars = [];

        for (let i = 0; i < LEVEL_SEGMENTS; i++) {
            const bar = document.createElement('span');
            if (i >= LEVEL_SEGMENTS * 0.92) bar.classList.add('clip');
            else if (i >= LEVEL_SEGMENTS * 0.72) bar.classList.add('hot');
            levelMeter.appendChild(bar);
            levelBars.push(bar);
        }

        function updateLevel(peak) {
            // Perceptual scale so normal speech fills roughly half the meter.
            const level = Math.min(1, Math.sqrt(Math.max(0, peak)));
            const lit = Math.round(level * LEVEL_SEGMENTS);

            for (let i = 0; i < LEVEL_SEGMENTS; i++) {
                levelBars[i].classList.toggle('on', i < lit);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | START RECORDING
        |--------------------------------------------------------------------------
        */

        startButton.addEventListener('click', async function() {
            try {
                /*
                | Check browser support
                */
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Your browser does not support microphone recording.');
                    return;
                }

                /*
                | Request microphone
                */
                mediaStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        echoCancellation: false,
                        noiseSuppression: false,
                        autoGainControl: false
                    }
                });

                console.log('Microphone access granted.');

                /*
                | Create AudioContext
                */
                audioContext = new(window.AudioContext || window.webkitAudioContext)();

                /*
                | Resume AudioContext
                */
                if (audioContext.state === 'suspended') {
                    await audioContext.resume();
                }

                console.log('AudioContext sample rate:', audioContext.sampleRate);

                /*
                | Create microphone source
                */
                sourceNode = audioContext.createMediaStreamSource(mediaStream);

                /*
                | Create processor
                |
                | 4096 buffer
                | 1 input channel
                | 1 output channel
                */
                processorNode = audioContext.createScriptProcessor(4096, 1, 1);

                /*
                | Reset samples
                */
                audioSamples = [];

                /*
                | Receive microphone samples
                */
                processorNode.onaudioprocess = function(event) {
                    if (!isRecording) {
                        return;
                    }

                    const inputData = event.inputBuffer.getChannelData(0);

                    /*
                    | Copy samples
                    */
                    const samples = new Float32Array(inputData.length);
                    samples.set(inputData);
                    audioSamples.push(samples);

                    /*
                    | Level meter (display only)
                    */
                    let peak = 0;
                    for (let i = 0; i < inputData.length; i++) {
                        const value = Math.abs(inputData[i]);
                        if (value > peak) peak = value;
                    }
                    updateLevel(peak);
                };

                /*
                | Connect microphone
                */
                sourceNode.connect(processorNode);

                /*
                | IMPORTANT
                |
                | Connect processor to destination so
                | Chrome continues processing audio.
                |
                | The gain is ZERO so the user does not
                | hear their own microphone.
                */
                const silentGain = audioContext.createGain();
                silentGain.gain.value = 0;
                processorNode.connect(silentGain);
                silentGain.connect(audioContext.destination);

                /*
                | Start
                */
                isRecording = true;
                recordingSeconds = 0;
                updateTimer();

                startButton.disabled = true;
                // Stop unlocks once the minimum duration is reached.
                stopButton.disabled = true;
                saveButton.disabled = true;

                microphoneIcon.classList.add('recording-pulse');

                recordingStatus.textContent = 'Recording...';
                recordingInstruction.textContent =
                    'Speak clearly into the microphone. Keep reading for at least ' +
                    MIN_RECORDING_SECONDS + ' seconds.';

                /*
                | Timer
                */
                timerInterval = setInterval(function() {
                    recordingSeconds++;
                    updateTimer();

                    /*
                    | Minimum duration reached
                    */
                    if (recordingSeconds === MIN_RECORDING_SECONDS) {
                        stopButton.disabled = false;
                        recordingInstruction.textContent =
                            'Minimum length reached. Finish the sentence, then click Stop Recording.';
                    }

                    /*
                    | Maximum duration
                    */
                    if (recordingSeconds >= MAX_RECORDING_SECONDS) {
                        stopRecording();
                    }
                }, 1000);

                console.log('WAV recording started.');
            } catch (error) {
                console.error('Microphone error:', error);

                if (error.name === 'NotAllowedError') {
                    alert('Microphone permission was denied. Please allow microphone access in Chrome.');
                } else if (error.name === 'NotFoundError') {
                    alert('No microphone was found.');
                } else {
                    alert('Unable to access microphone: ' + error.message);
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | STOP RECORDING
        |--------------------------------------------------------------------------
        */

        stopButton.addEventListener('click', function() {
            stopRecording();
        });

        function stopRecording() {
            if (!isRecording) {
                return;
            }

            console.log('Stopping WAV recording...');

            /*
            | Stop state first
            */
            isRecording = false;

            /*
            | Stop timer
            */
            stopTimer();
            updateLevel(0);

            /*
            | Stop processor
            */
            if (processorNode) {
                processorNode.disconnect();
                processorNode.onaudioprocess = null;
                processorNode = null;
            }

            /*
            | Stop source
            */
            if (sourceNode) {
                sourceNode.disconnect();
                sourceNode = null;
            }

            /*
            | Stop microphone
            */
            stopMicrophone();

            /*
            | Create WAV
            */
            createWavRecording();
        }

        /*
        |--------------------------------------------------------------------------
        | STOP MICROPHONE
        |--------------------------------------------------------------------------
        */

        function stopMicrophone() {
            if (mediaStream) {
                mediaStream.getTracks().forEach(function(track) {
                    track.stop();
                });

                mediaStream = null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE WAV
        |--------------------------------------------------------------------------
        */

        function createWavRecording() {
            console.log('Audio sample buffers:', audioSamples.length);

            if (!audioSamples || audioSamples.length === 0) {
                alert('No audio samples were captured. Please try again.');
                resetRecordingUI();
                return;
            }

            /*
            | Calculate total samples
            */
            let totalLength = 0;

            for (let i = 0; i < audioSamples.length; i++) {
                totalLength += audioSamples[i].length;
            }

            console.log('Total PCM samples:', totalLength);

            /*
            | Merge Float32 arrays
            */
            const mergedSamples = new Float32Array(totalLength);
            let offset = 0;

            for (let i = 0; i < audioSamples.length; i++) {
                mergedSamples.set(audioSamples[i], offset);
                offset += audioSamples[i].length;
            }

            /*
            | Original sample rate
            */
            const originalSampleRate = audioContext.sampleRate;

            console.log('Original sample rate:', originalSampleRate);

            /*
            | Resample to 16 kHz
            */
            const resampledSamples = resampleAudio(mergedSamples, originalSampleRate, TARGET_SAMPLE_RATE);

            console.log('Target sample rate:', TARGET_SAMPLE_RATE);
            console.log('Resampled samples:', resampledSamples.length);

            /*
            | Convert to WAV
            */
            recordedBlob = encodeWAV(resampledSamples, TARGET_SAMPLE_RATE);

            console.log('================================');
            console.log('FINAL WAV RECORDING');
            console.log('Type:', recordedBlob.type);
            console.log('Size:', recordedBlob.size);
            console.log('Duration:', (resampledSamples.length / TARGET_SAMPLE_RATE).toFixed(2) + ' seconds');
            console.log('================================');

            /*
            | Validate
            */
            if (!recordedBlob || recordedBlob.size === 0) {
                alert('The WAV recording is empty. Please try again.');
                resetRecordingUI();
                return;
            }

            /*
            | Create preview URL
            */
            if (audioObjectUrl) {
                URL.revokeObjectURL(audioObjectUrl);
            }

            audioObjectUrl = URL.createObjectURL(recordedBlob);

            /*
            | Set audio preview
            */
            previewHasFinished = false;
            saveButton.disabled = true;
            audioPreview.src = audioObjectUrl;
            audioPreview.controls = true;
            audioPreview.preload = 'metadata';
            audioPreview.load();

            /*
            | Show preview
            */
            audioPreviewContainer.classList.remove('hidden');

            /*
            | Update UI
            */
            recordingStatus.textContent = 'Recording Complete';
            recordingInstruction.textContent = 'Play the recording below before saving.';

            startButton.disabled = false;
            stopButton.disabled = true;
            // Saving is enabled only after the full preview has played.
            saveButton.disabled = true;

            microphoneIcon.classList.remove('recording-pulse');

            /*
            | Close AudioContext
            */
            if (audioContext) {
                audioContext.close();
                audioContext = null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RESET RECORDING UI
        |--------------------------------------------------------------------------
        |
        | Called when a recording produced no usable audio.
        */

        function resetRecordingUI() {
            isRecording = false;
            stopTimer();
            updateLevel(0);
            stopMicrophone();

            if (audioContext) {
                audioContext.close();
                audioContext = null;
            }

            audioSamples = [];
            recordedBlob = null;
            previewHasFinished = false;
            recordingSeconds = 0;
            updateTimer();

            startButton.disabled = false;
            stopButton.disabled = true;
            saveButton.disabled = true;

            microphoneIcon.classList.remove('recording-pulse');

            recordingStatus.textContent = 'Ready to Record';
            recordingInstruction.textContent = 'Click Start Recording and speak clearly into the microphone.';
        }

        /*
        |--------------------------------------------------------------------------
        | RESAMPLE AUDIO
        |--------------------------------------------------------------------------
        */

        function resampleAudio(inputSamples, inputSampleRate, outputSampleRate) {
            if (inputSampleRate === outputSampleRate) {
                return inputSamples;
            }

            const ratio = inputSampleRate / outputSampleRate;
            const newLength = Math.round(inputSamples.length / ratio);
            const output = new Float32Array(newLength);

            /*
            | Linear interpolation
            */
            for (let i = 0; i < newLength; i++) {
                const position = i * ratio;
                const index = Math.floor(position);
                const fraction = position - index;
                const sample1 = inputSamples[index] || 0;
                const sample2 = inputSamples[index + 1] || sample1;

                output[i] = sample1 + (sample2 - sample1) * fraction;
            }

            return output;
        }

        /*
        |--------------------------------------------------------------------------
        | ENCODE WAV
        |--------------------------------------------------------------------------
        */

        function encodeWAV(samples, sampleRate) {
            const buffer = new ArrayBuffer(44 + samples.length * 2);
            const view = new DataView(buffer);

            function writeString(offset, string) {
                for (let i = 0; i < string.length; i++) {
                    view.setUint8(offset + i, string.charCodeAt(i));
                }
            }

            // RIFF header
            writeString(0, 'RIFF');
            view.setUint32(4, 36 + samples.length * 2, true);
            writeString(8, 'WAVE');

            // fmt chunk
            writeString(12, 'fmt ');
            view.setUint32(16, 16, true); // PCM chunk size
            view.setUint16(20, 1, true); // PCM format
            view.setUint16(22, 1, true); // Mono
            view.setUint32(24, sampleRate, true);
            view.setUint32(28, sampleRate * 2, true); // byte rate
            view.setUint16(32, 2, true); // block align
            view.setUint16(34, 16, true); // bits per sample

            // data chunk
            writeString(36, 'data');
            view.setUint32(40, samples.length * 2, true);

            // PCM samples
            let offset = 44;

            for (let i = 0; i < samples.length; i++) {
                let sample = Math.max(-1, Math.min(1, samples[i]));
                let intSample;

                if (sample < 0) {
                    intSample = sample * 0x8000;
                } else {
                    intSample = sample * 0x7FFF;
                }

                view.setInt16(offset, intSample, true);
                offset += 2;
            }

            // Return an actual WAV Blob so it can be previewed and uploaded.
            return new Blob([buffer], {
                type: 'audio/wav'
            });
        }

        /*
        |--------------------------------------------------------------------------
        | WRITE STRING
        |--------------------------------------------------------------------------
        */

        function writeString(view, offset, string) {
            for (let i = 0; i < string.length; i++) {
                view.setUint8(offset + i, string.charCodeAt(i));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FLOAT TO PCM
        |--------------------------------------------------------------------------
        */

        function floatTo16BitPCM(output, offset, input) {
            for (let i = 0; i < input.length; i++, offset += 2) {
                let sample = Math.max(-1, Math.min(1, input[i]));

                /*
                | Convert Float32 [-1,1]
                | to Int16
                */
                const value = sample < 0 ? sample * 0x8000 : sample * 0x7FFF;

                output.setInt16(offset, value, true);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TIMER
        |--------------------------------------------------------------------------
        */

        function updateTimer() {
            const minutes = Math.floor(recordingSeconds / 60);
            const seconds = recordingSeconds % 60;

            recordingTimer.textContent =
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            // Duration track (display only).
            durationFill.style.width =
                Math.min(100, recordingSeconds / MAX_RECORDING_SECONDS * 100) + '%';
            durationFill.classList.toggle('is-enough', recordingSeconds >= MIN_RECORDING_SECONDS);
        }

        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }

        // Try to play the recording as soon as it is ready after Stop is clicked.
        // If the browser blocks automatic playback, the visible audio controls remain available.
        audioPreview.addEventListener('canplay', function() {
            if (!recordedBlob || previewHasFinished) {
                return;
            }

            recordingInstruction.textContent =
                'Recording is ready. Listen to the complete recording before saving.';

            const playPromise = audioPreview.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(function(error) {
                    console.warn('Automatic preview playback was blocked; use the audio player Play button.', error);
                    recordingInstruction.textContent =
                        'Recording ready. Press Play and listen to the complete recording before saving.';
                });
            }
        });

        audioPreview.addEventListener('error', function() {
            console.error('Audio preview failed to load:', audioPreview.error);
            recordingInstruction.textContent =
                'The preview could not be loaded. Please click Record Again and try again.';
        });

        audioPreview.addEventListener('ended', function() {
            if (!recordedBlob || recordedBlob.size === 0) {
                return;
            }

            previewHasFinished = true;
            saveButton.disabled = false;
            recordingInstruction.textContent =
                'Playback complete. You can now save the voice sample or record again.';
        });

        /*
        |--------------------------------------------------------------------------
        | RECORD AGAIN
        |--------------------------------------------------------------------------
        */

        recordAgainButton.addEventListener('click', function() {
            /*
            | Stop preview
            */
            audioPreview.pause();
            audioPreview.removeAttribute('src');
            audioPreview.load();

            /*
            | Release object URL
            */
            if (audioObjectUrl) {
                URL.revokeObjectURL(audioObjectUrl);
                audioObjectUrl = null;
            }

            /*
            | Reset recording
            */
            audioSamples = [];
            recordedBlob = null;
            previewHasFinished = false;

            /*
            | Clear file
            */
            voiceAudioInput.value = '';

            /*
            | Hide preview
            */
            audioPreviewContainer.classList.add('hidden');

            /*
            | Reset timer
            */
            recordingSeconds = 0;
            updateTimer();

            /*
            | Reset buttons
            */
            startButton.disabled = false;
            stopButton.disabled = true;
            saveButton.disabled = true;

            /*
            | Reset text
            */
            recordingStatus.textContent = 'Ready to Record';
            recordingInstruction.textContent = 'Click Start Recording and speak clearly into the microphone.';
        });

        /*
        |--------------------------------------------------------------------------
        | FORM SUBMIT
        |--------------------------------------------------------------------------
        */

        voiceForm.addEventListener('submit', function(event) {
            /*
            | Validate
            */
            if (!recordedBlob || recordedBlob.size === 0) {
                event.preventDefault();
                alert('Please record your voice before saving.');
                return;
            }

            if (!previewHasFinished) {
                event.preventDefault();
                alert('Please listen to the complete recording before saving.');
                return;
            }

            /* Ensure the recorded WAV is attached to the actual form input. */
            try {
                const audioFile = new File(
                    [recordedBlob],
                    'voice_sample.wav', {
                        type: 'audio/wav',
                        lastModified: Date.now()
                    }
                );

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(audioFile);
                voiceAudioInput.files = dataTransfer.files;

                if (!voiceAudioInput.files || voiceAudioInput.files.length === 0) {
                    throw new Error('The recorded audio file could not be attached to the form.');
                }

                console.log('Submitting voice sample:', {
                    name: voiceAudioInput.files[0].name,
                    type: voiceAudioInput.files[0].type,
                    size: voiceAudioInput.files[0].size
                });
            } catch (error) {
                event.preventDefault();
                console.error('Could not attach recorded audio:', error);
                alert(
                    'Could not prepare the voice recording for upload. Please click Record Again and try once more.');
                return;
            }

            // Prevent a double submission while the AI service processes the sample.
            saveButton.disabled = true;
            saveButton.textContent = 'Saving…';
        });

        /*
        |--------------------------------------------------------------------------
        | CLEANUP WHEN LEAVING PAGE
        |--------------------------------------------------------------------------
        */

        window.addEventListener('beforeunload', function() {
            stopTimer();
            stopMicrophone();

            if (audioContext) {
                audioContext.close();
            }

            if (audioObjectUrl) {
                URL.revokeObjectURL(audioObjectUrl);
            }
        });
    </script>
@endpush
