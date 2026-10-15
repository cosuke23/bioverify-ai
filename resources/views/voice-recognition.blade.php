<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Voice Verification | BioVerify AI</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #f3f6fb;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .pcg-blue {
            background: #0033A0;
        }

        .pcg-text {
            color: #0033A0;
        }

        .mic-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8efff;
            color: #0033A0;
            margin: 0 auto;
            transition: all 0.25s ease;
        }

        .mic-circle.recording {
            background: #fee2e2;
            color: #dc2626;
            animation: pulse 1.5s infinite;
        }

        .mic-circle.processing {
            background: #dbeafe;
            color: #2563eb;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.25);
            }

            70% {
                box-shadow: 0 0 0 18px rgba(220, 38, 38, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        audio {
            width: 100%;
        }
    </style>
</head>

<body class="min-h-screen">

    <header class="pcg-blue text-white shadow-md">
        <div class="max-w-6xl mx-auto px-5 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold">BioVerify AI</h1>
                <p class="text-sm text-blue-100">Personnel Identification System</p>
            </div>

            <a href="{{ url('/people') }}"
                class="text-sm bg-white text-blue-900 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50">
                Back to Personnel
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-10">

        <div class="text-center mb-8">
            <p class="pcg-text font-semibold uppercase tracking-widest text-xs mb-2">
                Biometric Authentication
            </p>

            <h2 class="text-3xl font-bold text-gray-900">
                Voice Verification
            </h2>

            <p class="text-gray-600 mt-3">
                Verify your identity using your registered voice sample.
            </p>
        </div>

        <section class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

            <div class="pcg-blue text-white px-6 py-4">
                <h3 class="font-semibold text-lg">Voice Recognition</h3>
                <p class="text-blue-100 text-sm">
                    Allow microphone access and speak clearly.
                </p>
            </div>

            <div class="p-6 sm:p-10">

                <!-- Microphone -->
                <div id="micCircle" class="mic-circle mb-6">
                    <svg id="micIcon" xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="2" width="6" height="12" rx="3"></rect>
                        <path d="M5 10v2a7 7 0 0 0 14 0v-2"></path>
                        <line x1="12" y1="19" x2="12" y2="22"></line>
                        <line x1="8" y1="22" x2="16" y2="22"></line>
                    </svg>
                </div>

                <div class="text-center mb-6">
                    <p id="statusText" class="text-lg font-semibold text-gray-800">
                        Ready to verify
                    </p>

                    <p id="instructionText" class="text-sm text-gray-500 mt-2">
                        Click Start Recording and speak for 3–10 seconds.
                    </p>

                    <p id="timer" class="text-2xl font-bold pcg-text mt-4">
                        00:00
                    </p>
                </div>

                <!-- Recording controls -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-6">

                    <button type="button" id="startBtn"
                        class="pcg-blue text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800">
                        <span class="inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="9" y="2" width="6" height="12" rx="3"></rect>
                                <path d="M5 10v2a7 7 0 0 0 14 0v-2"></path>
                            </svg>
                            Start Recording
                        </span>
                    </button>

                    <button type="button" id="stopBtn" disabled
                        class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700">
                        Stop Recording
                    </button>
                </div>

                <!-- Recorded audio preview -->
                <div id="previewSection" class="hidden border border-gray-200 rounded-xl p-5 bg-gray-50 mb-6">

                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-800">
                            Recording Preview
                        </h4>

                        <span id="recordingDuration" class="text-xs text-gray-500"></span>
                    </div>

                    <audio id="audioPreview" controls></audio>

                    <p class="text-xs text-gray-500 mt-3">
                        Listen to your recording before verifying.
                    </p>
                </div>

                <!-- Verify -->
                <button type="button" id="verifyBtn" disabled
                    class="w-full pcg-blue text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800">
                    Verify Voice
                </button>

                <button type="button" id="retryBtn"
                    class="hidden w-full mt-3 border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">
                    Record Again
                </button>

                <!-- Inline result -->
                <div id="resultBox" class="hidden mt-6 rounded-xl border p-4" role="status" aria-live="polite">
                    <p id="resultTitle" class="font-bold"></p>
                    <p id="resultMessage" class="text-sm mt-1 break-words"></p>
                </div>

                <p class="text-xs text-gray-500 text-center mt-6">
                    Your recording will be processed for voice matching.
                    A matching voice alone does not prevent replay attacks.
                </p>
            </div>
        </section>

        <p class="text-center text-xs text-gray-500 mt-6">
            BioVerify AI &copy; {{ date('Y') }}
        </p>
    </main>

    <script>
        (() => {
            'use strict';

            // Laravel endpoint: create this route in web.php.
            const VERIFY_URL = @json(route('voice.recognition.recognize'));

            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;

            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');
            const verifyBtn = document.getElementById('verifyBtn');
            const retryBtn = document.getElementById('retryBtn');

            const micCircle = document.getElementById('micCircle');
            const statusText = document.getElementById('statusText');
            const instructionText = document.getElementById('instructionText');
            const timer = document.getElementById('timer');

            const previewSection = document.getElementById('previewSection');
            const audioPreview = document.getElementById('audioPreview');
            const recordingDuration = document.getElementById('recordingDuration');

            const resultBox = document.getElementById('resultBox');
            const resultTitle = document.getElementById('resultTitle');
            const resultMessage = document.getElementById('resultMessage');

            let mediaRecorder = null;
            let mediaStream = null;
            let audioChunks = [];
            let audioBlob = null;
            let previewUrl = null;
            let recordingStartedAt = 0;
            let recordingSeconds = 0;
            let timerInterval = null;
            let recordingGeneration = 0;
            let isRecording = false;
            let isProcessing = false;

            const MIN_SECONDS = 2;
            const MAX_SECONDS = 30;

            function setStatus(title, description) {
                statusText.textContent = title;
                instructionText.textContent = description;
            }

            function showResult(title, message, success = false) {
                resultBox.classList.remove('hidden');

                resultBox.className = success ?
                    'mt-6 rounded-xl border border-green-300 bg-green-50 p-4' :
                    'mt-6 rounded-xl border border-red-300 bg-red-50 p-4';

                resultTitle.className = success ?
                    'font-bold text-green-800' :
                    'font-bold text-red-800';

                resultMessage.className = success ?
                    'text-sm text-green-700 mt-1 break-words' :
                    'text-sm text-red-700 mt-1 break-words';

                resultTitle.textContent = title;
                resultMessage.textContent = message;
            }

            function clearResult() {
                resultBox.classList.add('hidden');
                resultTitle.textContent = '';
                resultMessage.textContent = '';
            }

            function formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;

                return String(mins).padStart(2, '0') + ':' +
                    String(secs).padStart(2, '0');
            }

            function clearTimer() {
                if (timerInterval !== null) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
            }

            function releaseMicrophone() {
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                    mediaStream = null;
                }
            }

            function clearPreview() {
                audioPreview.pause();
                audioPreview.removeAttribute('src');
                audioPreview.load();

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }

                previewSection.classList.add('hidden');
                recordingDuration.textContent = '';
            }

            function resetRecording() {
                recordingGeneration++;

                clearTimer();
                releaseMicrophone();
                clearPreview();

                audioChunks = [];
                audioBlob = null;
                mediaRecorder = null;
                recordingSeconds = 0;
                isRecording = false;
                isProcessing = false;

                timer.textContent = '00:00';

                micCircle.classList.remove('recording', 'processing');

                startBtn.disabled = false;
                stopBtn.disabled = true;
                verifyBtn.disabled = true;

                startBtn.classList.remove('hidden');
                stopBtn.classList.remove('hidden');
                verifyBtn.classList.remove('hidden');
                retryBtn.classList.add('hidden');

                clearResult();

                setStatus(
                    'Ready to verify',
                    'Click Start Recording and speak for 3–10 seconds.'
                );
            }

            function getSupportedMimeType() {
                if (!window.MediaRecorder) {
                    return '';
                }

                const types = [
                    'audio/webm;codecs=opus',
                    'audio/webm',
                    'audio/ogg;codecs=opus',
                    'audio/mp4'
                ];

                return types.find(type =>
                    MediaRecorder.isTypeSupported(type)
                ) || '';
            }

            async function startRecording() {
                if (isRecording || isProcessing) {
                    return;
                }

                if (!navigator.mediaDevices ||
                    !navigator.mediaDevices.getUserMedia ||
                    !window.MediaRecorder) {
                    showResult(
                        'Microphone unavailable',
                        'Use a supported browser and open this page through HTTPS or localhost.'
                    );
                    return;
                }

                resetRecording();

                const generation = recordingGeneration;

                try {
                    setStatus(
                        'Requesting microphone',
                        'Allow microphone access when your browser asks.'
                    );

                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: {
                            echoCancellation: true,
                            noiseSuppression: true,
                            autoGainControl: true
                        }
                    });

                    // If the user restarted or reset while permission was pending,
                    // do not keep an unwanted microphone stream.
                    if (generation !== recordingGeneration) {
                        stream.getTracks().forEach(track => track.stop());
                        return;
                    }

                    mediaStream = stream;

                    const mimeType = getSupportedMimeType();

                    mediaRecorder = mimeType ?
                        new MediaRecorder(stream, {
                            mimeType
                        }) :
                        new MediaRecorder(stream);

                    audioChunks = [];
                    audioBlob = null;

                    mediaRecorder.ondataavailable = event => {
                        if (event.data && event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onerror = () => {
                        clearTimer();
                        releaseMicrophone();

                        isRecording = false;

                        startBtn.disabled = false;
                        stopBtn.disabled = true;

                        micCircle.classList.remove('recording');

                        showResult(
                            'Recording error',
                            'The browser could not record audio. Please try again.'
                        );
                    };

                    mediaRecorder.onstop = () => {
                        clearTimer();
                        releaseMicrophone();

                        isRecording = false;

                        micCircle.classList.remove('recording');

                        startBtn.disabled = false;
                        stopBtn.disabled = true;

                        if (!audioChunks.length) {
                            setStatus(
                                'No audio captured',
                                'Please record again and speak clearly.'
                            );

                            showResult(
                                'Recording is empty',
                                'No audio data was captured. Check your microphone and try again.'
                            );

                            verifyBtn.disabled = true;
                            retryBtn.classList.remove('hidden');
                            return;
                        }

                        const mime = mediaRecorder && mediaRecorder.mimeType ?
                            mediaRecorder.mimeType :
                            (audioChunks[0].type || 'audio/webm');

                        audioBlob = new Blob(audioChunks, {
                            type: mime
                        });

                        if (audioBlob.size === 0) {
                            audioBlob = null;

                            showResult(
                                'Recording is empty',
                                'The audio file is empty. Please record again.'
                            );

                            verifyBtn.disabled = true;
                            retryBtn.classList.remove('hidden');
                            return;
                        }

                        previewUrl = URL.createObjectURL(audioBlob);

                        audioPreview.src = previewUrl;
                        previewSection.classList.remove('hidden');

                        recordingDuration.textContent =
                            formatTime(recordingSeconds);

                        verifyBtn.disabled = false;
                        retryBtn.classList.remove('hidden');

                        setStatus(
                            'Recording complete',
                            'Play your recording to check the audio, then click Verify Voice.'
                        );
                    };

                    mediaRecorder.start(250);

                    isRecording = true;
                    recordingStartedAt = Date.now();
                    recordingSeconds = 0;

                    startBtn.disabled = true;
                    stopBtn.disabled = false;
                    verifyBtn.disabled = true;

                    retryBtn.classList.add('hidden');

                    micCircle.classList.add('recording');

                    setStatus(
                        'Listening...',
                        'Speak clearly. Recording stops automatically after 30 seconds.'
                    );

                    timerInterval = setInterval(() => {
                        recordingSeconds = Math.floor(
                            (Date.now() - recordingStartedAt) / 1000
                        );

                        timer.textContent = formatTime(recordingSeconds);

                        if (recordingSeconds >= MAX_SECONDS && isRecording) {
                            stopRecording();
                        }
                    }, 250);

                } catch (error) {
                    clearTimer();
                    releaseMicrophone();

                    isRecording = false;

                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                    verifyBtn.disabled = true;

                    micCircle.classList.remove('recording');

                    setStatus(
                        'Microphone access required',
                        'Allow microphone permission in your browser settings.'
                    );

                    showResult(
                        'Cannot access microphone',
                        error.name === 'NotAllowedError' ?
                        'Microphone permission was denied. Allow microphone access and try again.' :
                        'Could not start recording. Check your microphone and browser connection.'
                    );
                }
            }

            function stopRecording() {
                if (!isRecording || !mediaRecorder) {
                    return;
                }

                recordingSeconds = Math.floor(
                    (Date.now() - recordingStartedAt) / 1000
                );

                timer.textContent = formatTime(recordingSeconds);

                clearTimer();

                if (mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }
            }

            async function verifyVoice() {
                if (isProcessing || isRecording || !audioBlob) {
                    return;
                }

                if (recordingSeconds < MIN_SECONDS) {
                    showResult(
                        'Recording too short',
                        'Please record at least 2 seconds of speech.'
                    );
                    return;
                }

                if (audioBlob.size > 10 * 1024 * 1024) {
                    showResult(
                        'Recording too large',
                        'Please record a shorter sample under 10 MB.'
                    );
                    return;
                }

                isProcessing = true;

                verifyBtn.disabled = true;
                startBtn.disabled = true;
                retryBtn.disabled = true;

                micCircle.classList.add('processing');

                setStatus(
                    'Verifying voice...',
                    'Comparing your recording with registered voice samples.'
                );

                clearResult();

                try {
                    const formData = new FormData();

                    const extension = audioBlob.type.includes('mp4') ?
                        'm4a' :
                        audioBlob.type.includes('ogg') ?
                        'ogg' :
                        audioBlob.type.includes('wav') ?
                        'wav' :
                        'webm';

                    formData.append(
                        'voice_audio',
                        audioBlob,
                        'voice-verification.' + extension
                    );

                    const response = await fetch(VERIFY_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData,
                        credentials: 'same-origin'
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const validationMessage = data.errors ?
                            Object.values(data.errors).flat().join(' ') :
                            '';

                        throw new Error(
                            validationMessage ||
                            data.message ||
                            data.detail ||
                            'Voice verification failed. Please try again.'
                        );
                    }

                    if (data.matched === true &&
                        data.person &&
                        data.person.id) {

                        setStatus(
                            'Voice recognized',
                            'Personnel identity matched successfully.'
                        );

                        showResult(
                            'Voice recognized',
                            'Matched personnel: ' +
                            (data.person.name || data.person.person_id || 'Verified personnel'),
                            true
                        );

                        // Follow the same personnel detail-page pattern
                        // used by the existing face-verification flow.
                        window.location.href =
                            @json(url('/people')) + '/' +
                            encodeURIComponent(data.person.id);

                        return;
                    }

                    setStatus(
                        'Voice not recognized',
                        'No registered voice matched this recording.'
                    );

                    showResult(
                        'Verification unsuccessful',
                        data.message ||
                        'Your voice did not match a registered sample. Try recording again.'
                    );

                    verifyBtn.disabled = false;
                    startBtn.disabled = false;
                    retryBtn.disabled = false;

                } catch (error) {
                    setStatus(
                        'Verification failed',
                        'Please check the connection and try again.'
                    );

                    showResult(
                        'Unable to verify voice',
                        error.message || 'An unexpected error occurred.'
                    );

                    verifyBtn.disabled = false;
                    startBtn.disabled = false;
                    retryBtn.disabled = false;

                } finally {
                    isProcessing = false;
                    micCircle.classList.remove('processing');
                }
            }

            startBtn.addEventListener('click', startRecording);
            stopBtn.addEventListener('click', stopRecording);
            verifyBtn.addEventListener('click', verifyVoice);

            retryBtn.addEventListener('click', () => {
                resetRecording();
                startRecording();
            });

            window.addEventListener('beforeunload', () => {
                clearTimer();
                releaseMicrophone();

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }
            });
        })();
    </script>

</body>

</html>
