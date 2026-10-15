<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Face Recognition - BioVerify AI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">

            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Face Recognition
                </h1>

                <p class="text-gray-500 mt-1">
                    Identify a registered person using facial recognition.
                </p>

            </div>

            <div class="flex gap-3">

                <a href="{{ route('people.index') }}"
                    class="px-5 py-3 bg-gray-600 hover:bg-gray-700
                           text-white rounded-lg font-semibold">
                    ← People
                </a>

            </div>

        </div>


        {{-- Main Card --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">

            <div class="p-6">

                {{-- Information --}}
                <div
                    class="mb-6 bg-blue-50 border border-blue-200
                           text-blue-800 px-5 py-4 rounded-lg">

                    <div class="font-semibold mb-1">
                        How Face Recognition Works
                    </div>

                    <div class="text-sm">

                        Upload an image containing exactly one face.
                        BioVerify AI will compare the face against
                        registered face samples.

                    </div>

                </div>


                {{-- Form --}}
                <form id="recognitionForm" enctype="multipart/form-data">

                    @csrf

                    <div class="mb-5">

                        <label for="face_image"
                            class="block text-sm font-semibold
                                   text-gray-700 mb-2">
                            Face Image
                        </label>

                        <input type="file" id="face_image" name="face_image" accept="image/jpeg,image/png,image/webp"
                            required
                            class="block w-full text-sm text-gray-700
                                   border border-gray-300 rounded-lg
                                   cursor-pointer bg-gray-50
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500">

                        <p class="mt-2 text-xs text-gray-500">
                            JPG, JPEG, PNG or WEBP. Maximum 5 MB.
                        </p>

                    </div>


                    {{-- Preview --}}
                    <div id="previewContainer" class="mb-6 hidden">

                        <div class="text-sm font-semibold text-gray-700 mb-2">
                            Image Preview
                        </div>

                        <div class="flex justify-center">

                            <img id="previewImage" src="" alt="Face preview"
                                class="max-h-96 rounded-xl border
                                       border-gray-200 shadow-sm">

                        </div>

                    </div>


                    {{-- Button --}}
                    <button type="submit" id="recognizeButton"
                        class="w-full bg-blue-600 hover:bg-blue-700
                               text-white px-5 py-3 rounded-lg
                               font-semibold transition">

                        🔍 Recognize Face

                    </button>

                </form>


                {{-- Loading --}}
                <div id="loading" class="hidden mt-6">

                    <div
                        class="flex items-center justify-center
                               bg-gray-50 border rounded-lg
                               px-5 py-6">

                        <div class="text-center">

                            <div
                                class="inline-block w-8 h-8
                                       border-4 border-blue-200
                                       border-t-blue-600
                                       rounded-full animate-spin">
                            </div>

                            <div class="mt-3 text-sm
                                       text-gray-600">
                                Analyzing face...
                            </div>

                            <div class="mt-1 text-xs
                                       text-gray-400">
                                Please wait while BioVerify AI
                                compares the face.
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Result --}}
                <div id="result" class="hidden mt-6"></div>

            </div>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form =
                document.getElementById('recognitionForm');

            const imageInput =
                document.getElementById('face_image');

            const previewContainer =
                document.getElementById('previewContainer');

            const previewImage =
                document.getElementById('previewImage');

            const loading =
                document.getElementById('loading');

            const result =
                document.getElementById('result');

            const button =
                document.getElementById('recognizeButton');


            /*
            |--------------------------------------------------------------------------
            | Image Preview
            |--------------------------------------------------------------------------
            */

            imageInput.addEventListener('change', function() {

                const file = this.files[0];

                result.classList.add('hidden');
                result.innerHTML = '';

                if (!file) {

                    previewContainer.classList.add('hidden');

                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {

                    previewImage.src =
                        event.target.result;

                    previewContainer.classList.remove(
                        'hidden'
                    );
                };

                reader.readAsDataURL(file);

            });


            /*
            |--------------------------------------------------------------------------
            | Recognition
            |--------------------------------------------------------------------------
            */

            form.addEventListener('submit', async function(event) {

                event.preventDefault();


                const file =
                    imageInput.files[0];


                if (!file) {

                    showError(
                        'Please select a face image.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Start Loading
                |--------------------------------------------------------------------------
                */

                loading.classList.remove('hidden');

                result.classList.add('hidden');

                result.innerHTML = '';

                button.disabled = true;

                button.classList.add(
                    'opacity-50',
                    'cursor-not-allowed'
                );


                /*
                |--------------------------------------------------------------------------
                | Form Data
                |--------------------------------------------------------------------------
                */

                const formData =
                    new FormData();

                formData.append(
                    'face_image',
                    file
                );

                formData.append(
                    '_token',
                    '{{ csrf_token() }}'
                );


                try {

                    const response =
                        await fetch(
                            '{{ route('face.recognition.recognize') }}', {
                                method: 'POST',

                                body: formData,

                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                    const data =
                        await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | Request Failed
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok || !data.success) {

                        showError(
                            data.message ??
                            'Unable to recognize the face.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | No Match
                    |--------------------------------------------------------------------------
                    */

                    if (!data.matched) {

                        result.innerHTML = `

                    <div
                        class="bg-yellow-50
                               border border-yellow-300
                               rounded-xl p-6"
                    >

                        <div class="flex items-start">

                            <div class="text-3xl mr-4">
                                ⚠️
                            </div>

                            <div>

                                <h3
                                    class="text-lg font-bold
                                           text-yellow-800"
                                >
                                    No Match
                                </h3>

                                <p
                                    class="mt-1 text-sm
                                           text-yellow-700"
                                >
                                    No registered person matched
                                    this face.
                                </p>

                            </div>

                        </div>

                    </div>

                `;

                        result.classList.remove('hidden');

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Match Found
                    |--------------------------------------------------------------------------
                    */

                    const personName =
                        data.person?.name ??
                        'Unknown Person';

                    const personId =
                        data.person?.person_id ??
                        data.person_id ??
                        'N/A';

                    const score =
                        data.similarity_score !== undefined ?
                        Number(
                            data.similarity_score
                        ).toFixed(5) :
                        'N/A';


                    result.innerHTML = `

                <div
                    class="bg-green-50
                           border border-green-300
                           rounded-xl p-6"
                >

                    <div class="flex items-start">

                        <div class="text-3xl mr-4">
                            ✓
                        </div>

                        <div class="flex-1">

                            <h3
                                class="text-xl font-bold
                                       text-green-800"
                            >
                                Face Match Found
                            </h3>

                            <p
                                class="text-sm
                                       text-green-700 mt-1"
                            >
                                BioVerify AI found a registered
                                face with the highest similarity.
                            </p>


                            <div
                                class="grid grid-cols-1
                                       md:grid-cols-3 gap-4
                                       mt-6"
                            >

                                <div
                                    class="bg-white rounded-lg
                                           border p-4"
                                >

                                    <div
                                        class="text-xs
                                               text-gray-500
                                               uppercase"
                                    >
                                        Person ID
                                    </div>

                                    <div
                                        class="mt-1 text-lg
                                               font-bold
                                               text-gray-800"
                                    >
                                        ${escapeHtml(personId)}
                                    </div>

                                </div>


                                <div
                                    class="bg-white rounded-lg
                                           border p-4"
                                >

                                    <div
                                        class="text-xs
                                               text-gray-500
                                               uppercase"
                                    >
                                        Name
                                    </div>

                                    <div
                                        class="mt-1 text-lg
                                               font-bold
                                               text-gray-800"
                                    >
                                        ${escapeHtml(personName)}
                                    </div>

                                </div>


                                <div
                                    class="bg-white rounded-lg
                                           border p-4"
                                >

                                    <div
                                        class="text-xs
                                               text-gray-500
                                               uppercase"
                                    >
                                        Similarity Score
                                    </div>

                                    <div
                                        class="mt-1 text-lg
                                               font-bold
                                               text-blue-600"
                                    >
                                        ${score}
                                    </div>

                                </div>

                            </div>


                            <div
                                class="mt-5 text-xs
                                       text-gray-500"
                            >

                                AI Model:
                                ${escapeHtml(
                                    data.model_version ??
                                    'N/A'
                                )}

                            </div>

                        </div>

                    </div>

                </div>

            `;

                    result.classList.remove('hidden');

                } catch (error) {

                    showError(
                        'Unable to connect to the Laravel recognition endpoint.'
                    );

                } finally {

                    loading.classList.add('hidden');

                    button.disabled = false;

                    button.classList.remove(
                        'opacity-50',
                        'cursor-not-allowed'
                    );

                }

            });


            /*
            |--------------------------------------------------------------------------
            | Error Display
            |--------------------------------------------------------------------------
            */

            function showError(message) {

                result.innerHTML = `

            <div
                class="bg-red-50
                       border border-red-300
                       rounded-xl p-6"
            >

                <div class="flex items-start">

                    <div class="text-3xl mr-4">
                        ✕
                    </div>

                    <div>

                        <h3
                            class="text-lg font-bold
                                   text-red-800"
                        >
                            Recognition Failed
                        </h3>

                        <p
                            class="mt-1 text-sm
                                   text-red-700"
                        >
                            ${escapeHtml(message)}
                        </p>

                    </div>

                </div>

            </div>

        `;

                result.classList.remove('hidden');
            }


            /*
            |--------------------------------------------------------------------------
            | HTML Escape
            |--------------------------------------------------------------------------
            */

            function escapeHtml(value) {

                const div =
                    document.createElement('div');

                div.textContent =
                    value ?? '';

                return div.innerHTML;
            }

        });
    </script>

</body>

</html>
