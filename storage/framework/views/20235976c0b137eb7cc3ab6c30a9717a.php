<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>BioVerify AI | Biometric Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #f1f5f9;
            font-family: Arial, sans-serif;
        }

        .choice-card {
            transition: all .2s ease;
        }

        .choice-card:hover {
            transform: translateY(-3px);
            border-color: #2563eb;
        }

        video {
            width: 100%;
            max-height: 380px;
            object-fit: cover;
            background: #0f172a;
            border-radius: 14px;
        }

        .hidden-panel {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden">

        <header class="bg-blue-900 text-white px-6 py-7 text-center">
            <h1 class="text-3xl font-bold">BioVerify AI</h1>
            <p class="text-blue-100 mt-2">Personnel Biometric Verification</p>
        </header>

        <main class="p-6 md:p-8">

            <section id="choicePanel">
                <h2 class="text-2xl font-bold text-slate-800 text-center">
                    Verify Your Identity
                </h2>

                <p class="text-slate-500 text-center mt-2 mb-7">
                    Choose your preferred verification method.
                </p>

                <div class="grid md:grid-cols-2 gap-5">

                    <button type="button" id="chooseFace"
                        class="choice-card border-2 border-slate-200 rounded-2xl p-7 text-left">
                        <div class="text-5xl mb-4">📷</div>
                        <h3 class="text-xl font-bold text-slate-800">Verify with Face</h3>
                        <p class="text-slate-500 mt-2">
                            Use your camera to match your face against registered personnel.
                        </p>
                        <span class="inline-block mt-5 bg-blue-900 text-white rounded-lg px-5 py-2">
                            Continue with Face
                        </span>
                    </button>

                    <button type="button" id="chooseVoice"
                        class="choice-card border-2 border-slate-200 rounded-2xl p-7 text-left">
                        <div class="text-5xl mb-4">🎙️</div>
                        <h3 class="text-xl font-bold text-slate-800">Verify with Voice</h3>
                        <p class="text-slate-500 mt-2">
                            Record your voice, replay the sample, and verify your identity.
                        </p>
                        <span class="inline-block mt-5 bg-emerald-700 text-white rounded-lg px-5 py-2">
                            Continue with Voice
                        </span>
                    </button>

                </div>
            </section>

            <section id="facePanel" class="hidden-panel">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-slate-800">Face Verification</h2>
                    <button type="button" class="backButton text-blue-700 font-semibold">
                        ← Back
                    </button>
                </div>

                <video id="camera" autoplay muted playsinline></video>
                <canvas id="faceCanvas" class="hidden"></canvas>

                <p id="faceStatus" class="text-center text-slate-600 my-4">
                    Start the camera to continue.
                </p>

                <div class="flex flex-wrap justify-center gap-3">
                    <button type="button" id="startCamera"
                        class="bg-blue-900 hover:bg-blue-800 text-white rounded-lg px-5 py-3">
                        Start Camera
                    </button>

                    <button type="button" id="verifyFace" disabled
                        class="bg-emerald-700 hover:bg-emerald-800 disabled:bg-slate-400 text-white rounded-lg px-5 py-3">
                        Verify Face
                    </button>

                    <button type="button" id="stopCamera"
                        class="bg-slate-600 hover:bg-slate-700 text-white rounded-lg px-5 py-3">
                        Stop Camera
                    </button>
                </div>
            </section>

            <section id="voicePanel" class="hidden-panel">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-slate-800">Voice Verification</h2>
                    <button type="button" class="backButton text-blue-700 font-semibold">
                        ← Back
                    </button>
                </div>

                <div class="rounded-xl bg-slate-50 border p-5">
                    <p class="text-slate-600 mb-4">
                        Record a clear voice sample in a quiet environment. Stop recording
                        and replay the sample before verifying.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <button type="button" id="startRecording"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg px-5 py-3">
                            Start Recording
                        </button>

                        <button type="button" id="stopRecording" disabled
                            class="bg-red-600 hover:bg-red-700 disabled:bg-slate-400 text-white rounded-lg px-5 py-3">
                            Stop Recording
                        </button>
                    </div>

                    <div id="audioPreviewPanel" class="hidden-panel mt-5">
                        <label class="block font-semibold text-slate-700 mb-2">
                            Recorded Voice Preview
                        </label>

                        <audio id="audioPreview" controls class="w-full"></audio>

                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="button" id="verifyVoice"
                                class="bg-blue-900 hover:bg-blue-800 text-white rounded-lg px-5 py-3">
                                Verify Voice
                            </button>

                            <button type="button" id="recordAgain"
                                class="bg-slate-600 hover:bg-slate-700 text-white rounded-lg px-5 py-3">
                                Record Again
                            </button>
                        </div>
                    </div>
                </div>

                <p id="voiceStatus" class="text-center text-slate-600 mt-4">
                    Ready to record.
                </p>
            </section>

            <div id="resultMessage" class="hidden-panel mt-6 rounded-xl p-4 text-center" role="status"
                aria-live="polite"></div>

            <div id="loadingMessage" class="hidden-panel text-center mt-4 text-blue-800 font-semibold">
                Verifying identity. Please wait...
            </div>

        </main>

        <footer class="bg-slate-50 border-t px-6 py-4 text-center text-sm text-slate-500">
            BioVerify AI · Secure Personnel Verification
        </footer>

    </div>

    <script>
        (() => {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            const choicePanel = document.getElementById('choicePanel');
            const facePanel = document.getElementById('facePanel');
            const voicePanel = document.getElementById('voicePanel');
            const resultMessage = document.getElementById('resultMessage');
            const loadingMessage = document.getElementById('loadingMessage');

            const camera = document.getElementById('camera');
            const canvas = document.getElementById('faceCanvas');
            const startCameraButton = document.getElementById('startCamera');
            const verifyFaceButton = document.getElementById('verifyFace');
            const faceStatus = document.getElementById('faceStatus');

            const startRecordingButton = document.getElementById('startRecording');
            const stopRecordingButton = document.getElementById('stopRecording');
            const verifyVoiceButton = document.getElementById('verifyVoice');
            const recordAgainButton = document.getElementById('recordAgain');
            const audioPreview = document.getElementById('audioPreview');
            const audioPreviewPanel = document.getElementById('audioPreviewPanel');
            const voiceStatus = document.getElementById('voiceStatus');

            let cameraStream = null;
            let microphoneStream = null;
            let mediaRecorder = null;
            let audioChunks = [];
            let recordedBlob = null;
            let audioObjectUrl = null;
            let busy = false;

            function showMessage(message, success = false) {
                resultMessage.textContent = message;
                resultMessage.className = 'mt-6 rounded-xl p-4 text-center ' +
                    (success ?
                        'bg-emerald-100 text-emerald-800' :
                        'bg-red-100 text-red-800');
            }

            function clearMessage() {
                resultMessage.textContent = '';
                resultMessage.className = 'hidden-panel mt-6 rounded-xl p-4 text-center';
            }

            function setBusy(value) {
                busy = value;
                loadingMessage.classList.toggle('hidden-panel', !value);
                verifyFaceButton.disabled = value || !cameraStream;
                verifyVoiceButton.disabled = value || !recordedBlob;
            }

            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }

                camera.srcObject = null;
                verifyFaceButton.disabled = true;
            }

            function stopMicrophone() {
                if (microphoneStream) {
                    microphoneStream.getTracks().forEach(track => track.stop());
                    microphoneStream = null;
                }
            }

            function releaseAudioPreview() {
                audioPreview.pause();
                audioPreview.removeAttribute('src');
                audioPreview.load();

                if (audioObjectUrl) {
                    URL.revokeObjectURL(audioObjectUrl);
                    audioObjectUrl = null;
                }
            }

            function showChoice() {
                stopCamera();
                stopMicrophone();

                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }

                choicePanel.classList.remove('hidden-panel');
                facePanel.classList.add('hidden-panel');
                voicePanel.classList.add('hidden-panel');
                clearMessage();
            }

            document.getElementById('chooseFace').addEventListener('click', () => {
                clearMessage();
                choicePanel.classList.add('hidden-panel');
                voicePanel.classList.add('hidden-panel');
                facePanel.classList.remove('hidden-panel');
                faceStatus.textContent = 'Click Start Camera to continue.';
            });

            document.getElementById('chooseVoice').addEventListener('click', () => {
                clearMessage();
                stopCamera();
                choicePanel.classList.add('hidden-panel');
                facePanel.classList.add('hidden-panel');
                voicePanel.classList.remove('hidden-panel');
                voiceStatus.textContent = 'Ready to record.';
            });

            document.querySelectorAll('.backButton').forEach(button => {
                button.addEventListener('click', showChoice);
            });

            startCameraButton.addEventListener('click', async () => {
                clearMessage();

                try {
                    stopCamera();

                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user'
                        },
                        audio: false
                    });

                    camera.srcObject = cameraStream;
                    await camera.play();

                    verifyFaceButton.disabled = false;
                    faceStatus.textContent = 'Camera ready. Look directly at the camera.';
                } catch (error) {
                    faceStatus.textContent = 'Unable to access the camera. Check browser permissions.';
                    showMessage(error.message || 'Camera access failed.');
                }
            });

            document.getElementById('stopCamera').addEventListener('click', () => {
                stopCamera();
                faceStatus.textContent = 'Camera stopped.';
            });

            verifyFaceButton.addEventListener('click', async () => {
                if (!cameraStream || busy) return;

                clearMessage();

                if (!camera.videoWidth || !camera.videoHeight) {
                    showMessage('Camera is not ready. Please wait and try again.');
                    return;
                }

                setBusy(true);

                try {
                    canvas.width = camera.videoWidth;
                    canvas.height = camera.videoHeight;

                    canvas.getContext('2d').drawImage(camera, 0, 0);

                    const blob = await new Promise(resolve =>
                        canvas.toBlob(resolve, 'image/jpeg', 0.92)
                    );

                    if (!blob) {
                        throw new Error('Unable to capture the camera image.');
                    }

                    const formData = new FormData();
                    formData.append('face_image', blob, 'face-capture.jpg');

                    const response = await fetch(<?php echo json_encode(route('face.recognition.recognize'), 15, 512) ?>, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Face verification request failed.');
                    }

                    if (data.matched === true && data.person && data.person.id) {
                        stopCamera();
                        showMessage(
                            'Face verified successfully. Redirecting to personnel details...',
                            true
                        );

                        window.location.href =
                            <?php echo json_encode(url('/people'), 15, 512) ?> + '/' + encodeURIComponent(data.person.id);
                        return;
                    }

                    showMessage(data.message || 'No matching face found. Please try again.');
                } catch (error) {
                    showMessage(error.message || 'Face verification failed. Please try again.');
                } finally {
                    setBusy(false);
                }
            });

            startRecordingButton.addEventListener('click', async () => {
                clearMessage();
                releaseAudioPreview();
                recordedBlob = null;
                audioChunks = [];

                try {
                    if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) {
                        throw new Error('Audio recording is not supported in this browser.');
                    }

                    stopMicrophone();

                    microphoneStream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });

                    let options = {};

                    if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
                        options.mimeType = 'audio/webm;codecs=opus';
                    } else if (MediaRecorder.isTypeSupported('audio/ogg;codecs=opus')) {
                        options.mimeType = 'audio/ogg;codecs=opus';
                    }

                    mediaRecorder = new MediaRecorder(microphoneStream, options);

                    mediaRecorder.ondataavailable = event => {
                        if (event.data && event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onerror = () => {
                        voiceStatus.textContent = 'Recording failed. Please try again.';
                        stopMicrophone();
                    };

                    mediaRecorder.onstop = () => {
                        stopMicrophone();

                        const mimeType = mediaRecorder.mimeType || 'audio/webm';
                        recordedBlob = new Blob(audioChunks, {
                            type: mimeType
                        });

                        if (!recordedBlob.size) {
                            recordedBlob = null;
                            voiceStatus.textContent = 'No audio was captured. Please record again.';
                            audioPreviewPanel.classList.add('hidden-panel');
                            return;
                        }

                        audioObjectUrl = URL.createObjectURL(recordedBlob);
                        audioPreview.src = audioObjectUrl;
                        audioPreviewPanel.classList.remove('hidden-panel');

                        voiceStatus.textContent =
                            'Recording complete. Replay your sample before verifying.';
                        verifyVoiceButton.disabled = false;
                    };

                    mediaRecorder.start();

                    startRecordingButton.disabled = true;
                    stopRecordingButton.disabled = false;
                    audioPreviewPanel.classList.add('hidden-panel');
                    voiceStatus.textContent = 'Recording... Speak clearly into the microphone.';
                } catch (error) {
                    stopMicrophone();
                    voiceStatus.textContent = 'Could not start recording.';
                    showMessage(error.message || 'Microphone access failed.');
                }
            });

            stopRecordingButton.addEventListener('click', () => {
                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }

                startRecordingButton.disabled = false;
                stopRecordingButton.disabled = true;
            });

            recordAgainButton.addEventListener('click', () => {
                recordedBlob = null;
                audioChunks = [];
                releaseAudioPreview();

                audioPreviewPanel.classList.add('hidden-panel');
                verifyVoiceButton.disabled = true;
                voiceStatus.textContent = 'Ready to record again.';
                clearMessage();
            });

            verifyVoiceButton.addEventListener('click', async () => {
                if (!recordedBlob || busy) return;

                clearMessage();
                setBusy(true);

                try {
                    const formData = new FormData();

                    const mimeType = recordedBlob.type || 'audio/webm';
                    let extension = 'webm';

                    if (mimeType.includes('ogg')) extension = 'ogg';
                    else if (mimeType.includes('wav')) extension = 'wav';
                    else if (mimeType.includes('mp4')) extension = 'm4a';

                    formData.append(
                        'voice_audio',
                        recordedBlob,
                        'voice-sample.' + extension
                    );

                    const response = await fetch(<?php echo json_encode(route('voice.recognition.recognize'), 15, 512) ?>, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Voice verification request failed.');
                    }

                    if (data.matched === true && data.person && data.person.id) {
                        stopMicrophone();
                        showMessage(
                            'Voice verified successfully. Redirecting to personnel details...',
                            true
                        );

                        window.location.href =
                            <?php echo json_encode(url('/people'), 15, 512) ?> + '/' + encodeURIComponent(data.person.id);
                        return;
                    }

                    showMessage(data.message || 'No matching voice found. Please try again.');
                } catch (error) {
                    showMessage(error.message || 'Voice verification failed. Please try again.');
                } finally {
                    setBusy(false);
                }
            });

            window.addEventListener('beforeunload', () => {
                stopCamera();
                stopMicrophone();

                if (audioObjectUrl) {
                    URL.revokeObjectURL(audioObjectUrl);
                }
            });
        })();
    </script>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\bioverify-ai\resources\views/biometric-login.blade.php ENDPATH**/ ?>