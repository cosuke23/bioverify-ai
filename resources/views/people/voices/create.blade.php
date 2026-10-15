<!DOCTYPE html>

<html lang="en">



<head>



    <meta charset="UTF-8">



    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <title>

        Record Voice - {{ $person->full_name }}

    </title>



    <script src="https://cdn.tailwindcss.com"></script>



    <style>
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

                opacity: 0.75;

            }



            100% {

                transform: scale(1);

                opacity: 1;

            }



        }



        button:disabled {

            cursor: not-allowed;

        }
    </style>



</head>





<body class="bg-gray-50 min-h-screen">





    <!-- =========================================================

         HEADER

    ========================================================== -->



    <header class="bg-white border-b border-gray-200">



        <div class="max-w-5xl mx-auto px-6 py-5">



            <div class="flex items-center justify-between">



                <div>



                    <h1 class="text-2xl font-bold text-gray-900">

                        Record Voice Sample

                    </h1>



                    <p class="text-sm text-gray-500 mt-1">

                        Record a voice sample for personnel recognition.

                    </p>



                </div>





                <a href="{{ route('recognized.person', $person) }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700

                           rounded-lg font-medium

                           hover:bg-gray-200 transition">

                    ← Back

                </a>



            </div>



        </div>



    </header>





    <!-- =========================================================

         CONTENT

    ========================================================== -->



    <main class="max-w-5xl mx-auto px-6 py-8">





        <!-- =====================================================

             PERSON INFORMATION

        ====================================================== -->



        <div class="bg-white rounded-2xl shadow-sm

                    border border-gray-200">



            <div class="px-6 py-4 border-b border-gray-200

                        bg-gray-50">



                <h2 class="text-lg font-semibold text-gray-900">

                    Personnel Information

                </h2>



            </div>





            <div class="p-6">



                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">





                    <!-- PERSONNEL ID -->



                    <div>



                        <p class="text-xs uppercase

                                  font-semibold text-gray-500">

                            Personnel ID

                        </p>



                        <p class="mt-1 font-semibold text-gray-900">

                            {{ $person->person_id }}

                        </p>



                    </div>





                    <!-- NAME -->



                    <div>



                        <p class="text-xs uppercase

                                  font-semibold text-gray-500">

                            Name

                        </p>



                        <p class="mt-1 font-semibold text-gray-900">

                            {{ $person->full_name }}

                        </p>



                    </div>





                    <!-- STATUS -->



                    <div>



                        <p class="text-xs uppercase

                                  font-semibold text-gray-500">

                            Status

                        </p>



                        <p class="mt-1">



                            <span
                                class="inline-flex px-2.5 py-1

                                       rounded-full text-xs font-semibold

                                       {{ $person->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">

                                {{ $person->status }}

                            </span>



                        </p>



                    </div>



                </div>



            </div>



        </div>





        <!-- =====================================================

             VOICE RECORDING

        ====================================================== -->



        <div class="bg-white rounded-2xl shadow-sm

                    border border-gray-200 mt-6">





            <!-- HEADER -->



            <div class="px-6 py-4 border-b border-gray-200

                        bg-gray-50">



                <h2 class="text-lg font-semibold text-gray-900">

                    Voice Recording

                </h2>



                <p class="text-sm text-gray-500 mt-1">

                    Make sure the microphone is working before

                    starting the recording.

                </p>



            </div>





            <!-- FORM -->



            <form method="POST" action="{{ route('people.voices.store', $person) }}" enctype="multipart/form-data"
                id="voiceForm">



                @csrf





                <div class="p-6">





                    <!-- =================================================

                         ERRORS

                    ================================================== -->



                    @if ($errors->any())



                        <div
                            class="mb-6 bg-red-50

                                   border border-red-200

                                   text-red-700 rounded-lg p-4">



                            <ul class="list-disc list-inside text-sm">



                                @foreach ($errors->all() as $error)
                                    <li>

                                        {{ $error }}

                                    </li>
                                @endforeach



                            </ul>



                        </div>



                    @endif





                    <!-- =================================================

                         RECORDING AREA

                    ================================================== -->



                    <div
                        class="border border-gray-200

                               rounded-2xl p-8

                               bg-gray-50">





                        <!-- MICROPHONE -->



                        <div class="text-center">



                            <div id="microphoneIcon"
                                class="mx-auto w-24 h-24

                                       rounded-full

                                       bg-purple-100

                                       flex items-center

                                       justify-center

                                       text-purple-600

                                       text-4xl">



                                🎤



                            </div>





                            <!-- STATUS -->



                            <h3 id="recordingStatus"
                                class="mt-5 text-xl

                                       font-semibold

                                       text-gray-900">



                                Ready to Record



                            </h3>





                            <!-- INSTRUCTION -->



                            <p id="recordingInstruction"
                                class="mt-2 text-sm

                                       text-gray-500">



                                Click Start Recording and speak

                                clearly into the microphone.



                            </p>





                            <!-- TIMER -->



                            <div id="recordingTimer"
                                class="mt-5 text-4xl

                                       font-mono

                                       font-bold

                                       text-gray-800">



                                00:00



                            </div>



                        </div>





                        <!-- =================================================

                             BUTTONS

                        ================================================== -->



                        <div
                            class="mt-8 flex

                                   justify-center

                                   gap-3">



                            <!-- START -->



                            <button type="button" id="startRecording"
                                class="inline-flex

                                       items-center gap-2

                                       px-6 py-3

                                       bg-purple-600

                                       text-white

                                       rounded-lg

                                       font-semibold

                                       hover:bg-purple-700

                                       transition">



                                🎤 Start Recording



                            </button>





                            <!-- STOP -->



                            <button type="button" id="stopRecording" disabled
                                class="inline-flex

                                       items-center gap-2

                                       px-6 py-3

                                       bg-red-600

                                       text-white

                                       rounded-lg

                                       font-semibold

                                       hover:bg-red-700

                                       transition

                                       disabled:opacity-50

                                       disabled:cursor-not-allowed">



                                ■ Stop Recording



                            </button>



                        </div>





                        <!-- =================================================

                             AUDIO PREVIEW

                        ================================================== -->



                        <div id="audioPreviewContainer" class="hidden mt-8">



                            <div
                                class="bg-white

                                       border

                                       border-gray-200

                                       rounded-xl

                                       p-5">





                                <p
                                    class="text-sm

                                           font-semibold

                                           text-gray-700

                                           mb-3">



                                    Recorded Voice



                                </p>





                                <audio id="audioPreview" controls preload="metadata" class="w-full">

                                </audio>





                                <!-- RECORD AGAIN -->



                                <button type="button" id="recordAgain"
                                    class="mt-4

                                           text-sm

                                           font-medium

                                           text-purple-600

                                           hover:text-purple-700">



                                    Record Again



                                </button>



                            </div>



                        </div>



                    </div>





                    <!-- =================================================

                         PRIMARY VOICE

                    ================================================== -->



                    <div class="mt-6">



                        <label class="inline-flex items-center">



                            <input type="checkbox" name="is_primary" value="1"
                                class="w-4 h-4

                                       text-purple-600

                                       border-gray-300

                                       rounded">



                            <span
                                class="ml-2

                                       text-sm

                                       text-gray-700">



                                Set this as the primary voice sample



                            </span>



                        </label>



                    </div>





                    <!-- =================================================

                         HIDDEN AUDIO INPUT

                    ================================================== -->



                    <input type="file" name="voice_audio" id="voice_audio" class="hidden"
                        accept="audio/webm,audio/ogg,audio/*">





                    <!-- =================================================

                         ACTIONS

                    ================================================== -->



                    <div
                        class="mt-8 flex

                               justify-end

                               gap-3">





                        <!-- CANCEL -->



                        <a href="{{ route('recognized.person', $person) }}"
                            class="px-5 py-2.5

                                   bg-gray-100

                                   text-gray-700

                                   rounded-lg

                                   font-medium

                                   hover:bg-gray-200

                                   transition">



                            Cancel



                        </a>





                        <!-- SAVE -->



                        <button type="submit" id="saveVoice" disabled
                            class="px-5 py-2.5

                                   bg-purple-600

                                   text-white

                                   rounded-lg

                                   font-semibold

                                   hover:bg-purple-700

                                   transition

                                   disabled:opacity-50

                                   disabled:cursor-not-allowed">



                            Save Voice Sample



                        </button>



                    </div>



                </div>



            </form>



        </div>



    </main>





    <!-- =============================================================

         JAVASCRIPT

    ============================================================== -->



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



        const startButton =

            document.getElementById('startRecording');



        const stopButton =

            document.getElementById('stopRecording');



        const recordAgainButton =

            document.getElementById('recordAgain');



        const audioPreview =

            document.getElementById('audioPreview');



        const audioPreviewContainer =

            document.getElementById('audioPreviewContainer');



        const saveButton =

            document.getElementById('saveVoice');



        const voiceAudioInput =

            document.getElementById('voice_audio');



        const recordingStatus =

            document.getElementById('recordingStatus');



        const recordingInstruction =

            document.getElementById('recordingInstruction');



        const recordingTimer =

            document.getElementById('recordingTimer');



        const microphoneIcon =

            document.getElementById('microphoneIcon');



        const voiceForm =

            document.getElementById('voiceForm');





        /*

        |--------------------------------------------------------------------------

        | SETTINGS

        |--------------------------------------------------------------------------

        */



        const TARGET_SAMPLE_RATE = 16000;





        /*

        |--------------------------------------------------------------------------

        | START RECORDING

        |--------------------------------------------------------------------------

        */



        startButton.addEventListener(

            'click',

            async function() {



                try {



                    /*

                    |--------------------------------------------------------------------------

                    | Check browser support

                    |--------------------------------------------------------------------------

                    */



                    if (

                        !navigator.mediaDevices ||

                        !navigator.mediaDevices.getUserMedia

                    ) {



                        alert(

                            'Your browser does not support microphone recording.'

                        );



                        return;



                    }





                    /*

                    |--------------------------------------------------------------------------

                    | Request microphone

                    |--------------------------------------------------------------------------

                    */



                    mediaStream =

                        await navigator.mediaDevices.getUserMedia({



                            audio: {



                                echoCancellation: false,



                                noiseSuppression: false,



                                autoGainControl: false



                            }



                        });





                    console.log(

                        'Microphone access granted.'

                    );





                    /*

                    |--------------------------------------------------------------------------

                    | Create AudioContext

                    |--------------------------------------------------------------------------

                    */



                    audioContext =

                        new(

                            window.AudioContext ||

                            window.webkitAudioContext

                        )();





                    /*

                    |--------------------------------------------------------------------------

                    | Resume AudioContext

                    |--------------------------------------------------------------------------

                    */



                    if (

                        audioContext.state === 'suspended'

                    ) {



                        await audioContext.resume();



                    }





                    console.log(

                        'AudioContext sample rate:',

                        audioContext.sampleRate

                    );





                    /*

                    |--------------------------------------------------------------------------

                    | Create microphone source

                    |--------------------------------------------------------------------------

                    */



                    sourceNode =

                        audioContext.createMediaStreamSource(

                            mediaStream

                        );





                    /*

                    |--------------------------------------------------------------------------

                    | Create processor

                    |--------------------------------------------------------------------------

                    |

                    | 4096 buffer

                    | 1 input channel

                    | 1 output channel

                    |

                    */



                    processorNode =

                        audioContext.createScriptProcessor(

                            4096,

                            1,

                            1

                        );





                    /*

                    |--------------------------------------------------------------------------

                    | Reset samples

                    |--------------------------------------------------------------------------

                    */



                    audioSamples = [];





                    /*

                    |--------------------------------------------------------------------------

                    | Receive microphone samples

                    |--------------------------------------------------------------------------

                    */



                    processorNode.onaudioprocess =

                        function(event) {



                            if (!isRecording) {



                                return;



                            }





                            const inputData =

                                event.inputBuffer.getChannelData(0);





                            /*

                            |--------------------------------------------------------------------------

                            | Copy samples

                            |--------------------------------------------------------------------------

                            */



                            const samples =

                                new Float32Array(

                                    inputData.length

                                );





                            samples.set(

                                inputData

                            );





                            audioSamples.push(

                                samples

                            );



                        };





                    /*

                    |--------------------------------------------------------------------------

                    | Connect microphone

                    |--------------------------------------------------------------------------

                    */



                    sourceNode.connect(

                        processorNode

                    );





                    /*

                    |--------------------------------------------------------------------------

                    | IMPORTANT

                    |

                    | Connect processor to destination so

                    | Chrome continues processing audio.

                    |

                    | The gain is ZERO so the user does not

                    | hear their own microphone.

                    |--------------------------------------------------------------------------

                    */



                    const silentGain =

                        audioContext.createGain();





                    silentGain.gain.value = 0;





                    processorNode.connect(

                        silentGain

                    );





                    silentGain.connect(

                        audioContext.destination

                    );





                    /*

                    |--------------------------------------------------------------------------

                    | Start

                    |--------------------------------------------------------------------------

                    */



                    isRecording = true;



                    recordingSeconds = 0;



                    updateTimer();





                    startButton.disabled =

                        true;



                    stopButton.disabled =

                        false;



                    saveButton.disabled =

                        true;





                    microphoneIcon.classList.add(

                        'recording-pulse'

                    );





                    recordingStatus.textContent =

                        'Recording...';





                    recordingInstruction.textContent =

                        'Speak clearly into the microphone.';





                    /*

                    |--------------------------------------------------------------------------

                    | Timer

                    |--------------------------------------------------------------------------

                    */



                    timerInterval =

                        setInterval(

                            function() {



                                recordingSeconds++;



                                updateTimer();





                                /*

                                |--------------------------------------------------------------------------

                                | Maximum 60 seconds

                                |--------------------------------------------------------------------------

                                */



                                if (

                                    recordingSeconds >= 60

                                ) {



                                    stopRecording();



                                }



                            },

                            1000

                        );





                    console.log(

                        'WAV recording started.'

                    );



                } catch (error) {



                    console.error(

                        'Microphone error:',

                        error

                    );





                    if (

                        error.name ===

                        'NotAllowedError'

                    ) {



                        alert(

                            'Microphone permission was denied. Please allow microphone access in Chrome.'

                        );



                    } else if (

                        error.name ===

                        'NotFoundError'

                    ) {



                        alert(

                            'No microphone was found.'

                        );



                    } else {



                        alert(

                            'Unable to access microphone: ' +

                            error.message

                        );



                    }



                }



            }

        );





        /*

        |--------------------------------------------------------------------------

        | STOP RECORDING

        |--------------------------------------------------------------------------

        */



        stopButton.addEventListener(

            'click',

            function() {



                stopRecording();



            }

        );





        function stopRecording() {



            if (!isRecording) {



                return;



            }





            console.log(

                'Stopping WAV recording...'

            );





            /*

            |--------------------------------------------------------------------------

            | Stop state first

            |--------------------------------------------------------------------------

            */



            isRecording = false;





            /*

            |--------------------------------------------------------------------------

            | Stop timer

            |--------------------------------------------------------------------------

            */



            stopTimer();





            /*

            |--------------------------------------------------------------------------

            | Stop processor

            |--------------------------------------------------------------------------

            */



            if (processorNode) {



                processorNode.disconnect();



                processorNode.onaudioprocess = null;



                processorNode = null;



            }





            /*

            |--------------------------------------------------------------------------

            | Stop source

            |--------------------------------------------------------------------------

            */



            if (sourceNode) {



                sourceNode.disconnect();



                sourceNode = null;



            }





            /*

            |--------------------------------------------------------------------------

            | Stop microphone

            |--------------------------------------------------------------------------

            */



            stopMicrophone();





            /*

            |--------------------------------------------------------------------------

            | Create WAV

            |--------------------------------------------------------------------------

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



                mediaStream

                    .getTracks()

                    .forEach(

                        function(track) {



                            track.stop();



                        }

                    );





                mediaStream = null;



            }



        }





        /*

        |--------------------------------------------------------------------------

        | CREATE WAV

        |--------------------------------------------------------------------------

        */



        function createWavRecording() {



            console.log(

                'Audio sample buffers:',

                audioSamples.length

            );





            if (

                !audioSamples ||

                audioSamples.length === 0

            ) {



                alert(

                    'No audio samples were captured. Please try again.'

                );





                resetRecordingUI();



                return;



            }





            /*

            |--------------------------------------------------------------------------

            | Calculate total samples

            |--------------------------------------------------------------------------

            */



            let totalLength = 0;





            for (

                let i = 0; i < audioSamples.length; i++

            ) {



                totalLength +=

                    audioSamples[i].length;



            }





            console.log(

                'Total PCM samples:',

                totalLength

            );





            /*

            |--------------------------------------------------------------------------

            | Merge Float32 arrays

            |--------------------------------------------------------------------------

            */



            const mergedSamples =

                new Float32Array(

                    totalLength

                );





            let offset = 0;





            for (

                let i = 0; i < audioSamples.length; i++

            ) {



                mergedSamples.set(

                    audioSamples[i],

                    offset

                );





                offset +=

                    audioSamples[i].length;



            }





            /*

            |--------------------------------------------------------------------------

            | Original sample rate

            |--------------------------------------------------------------------------

            */



            const originalSampleRate =

                audioContext.sampleRate;





            console.log(

                'Original sample rate:',

                originalSampleRate

            );





            /*

            |--------------------------------------------------------------------------

            | Resample to 16 kHz

            |--------------------------------------------------------------------------

            */



            const resampledSamples =

                resampleAudio(

                    mergedSamples,

                    originalSampleRate,

                    TARGET_SAMPLE_RATE

                );





            console.log(

                'Target sample rate:',

                TARGET_SAMPLE_RATE

            );





            console.log(

                'Resampled samples:',

                resampledSamples.length

            );





            /*

            |--------------------------------------------------------------------------

            | Convert to WAV

            |--------------------------------------------------------------------------

            */



            recordedBlob =

                encodeWAV(

                    resampledSamples,

                    TARGET_SAMPLE_RATE

                );





            console.log(

                '================================'

            );





            console.log(

                'FINAL WAV RECORDING'

            );





            console.log(

                'Type:',

                recordedBlob.type

            );





            console.log(

                'Size:',

                recordedBlob.size

            );





            console.log(

                'Duration:',

                (

                    resampledSamples.length /

                    TARGET_SAMPLE_RATE

                ).toFixed(2) +

                ' seconds'

            );





            console.log(

                '================================'

            );





            /*

            |--------------------------------------------------------------------------

            | Validate

            |--------------------------------------------------------------------------

            */



            if (

                !recordedBlob ||

                recordedBlob.size === 0

            ) {



                alert(

                    'The WAV recording is empty. Please try again.'

                );





                resetRecordingUI();



                return;



            }





            /*

            |--------------------------------------------------------------------------

            | Create preview URL

            |--------------------------------------------------------------------------

            */



            if (audioObjectUrl) {



                URL.revokeObjectURL(

                    audioObjectUrl

                );



            }





            audioObjectUrl =

                URL.createObjectURL(

                    recordedBlob

                );





            /*

            |--------------------------------------------------------------------------

            | Set audio preview

            |--------------------------------------------------------------------------

            */



            previewHasFinished = false;

            saveButton.disabled = true;

            audioPreview.src =

                audioObjectUrl;





            audioPreview.controls =

                true;





            audioPreview.preload =

                'metadata';





            audioPreview.load();





            /*

            |--------------------------------------------------------------------------

            | Show preview

            |--------------------------------------------------------------------------

            */



            audioPreviewContainer

                .classList

                .remove('hidden');





            /*

            |--------------------------------------------------------------------------

            | Update UI

            |--------------------------------------------------------------------------

            */



            recordingStatus.textContent =

                'Recording Complete';





            recordingInstruction.textContent =

                'Play the recording below before saving.';





            startButton.disabled =

                false;





            stopButton.disabled =

                true;





            // Saving is enabled only after the full preview has played.
            saveButton.disabled = true;





            microphoneIcon.classList.remove(

                'recording-pulse'

            );





            /*

            |--------------------------------------------------------------------------

            | Close AudioContext

            |--------------------------------------------------------------------------

            */



            if (audioContext) {



                audioContext.close();



                audioContext = null;



            }



        }





        /*

        |--------------------------------------------------------------------------

        | RESAMPLE AUDIO

        |--------------------------------------------------------------------------

        */



        function resampleAudio(

            inputSamples,

            inputSampleRate,

            outputSampleRate

        ) {



            if (

                inputSampleRate ===

                outputSampleRate

            ) {



                return inputSamples;



            }





            const ratio =

                inputSampleRate /

                outputSampleRate;





            const newLength =

                Math.round(

                    inputSamples.length /

                    ratio

                );





            const output =

                new Float32Array(

                    newLength

                );





            /*

            |--------------------------------------------------------------------------

            | Linear interpolation

            |--------------------------------------------------------------------------

            */



            for (

                let i = 0; i < newLength; i++

            ) {



                const position =

                    i * ratio;





                const index =

                    Math.floor(

                        position

                    );





                const fraction =

                    position - index;





                const sample1 =

                    inputSamples[index] ||

                    0;





                const sample2 =

                    inputSamples[index + 1] ||

                    sample1;





                output[i] =

                    sample1 +

                    (

                        sample2 -

                        sample1

                    ) *

                    fraction;



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



        function writeString(

            view,

            offset,

            string

        ) {



            for (

                let i = 0; i < string.length; i++

            ) {



                view.setUint8(

                    offset + i,

                    string.charCodeAt(i)

                );



            }



        }





        /*

        |--------------------------------------------------------------------------

        | FLOAT TO PCM

        |--------------------------------------------------------------------------

        */



        function floatTo16BitPCM(

            output,

            offset,

            input

        ) {



            for (

                let i = 0; i < input.length; i++,

                offset += 2

            ) {



                let sample =

                    Math.max(

                        -1,

                        Math.min(

                            1,

                            input[i]

                        )

                    );





                /*

                |--------------------------------------------------------------------------

                | Convert Float32 [-1,1]

                | to Int16

                |--------------------------------------------------------------------------

                */



                const value =

                    sample < 0 ?

                    sample * 0x8000 :

                    sample * 0x7FFF;





                output.setInt16(

                    offset,

                    value,

                    true

                );



            }



        }





        /*

        |--------------------------------------------------------------------------

        | TIMER

        |--------------------------------------------------------------------------

        */



        function updateTimer() {



            const minutes =

                Math.floor(

                    recordingSeconds / 60

                );





            const seconds =

                recordingSeconds % 60;





            recordingTimer.textContent =

                String(minutes)

                .padStart(2, '0') +

                ':' +

                String(seconds)

                .padStart(2, '0');



        }





        function stopTimer() {



            if (timerInterval) {



                clearInterval(

                    timerInterval

                );



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
                    console.warn(
                        'Automatic preview playback was blocked; use the audio player Play button.',
                        error);
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



        recordAgainButton.addEventListener(

            'click',

            function() {



                /*

                |--------------------------------------------------------------------------

                | Stop preview

                |--------------------------------------------------------------------------

                */



                audioPreview.pause();





                audioPreview.removeAttribute(

                    'src'

                );





                audioPreview.load();





                /*

                |--------------------------------------------------------------------------

                | Release object URL

                |--------------------------------------------------------------------------

                */



                if (audioObjectUrl) {



                    URL.revokeObjectURL(

                        audioObjectUrl

                    );



                    audioObjectUrl = null;



                }





                /*

                |--------------------------------------------------------------------------

                | Reset recording

                |--------------------------------------------------------------------------

                */



                audioSamples = [];

                recordedBlob = null;

                previewHasFinished = false;





                /*

                |--------------------------------------------------------------------------

                | Clear file

                |--------------------------------------------------------------------------

                */



                voiceAudioInput.value = '';





                /*

                |--------------------------------------------------------------------------

                | Hide preview

                |--------------------------------------------------------------------------

                */



                audioPreviewContainer

                    .classList

                    .add('hidden');





                /*

                |--------------------------------------------------------------------------

                | Reset timer

                |--------------------------------------------------------------------------

                */



                recordingSeconds = 0;



                updateTimer();





                /*

                |--------------------------------------------------------------------------

                | Reset buttons

                |--------------------------------------------------------------------------

                */



                startButton.disabled =

                    false;





                stopButton.disabled =

                    true;





                saveButton.disabled =

                    true;





                /*

                |--------------------------------------------------------------------------

                | Reset text

                |--------------------------------------------------------------------------

                */



                recordingStatus.textContent =

                    'Ready to Record';





                recordingInstruction.textContent =

                    'Click Start Recording and speak clearly into the microphone.';





            }

        );





        /*

        |--------------------------------------------------------------------------

        | FORM SUBMIT

        |--------------------------------------------------------------------------

        */



        voiceForm.addEventListener(

            'submit',

            function(event) {



                /*

                |--------------------------------------------------------------------------

                | Validate

                |--------------------------------------------------------------------------

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





                /*

                |--------------------------------------------------------------------------

                | Debug

                |--------------------------------------------------------------------------

                */



                console.log(

                    '================================'

                );





                console.log(

                    'UPLOADING WAV FILE'

                );





                console.log(

                    'Name:',

                    audioFile.name

                );





                console.log(

                    'Type:',

                    audioFile.type

                );





                console.log(

                    'Size:',

                    audioFile.size

                );





                console.log(

                    '================================'

                );



            }

        );





        /*

        |--------------------------------------------------------------------------

        | CLEANUP WHEN LEAVING PAGE

        |--------------------------------------------------------------------------

        */



        window.addEventListener(

            'beforeunload',

            function() {



                stopTimer();



                stopMicrophone();





                if (audioContext) {



                    audioContext.close();



                }





                if (audioObjectUrl) {



                    URL.revokeObjectURL(

                        audioObjectUrl

                    );



                }



            }

        );
    </script>



</body>



</html>
