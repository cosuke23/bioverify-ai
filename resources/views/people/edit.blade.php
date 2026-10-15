<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit {{ $person->full_name }} - BioVerify AI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-4xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Edit Person
                </h1>

                <p class="text-gray-500 mt-1">
                    Update person information
                </p>
            </div>

            <a href="{{ route('people.show', $person) }}"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Back
            </a>

        </div>


        {{-- Validation Errors --}}
        @if($errors->any())

            <div class="mb-6 bg-red-100 border border-red-300
                        text-red-800 rounded-lg px-4 py-3">

                <p class="font-semibold mb-2">
                    Please correct the following:
                </p>

                <ul class="list-disc list-inside text-sm">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Edit Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200">

                <h2 class="text-xl font-semibold text-gray-800">
                    Personal Information
                </h2>

            </div>


            <form method="POST"
                  action="{{ route('people.update', $person) }}"
                  class="p-6">

                @csrf

                @method('PUT')


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    {{-- Person ID --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Person ID
                        </label>

                        <input
                            type="text"
                            name="person_id"
                            value="{{ old('person_id', $person->person_id) }}"
                            required
                            maxlength="50"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        @error('person_id')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Status --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>

                        <select
                            name="status"
                            required
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                            <option value="Active"
                                {{ old('status', $person->status) === 'Active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="Inactive"
                                {{ old('status', $person->status) === 'Inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                        @error('status')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- First Name --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            First Name
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            value="{{ old('first_name', $person->first_name) }}"
                            required
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        @error('first_name')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Middle Name --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Middle Name
                        </label>

                        <input
                            type="text"
                            name="middle_name"
                            value="{{ old('middle_name', $person->middle_name) }}"
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        @error('middle_name')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Last Name --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="{{ old('last_name', $person->last_name) }}"
                            required
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        @error('last_name')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Suffix --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Suffix
                        </label>

                        <input
                            type="text"
                            name="suffix"
                            value="{{ old('suffix', $person->suffix) }}"
                            maxlength="20"
                            placeholder="Jr., Sr., III"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        @error('suffix')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $person->email) }}"
                            maxlength="255"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        @error('email')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Mobile Number --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Number
                        </label>

                        <input
                            type="text"
                            name="mobile_number"
                            value="{{ old('mobile_number', $person->mobile_number) }}"
                            maxlength="30"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500
                                   text-black"
                        >

                        @error('mobile_number')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="flex justify-end gap-3 mt-8 pt-6
                            border-t border-gray-200">

                    <a href="{{ route('people.show', $person) }}"
                       class="px-5 py-2.5 bg-gray-200 text-gray-700
                              rounded-lg hover:bg-gray-300">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white
                               rounded-lg hover:bg-blue-700">

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</body>
</html>