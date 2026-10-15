<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Face Sample - {{ $person->full_name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-3xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">

            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    Add Face Sample
                </h1>

                <p class="text-gray-500 mt-1">
                    {{ $person->full_name }}
                    — {{ $person->person_id }}
                </p>

            </div>

            <a href="{{ route('people.show', $person) }}"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg
                      hover:bg-gray-700">

                Back

            </a>

        </div>


        {{-- Validation Errors --}}
        @if ($errors->any())

            <div
                class="mb-6 bg-red-100 border border-red-300
                        text-red-800 rounded-lg px-4 py-3">

                <p class="font-semibold mb-2">
                    Please correct the following:
                </p>

                <ul class="list-disc list-inside text-sm">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Upload Form --}}
        <div class="bg-white rounded-xl shadow-sm
                    border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200">

                <h2 class="text-xl font-semibold text-gray-800">
                    Face Image
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Upload a clear photo of the person's face.
                </p>

            </div>


            <form method="POST" action="{{ route('people.faces.store', $person) }}" enctype="multipart/form-data"
                class="p-6">

                @csrf


                {{-- Image --}}
                <div>

                    <label class="block text-sm font-medium
                                  text-gray-700 mb-2">

                        Face Image

                    </label>

                    <input type="file" name="face_image"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required
                        class="w-full rounded-lg border border-gray-300
                               bg-white px-3 py-2 text-black
                               focus:border-blue-500
                               focus:ring-blue-500">

                    <p class="text-sm text-gray-500 mt-2">
                        JPG, JPEG, PNG, or WEBP. Maximum 5 MB.
                    </p>

                </div>


                {{-- Primary --}}
                <div class="mt-6">

                    <label class="inline-flex items-center">

                        <input type="checkbox" name="is_primary" value="1"
                            class="rounded border-gray-300
                                   text-blue-600
                                   focus:ring-blue-500">

                        <span class="ml-2 text-sm text-gray-700">
                            Set this as the primary face sample
                        </span>

                    </label>

                </div>


                {{-- Information --}}
                <div class="mt-6 bg-blue-50 border border-blue-200
                            rounded-lg p-4">

                    <p class="text-sm text-blue-800">

                        <strong>Tip:</strong>
                        Use a clear, front-facing photo with good lighting.
                        Avoid sunglasses, masks, heavy shadows, or images
                        where the face is partially hidden.

                    </p>

                </div>


                {{-- Buttons --}}
                <div class="flex justify-end gap-3 mt-8 pt-6
                            border-t border-gray-200">

                    <a href="{{ route('people.show', $person) }}"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700
                              rounded-lg hover:bg-gray-300">

                        Cancel

                    </a>

                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white
                               rounded-lg hover:bg-blue-700">

                        Upload Face Sample

                    </button>

                </div>

            </form>

        </div>

    </div>

</body>

</html>
