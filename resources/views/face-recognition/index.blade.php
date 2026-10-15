<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Face Verification - BioVerify AI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /*
         * Prevent the browser from showing unwanted
         * margins around the camera page.
         */
        html,
        body {
            margin: 0;
            padding: 0;
        }

        /*
         * Camera container
         */
        .camera-container {
            position: relative;
            width: 100%;
            height: 1500px;
            background: #000;
            overflow: hidden;
        }

        /*
         * Video
         *
         * object-contain keeps the complete camera image
         * visible and prevents the face from being cropped.
         */
        #cameraVideo {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #000;
        }

        /*
         * Camera guide
         */
        .face-guide {
            position: relative;
            width: 420px;
            height: 560px;
            max-width: 80%;
            max-height: 80%;
        }

        /*
         * Make the camera area smaller on very small screens.
         */
        @media (max-width: 768px) {

            .camera-container {
                height: 900px;
            }

            .face-guide {
                width: 280px;
                height: 380px;
            }

        }
    </style>

</head>


<body class="min-h-screen bg-gray-100">


    <!-- ============================================================= -->
    <!-- HEADER -->
    <!-- ============================================================= -->

    <header class="bg-white border-b border-gray-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center justify-between h-16">


                <!-- Logo -->
                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center">

                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                        </svg>

                    </div>


                    <div>

                        <h1 class="text-lg font-bold text-gray-900">
                            BioVerify AI
                        </h1>

                        <p class="text-xs text-gray-500">
                            Face Verification
                        </p>

                    </div>

                </div>


                <!-- Back -->
                <a href="{{ route('people.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />

                    </svg>

                    Back to People

                </a>

            </div>

        </div>

    </header>


    <!-- ============================================================= -->
    <!-- MAIN -->
    <!-- ============================================================= -->

    <main class="max-w-[1800px] mx-auto px-4 sm:px-6 lg:px-8 py-8">


        <!-- Heading -->
        <div class="mb-6">

            <h2 class="text-2xl font-bold text-gray-900">
                Face Verification
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Look at the camera. BioVerify AI will automatically identify the registered person.
            </p>

        </div>


        <!-- ========================================================= -->
        <!-- MAIN GRID -->
        <!-- ========================================================= -->

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">


            <!-- ===================================================== -->
            <!-- CAMERA COLUMN -->
            <!-- ===================================================== -->

            <div class="xl:col-span-3">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">


                    <!-- Camera Header -->
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Live Camera
                            </h3>

                            <p class="text-xs text-gray-500 mt-1">
                                Automatic face verification
                            </p>

                        </div>


                        <!-- Status -->
                        <div id="cameraStatus"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">

                            <span id="cameraStatusDot" class="w-2 h-2 rounded-full bg-gray-400">
                            </span>

                            <span id="cameraStatusText">
                                Starting camera...
                            </span>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- CAMERA -->
                    <!-- ================================================= -->

                    <div class="camera-container">


                        <!-- Video -->
                        <video id="cameraVideo" autoplay muted playsinline>
                        </video>


                        <!-- Face Guide -->
                        <div id="cameraOverlay"
                            class="absolute inset-0 pointer-events-none flex items-center justify-center">

                            <div class="face-guide">


                                <!-- Top Left -->
                                <div
                                    class="absolute top-0 left-0 w-16 h-16 border-l-4 border-t-4 border-white rounded-tl-2xl">
                                </div>


                                <!-- Top Right -->
                                <div
                                    class="absolute top-0 right-0 w-16 h-16 border-r-4 border-t-4 border-white rounded-tr-2xl">
                                </div>


                                <!-- Bottom Left -->
                                <div
                                    class="absolute bottom-0 left-0 w-16 h-16 border-l-4 border-b-4 border-white rounded-bl-2xl">
                                </div>


                                <!-- Bottom Right -->
                                <div
                                    class="absolute bottom-0 right-0 w-16 h-16 border-r-4 border-b-4 border-white rounded-br-2xl">
                                </div>


                                <!-- Guide Text -->
                                <div class="absolute left-1/2 -translate-x-1/2 bottom-[-55px] whitespace-nowrap">

                                    <div class="px-4 py-2 rounded-full bg-black/60 text-white text-sm backdrop-blur">

                                        Position your face inside the frame

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- SCANNING -->
                        <!-- ================================================= -->

                        <div id="scanningIndicator" class="absolute bottom-8 left-1/2 -translate-x-1/2 hidden">

                            <div
                                class="flex items-center px-5 py-3 rounded-full bg-black/75 text-white text-sm backdrop-blur">

                                <span class="relative flex h-3 w-3 mr-3">

                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75">
                                    </span>

                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500">
                                    </span>

                                </span>

                                Scanning for face...

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- CAMERA ERROR -->
                        <!-- ================================================= -->

                        <div id="cameraError"
                            class="absolute inset-0 hidden items-center justify-center bg-gray-900/95 p-6">

                            <div class="text-center text-white max-w-md">


                                <div
                                    class="mx-auto w-16 h-16 rounded-full bg-red-500/20 flex items-center justify-center mb-5">

                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />

                                    </svg>

                                </div>


                                <h3 class="text-lg font-semibold">
                                    Camera Access Required
                                </h3>


                                <p id="cameraErrorMessage" class="mt-2 text-sm text-gray-300">

                                    Please allow camera access to continue.

                                </p>


                                <button id="retryCameraButton" type="button"
                                    class="mt-5 inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">

                                    Retry Camera

                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- CAMERA FOOTER -->
                    <!-- ================================================= -->

                    <div class="px-5 py-4 border-t border-gray-200">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">


                            <div class="text-sm text-gray-500">

                                <span id="verificationStatus">
                                    Initializing verification...
                                </span>

                            </div>


                            <button id="restartButton" type="button"
                                class="hidden inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582M20 20v-5h-.581M5.64 15.64A8 8 0 0018.36 5.64M18.36 18.36A8 8 0 015.64 5.64" />

                                </svg>

                                Start Again

                            </button>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- RESULT COLUMN -->
            <!-- ===================================================== -->

            <div>


                <!-- Result -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">


                    <!-- Result Header -->
                    <div class="px-5 py-4 border-b border-gray-200">

                        <h3 class="font-semibold text-gray-900">
                            Verification Result
                        </h3>

                        <p class="text-xs text-gray-500 mt-1">
                            AI identification result
                        </p>

                    </div>


                    <!-- ================================================= -->
                    <!-- WAITING -->
                    <!-- ================================================= -->

                    <div id="waitingResult" class="p-6 text-center">


                        <div class="mx-auto w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center">

                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                            </svg>

                        </div>


                        <h4 class="mt-4 text-base font-semibold text-gray-900">
                            Waiting for face
                        </h4>


                        <p class="mt-1 text-sm text-gray-500">
                            Position your face inside the camera frame.
                        </p>

                    </div>


                    <!-- ================================================= -->
                    <!-- SUCCESS -->
                    <!-- ================================================= -->

                    <div id="successResult" class="hidden">


                        <div class="p-6">


                            <div class="flex items-center gap-3">


                                <div class="w-11 h-11 rounded-full bg-green-100 flex items-center justify-center">

                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />

                                    </svg>

                                </div>


                                <div>

                                    <h4 class="font-semibold text-green-700">
                                        Face Match Found
                                    </h4>

                                    <p class="text-xs text-gray-500">
                                        Registered person identified
                                    </p>

                                </div>

                            </div>


                            <div class="mt-6 space-y-4">


                                <!-- Person ID -->
                                <div>

                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                        Person ID
                                    </p>

                                    <p id="resultPersonId" class="mt-1 text-lg font-bold text-gray-900">
                                        —
                                    </p>

                                </div>


                                <!-- Name -->
                                <div>

                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                        Name
                                    </p>

                                    <p id="resultName" class="mt-1 text-lg font-semibold text-gray-900">
                                        —
                                    </p>

                                </div>


                                <!-- Score -->
                                <div class="bg-gray-50 rounded-lg p-4">

                                    <p class="text-xs text-gray-500">
                                        Similarity Score
                                    </p>

                                    <p id="resultScore" class="mt-1 text-2xl font-bold text-blue-700">
                                        —
                                    </p>

                                </div>


                                <!-- AI Model -->
                                <div class="bg-gray-50 rounded-lg p-4">

                                    <p class="text-xs text-gray-500">
                                        AI Model
                                    </p>

                                    <p id="resultModel" class="mt-1 text-sm font-semibold text-gray-900">
                                        —
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- FAILED -->
                    <!-- ================================================= -->

                    <div id="failedResult" class="hidden p-6">

                        <div class="flex items-center gap-3">


                            <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center">

                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />

                                </svg>

                            </div>


                            <div>

                                <h4 class="font-semibold text-red-700">
                                    Recognition Failed
                                </h4>

                                <p id="failedMessage" class="text-sm text-gray-500 mt-1">

                                    Unable to identify the face.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ===================================================== -->
                <!-- INSTRUCTIONS -->
                <!-- ===================================================== -->

                <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-5">


                    <h4 class="font-semibold text-blue-900">
                        Verification Instructions
                    </h4>


                    <ul class="mt-3 space-y-2 text-sm text-blue-800">


                        <li class="flex gap-2">
                            <span>•</span>
                            <span>Face the camera directly.</span>
                        </li>


                        <li class="flex gap-2">
                            <span>•</span>
                            <span>Make sure your face is well illuminated.</span>
                        </li>


                        <li class="flex gap-2">
                            <span>•</span>
                            <span>Keep your face inside the guide frame.</span>
                        </li>


                        <li class="flex gap-2">
                            <span>•</span>
                            <span>The system will automatically verify your face.</span>
                        </li>


                    </ul>

                </div>

            </div>

        </div>

    </main>


    <!-- ============================================================= -->
    <!-- HIDDEN CANVAS -->
    <!-- ============================================================= -->

    <canvas id="captureCanvas" class="hidden">
    </canvas>


    <!-- ============================================================= -->
    <!-- JAVASCRIPT -->
    <!-- ============================================================= -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {


            console.log('BioVerify Face Verification initialized.');


            /*
             * Verify SweetAlert2
             */
            if (typeof Swal === 'undefined') {

                console.error(
                    'SweetAlert2 was not loaded.'
                );

            } else {

                console.log(
                    'SweetAlert2 loaded successfully.'
                );

            }


            /*
             * Elements
             */

            const video =
                document.getElementById(
                    'cameraVideo'
                );

            const canvas =
                document.getElementById(
                    'captureCanvas'
                );


            const cameraStatus =
                document.getElementById(
                    'cameraStatus'
                );

            const cameraStatusDot =
                document.getElementById(
                    'cameraStatusDot'
                );

            const cameraStatusText =
                document.getElementById(
                    'cameraStatusText'
                );


            const cameraError =
                document.getElementById(
                    'cameraError'
                );

            const cameraErrorMessage =
                document.getElementById(
                    'cameraErrorMessage'
                );

            const retryCameraButton =
                document.getElementById(
                    'retryCameraButton'
                );


            const scanningIndicator =
                document.getElementById(
                    'scanningIndicator'
                );


            const verificationStatus =
                document.getElementById(
                    'verificationStatus'
                );


            const waitingResult =
                document.getElementById(
                    'waitingResult'
                );

            const successResult =
                document.getElementById(
                    'successResult'
                );

            const failedResult =
                document.getElementById(
                    'failedResult'
                );


            const resultPersonId =
                document.getElementById(
                    'resultPersonId'
                );

            const resultName =
                document.getElementById(
                    'resultName'
                );

            const resultScore =
                document.getElementById(
                    'resultScore'
                );

            const resultModel =
                document.getElementById(
                    'resultModel'
                );


            const failedMessage =
                document.getElementById(
                    'failedMessage'
                );


            const restartButton =
                document.getElementById(
                    'restartButton'
                );


            /*
             * State
             */

            let cameraStream = null;

            let scanTimer = null;

            let recognitionInProgress = false;

            let verificationStopped = false;

            let noMatchCount = 0;

            let noMatchAlertShowing = false;


            /*
             * =========================================================
             * CAMERA STATUS
             * =========================================================
             */

            function setCameraStatus(
                text,
                dotClass,
                backgroundClass,
                textClass
            ) {

                cameraStatusText.textContent =
                    text;


                cameraStatusDot.className =
                    'w-2 h-2 rounded-full ' +
                    dotClass;


                cameraStatus.className =
                    'inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium ' +
                    backgroundClass +
                    ' ' +
                    textClass;

            }


            /*
             * =========================================================
             * START CAMERA
             * =========================================================
             */

            async function startCamera() {


                console.log(
                    'Starting camera...'
                );


                stopScanning();

                stopCamera();


                verificationStopped =
                    false;

                recognitionInProgress =
                    false;

                noMatchCount =
                    0;


                cameraError.classList.add(
                    'hidden'
                );

                cameraError.classList.remove(
                    'flex'
                );


                waitingResult.classList.remove(
                    'hidden'
                );

                successResult.classList.add(
                    'hidden'
                );

                failedResult.classList.add(
                    'hidden'
                );


                restartButton.classList.add(
                    'hidden'
                );


                scanningIndicator.classList.add(
                    'hidden'
                );


                verificationStatus.textContent =
                    'Requesting camera access...';


                setCameraStatus(
                    'Starting',
                    'bg-yellow-400',
                    'bg-yellow-100',
                    'text-yellow-700'
                );


                try {


                    /*
                     * Check browser support
                     */

                    if (
                        !navigator.mediaDevices ||
                        !navigator.mediaDevices.getUserMedia
                    ) {

                        throw new Error(
                            'Camera access is not supported by this browser.'
                        );

                    }


                    /*
                     * Request camera
                     */

                    cameraStream =
                        await navigator.mediaDevices.getUserMedia({

                            video: {

                                facingMode: {
                                    ideal: 'user'
                                },

                                width: {
                                    ideal: 1280
                                },

                                height: {
                                    ideal: 720
                                }

                            },

                            audio: false

                        });


                    console.log(
                        'Camera stream received.'
                    );


                    /*
                     * Attach stream
                     */

                    video.srcObject =
                        cameraStream;


                    /*
                     * Wait until video metadata
                     * becomes available.
                     */

                    await new Promise(
                        function(resolve) {

                            if (
                                video.readyState >= 1
                            ) {

                                resolve();

                                return;

                            }


                            video.onloadedmetadata =
                                function() {

                                    resolve();

                                };

                        }
                    );


                    await video.play();


                    console.log(
                        'Video playing.',
                        video.videoWidth,
                        video.videoHeight
                    );


                    /*
                     * Camera active
                     */

                    setCameraStatus(
                        'Active',
                        'bg-green-500',
                        'bg-green-100',
                        'text-green-700'
                    );


                    verificationStatus.textContent =
                        'Camera active. Looking for a face...';


                    scanningIndicator.classList.remove(
                        'hidden'
                    );


                    /*
                     * Give camera one second
                     * to stabilize.
                     */

                    setTimeout(
                        function() {

                            if (
                                !verificationStopped
                            ) {

                                startScanning();

                            }

                        },
                        1000
                    );


                } catch (error) {


                    console.error(
                        'Camera error:',
                        error
                    );


                    showCameraError(
                        getCameraErrorMessage(
                            error
                        )
                    );

                }

            }


            /*
             * =========================================================
             * START SCANNING
             * =========================================================
             */

            function startScanning() {


                stopScanning();


                if (
                    verificationStopped
                ) {

                    return;

                }


                /*
                 * Scan immediately
                 */

                verifyCurrentFrame();


                /*
                 * Scan approximately
                 * every second.
                 */

                scanTimer =
                    setInterval(
                        function() {

                            verifyCurrentFrame();

                        },
                        1000
                    );

            }


            /*
             * =========================================================
             * STOP SCANNING
             * =========================================================
             */

            function stopScanning() {


                if (
                    scanTimer !== null
                ) {

                    clearInterval(
                        scanTimer
                    );

                    scanTimer = null;

                }

            }


            /*
             * =========================================================
             * VERIFY CURRENT FRAME
             * =========================================================
             */

            async function verifyCurrentFrame() {


                if (
                    verificationStopped
                ) {

                    return;

                }


                if (
                    recognitionInProgress
                ) {

                    return;

                }


                if (
                    !video.videoWidth ||
                    !video.videoHeight
                ) {

                    console.log(
                        'Video dimensions not ready.'
                    );

                    return;

                }


                recognitionInProgress =
                    true;


                try {


                    /*
                     * Set canvas dimensions
                     */

                    canvas.width =
                        video.videoWidth;

                    canvas.height =
                        video.videoHeight;


                    /*
                     * Draw video frame
                     */

                    const context =
                        canvas.getContext(
                            '2d'
                        );


                    context.drawImage(
                        video,
                        0,
                        0,
                        canvas.width,
                        canvas.height
                    );


                    /*
                     * Convert to JPEG
                     */

                    const blob =
                        await new Promise(
                            function(resolve) {

                                canvas.toBlob(
                                    resolve,
                                    'image/jpeg',
                                    0.85
                                );

                            }
                        );


                    if (!blob) {

                        throw new Error(
                            'Unable to capture camera frame.'
                        );

                    }


                    verificationStatus.textContent =
                        'Analyzing face...';


                    /*
                     * Form data
                     */

                    const formData =
                        new FormData();


                    formData.append(
                        'face_image',
                        blob,
                        'camera-verification.jpg'
                    );


                    /*
                     * Send to Laravel
                     */

                    const response =
                        await fetch(
                            '{{ route('face.recognition.recognize') }}', {

                                method: 'POST',

                                body: formData,

                                headers: {

                                    'Accept': 'application/json',

                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'

                                }

                            }
                        );


                    /*
                     * Parse response
                     */

                    const data =
                        await response.json();


                    console.log(
                        'Recognition response:',
                        data
                    );


                    if (
                        !response.ok
                    ) {

                        throw new Error(
                            data.message ||
                            data.detail ||
                            'Recognition request failed.'
                        );

                    }


                    /*
                     * =================================================
                     * MATCH FOUND
                     * =================================================
                     */

                    if (
                        data.matched === true &&
                        data.person
                    ) {


                        console.log(
                            'FACE MATCH FOUND',
                            data.person
                        );


                        noMatchCount =
                            0;


                        /*
                         * Update result panel
                         */

                        showMatch(
                            data
                        );


                        /*
                         * Stop scanning
                         */

                        stopScanning();


                        verificationStopped =
                            true;


                        scanningIndicator.classList.add(
                            'hidden'
                        );


                        verificationStatus.textContent =
                            'Face successfully verified.';


                        /*
                         * Stop camera
                         */

                        stopCamera();


                        /*
                         * SUCCESS SWEETALERT
                         */

                        showSuccessAlert(
                            data
                        );


                        return;

                    }


                    /*
                     * =================================================
                     * NO MATCH
                     * =================================================
                     */

                    noMatchCount++;


                    console.log(
                        'No match:',
                        noMatchCount
                    );


                    verificationStatus.textContent =
                        'No match found yet. Keep your face inside the frame.';


                    /*
                     * Show warning after
                     * 5 consecutive failures.
                     */

                    if (
                        noMatchCount >= 5 &&
                        !noMatchAlertShowing
                    ) {


                        noMatchCount =
                            0;


                        showNoMatchAlert();

                    }


                } catch (error) {


                    console.error(
                        'Face recognition error:',
                        error
                    );


                    verificationStatus.textContent =
                        'Unable to complete this scan. Retrying...';

                } finally {


                    recognitionInProgress =
                        false;

                }

            }


            /*
             * =========================================================
             * SUCCESS ALERT
             * =========================================================
             */

            function showSuccessAlert(data) {

                const personName =
                    data.person?.name ||
                    'Unknown';

                const personId =
                    data.person?.person_id ||
                    data.person_id ||
                    '—';

                const databasePersonId =
                    data.person?.id ||
                    null;

                const score =
                    data.similarity_score !== undefined &&
                    data.similarity_score !== null ?
                    Number(
                        data.similarity_score
                    ).toFixed(5) :
                    '—';

                /*
                |--------------------------------------------------------------------------
                | Make sure Laravel database ID exists
                |--------------------------------------------------------------------------
                */

                if (!databasePersonId) {

                    console.error(
                        'Personnel database ID missing.',
                        data
                    );

                    if (
                        typeof Swal === 'undefined'
                    ) {
                        alert(
                            'Face recognized, but the personnel database ID was not returned.'
                        );

                        return;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Personnel ID Missing',
                        text: 'The face was recognized, but the personnel database ID was not returned.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#2563eb',
                    });

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Build Proceed URL
                |--------------------------------------------------------------------------
                */

                const proceedUrl =
                    '{{ url('/recognized-person') }}/' +
                    encodeURIComponent(
                        databasePersonId
                    );

                /*
                |--------------------------------------------------------------------------
                | SweetAlert
                |--------------------------------------------------------------------------
                */

                if (
                    typeof Swal === 'undefined'
                ) {

                    const proceed = confirm(
                        'Face Recognized!\n\n' +
                        'Name: ' +
                        personName +
                        '\n' +
                        'Person ID: ' +
                        personId +
                        '\n' +
                        'Similarity: ' +
                        score +
                        '\n\n' +
                        'Proceed to personnel information?'
                    );

                    if (proceed) {
                        window.location.href =
                            proceedUrl;
                    }

                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Face Recognized!',
                    html: `
            <div style="text-align:center">

                <div style="
                    font-size:16px;
                    color:#6b7280;
                    margin-bottom:10px;
                ">
                    Registered person identified.
                </div>

                <div style="
                    font-size:22px;
                    font-weight:700;
                    color:#111827;
                ">
                    ${escapeHtml(personName)}
                </div>

                <div style="
                    margin-top:10px;
                    font-size:14px;
                    color:#6b7280;
                ">
                    Person ID:
                    <strong>
                        ${escapeHtml(personId)}
                    </strong>
                </div>

                <div style="
                    margin-top:6px;
                    font-size:15px;
                    font-weight:600;
                    color:#2563eb;
                ">
                    Similarity:
                    ${score}
                </div>

            </div>
        `,

                    confirmButtonText: 'Proceed',
                    confirmButtonColor: '#2563eb',

                    allowOutsideClick: false,
                    allowEscapeKey: false,

                }).then((result) => {

                    if (result.isConfirmed) {

                        window.location.href =
                            proceedUrl;
                    }

                });
            }

            /*
             * =========================================================
             * NO MATCH ALERT
             * =========================================================
             */

            function showNoMatchAlert() {


                noMatchAlertShowing =
                    true;


                if (
                    typeof Swal === 'undefined'
                ) {

                    alert(
                        'Face Not Recognized. Please position your face properly and try again.'
                    );

                    noMatchAlertShowing =
                        false;

                    return;

                }


                Swal.fire({

                    icon: 'warning',

                    title: 'Face Not Recognized',

                    text: 'The system could not identify your face. Please position your face properly and try again.',

                    confirmButtonText: 'Continue Scanning',

                    confirmButtonColor: '#2563eb',

                    allowOutsideClick: false

                }).then(
                    function() {

                        noMatchAlertShowing =
                            false;

                    }
                );

            }


            /*
             * =========================================================
             * SHOW MATCH
             * =========================================================
             */

            function showMatch(
                data
            ) {


                waitingResult.classList.add(
                    'hidden'
                );


                failedResult.classList.add(
                    'hidden'
                );


                successResult.classList.remove(
                    'hidden'
                );


                resultPersonId.textContent =
                    data.person?.person_id ||
                    data.person_id ||
                    '—';


                resultName.textContent =
                    data.person?.name ||
                    '—';


                if (
                    data.similarity_score !== undefined &&
                    data.similarity_score !== null
                ) {

                    resultScore.textContent =
                        Number(
                            data.similarity_score
                        ).toFixed(5);

                } else {

                    resultScore.textContent =
                        '—';

                }


                resultModel.textContent =
                    data.model_version ||
                    'buffalo_l';


                /*
                 * Show Start Again
                 */

                restartButton.classList.remove(
                    'hidden'
                );

            }


            /*
             * =========================================================
             * STOP CAMERA
             * =========================================================
             */

            function stopCamera() {


                if (
                    cameraStream
                ) {


                    cameraStream
                        .getTracks()
                        .forEach(
                            function(track) {

                                track.stop();

                            }
                        );


                    cameraStream =
                        null;

                }


                video.srcObject =
                    null;


                setCameraStatus(
                    'Stopped',
                    'bg-gray-400',
                    'bg-gray-100',
                    'text-gray-600'
                );

            }


            /*
             * =========================================================
             * CAMERA ERROR
             * =========================================================
             */

            function showCameraError(
                message
            ) {


                stopScanning();

                stopCamera();


                verificationStopped =
                    true;


                scanningIndicator.classList.add(
                    'hidden'
                );


                cameraErrorMessage.textContent =
                    message;


                cameraError.classList.remove(
                    'hidden'
                );


                cameraError.classList.add(
                    'flex'
                );


                verificationStatus.textContent =
                    'Camera could not be started.';

            }


            /*
             * =========================================================
             * CAMERA ERROR MESSAGE
             * =========================================================
             */

            function getCameraErrorMessage(
                error
            ) {


                if (!error) {

                    return 'Unable to access the camera.';

                }


                if (
                    error.name ===
                    'NotAllowedError'
                ) {

                    return 'Camera permission was denied. Please allow camera access in your browser and try again.';

                }


                if (
                    error.name ===
                    'NotFoundError'
                ) {

                    return 'No camera was found on this device.';

                }


                if (
                    error.name ===
                    'NotReadableError'
                ) {

                    return 'The camera is already being used by another application.';

                }


                if (
                    error.name ===
                    'SecurityError'
                ) {

                    return 'Camera access is blocked by the browser security settings.';

                }


                return error.message ||
                    'Unable to access the camera.';

            }


            /*
             * =========================================================
             * ESCAPE HTML
             * =========================================================
             */

            function escapeHtml(
                value
            ) {


                const div =
                    document.createElement(
                        'div'
                    );


                div.textContent =
                    value ?? '';


                return div.innerHTML;

            }


            /*
             * =========================================================
             * RETRY CAMERA
             * =========================================================
             */

            retryCameraButton.addEventListener(
                'click',
                function() {

                    startCamera();

                }
            );


            /*
             * =========================================================
             * START AGAIN
             * =========================================================
             */

            restartButton.addEventListener(
                'click',
                function() {

                    startCamera();

                }
            );


            /*
             * =========================================================
             * START CAMERA ON PAGE LOAD
             * =========================================================
             */

            startCamera();


            /*
             * =========================================================
             * CLEANUP
             * =========================================================
             */

            window.addEventListener(
                'beforeunload',
                function() {

                    stopScanning();

                    stopCamera();

                }
            );

        });
    </script>

</body>

</html>
