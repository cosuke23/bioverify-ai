<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Recognized Personnel - BioVerify AI
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-6xl mx-auto px-4 py-8">

        <!-- HEADER -->

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-blue-600 font-semibold">
                        BIOVERIFY AI
                    </p>

                    <h1 class="text-2xl font-bold text-gray-900 mt-1">
                        Recognized Personnel
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Personnel record identified through face recognition.
                    </p>

                </div>

                @auth
                    <a href="{{ url('/face-recognition') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                        Back to Recognition
                    </a>
                @else
                    {{-- Ends the verified session for this person. --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                            Done
                        </button>
                    </form>
                @endauth

            </div>

        </div>


        <!-- PERSONNEL INFORMATION -->

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">

                <h2 class="text-lg font-semibold text-gray-900">
                    Personnel Information
                </h2>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- PERSON ID -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            Person ID
                        </label>

                        <div class="text-lg font-semibold text-gray-900">
                            {{ $person->person_id }}
                        </div>

                    </div>


                    <!-- STATUS -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            Status
                        </label>

                        <div>

                            @if ($person->status === 'Active')
                                <span
                                    class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Active
                                </span>
                            @else
                                <span
                                    class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    Inactive
                                </span>
                            @endif

                        </div>

                    </div>


                    <!-- FIRST NAME -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            First Name
                        </label>

                        <div class="text-base font-medium text-gray-900">
                            {{ $person->first_name }}
                        </div>

                    </div>


                    <!-- MIDDLE NAME -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            Middle Name
                        </label>

                        <div class="text-base font-medium text-gray-900">
                            {{ $person->middle_name ?: '—' }}
                        </div>

                    </div>


                    <!-- LAST NAME -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            Last Name
                        </label>

                        <div class="text-base font-medium text-gray-900">
                            {{ $person->last_name }}
                        </div>

                    </div>


                    <!-- SUFFIX -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            Suffix
                        </label>

                        <div class="text-base font-medium text-gray-900">
                            {{ $person->suffix ?: '—' }}
                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            Email
                        </label>

                        <div class="text-base font-medium text-gray-900">
                            {{ $person->email ?: '—' }}
                        </div>

                    </div>


                    <!-- MOBILE -->

                    <div>

                        <label class="block text-sm font-medium text-gray-500 mb-1">
                            Mobile Number
                        </label>

                        <div class="text-base font-medium text-gray-900">
                            {{ $person->mobile_number ?: '—' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- REGISTERED FACE SAMPLES -->

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mt-6">

            <!-- SECTION HEADER -->

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-lg font-semibold text-gray-900">
                            Registered Face Samples
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Photos used for face recognition.
                        </p>

                    </div>


                    <!-- ACTION BUTTONS -->

                    @auth
                    <div class="flex items-center gap-2">

                        <!-- ADD PHOTO -->

                        <a href="{{ route('people.faces.create', $person) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7h3l2-3h8l2 3h3a1 1 0 011 1v11a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z" />

                                <circle cx="12" cy="13" r="3" />

                            </svg>

                            Add Photo

                        </a>


                        <!-- ADD VOICE -->

                        <a href="{{ route('people.voices.create', $person) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 1v11m0 0a4 4 0 004-4V5a4 4 0 00-8 0v3a4 4 0 004 4zm7-4v3a7 7 0 01-14 0V8m7 7v4m-4 0h8" />

                            </svg>

                            Add Voice

                        </a>

                    </div>
                    @endauth

                </div>

            </div>


            <!-- FACE SAMPLES -->

            <div class="p-6">

                @if ($person->faces->count())

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                        @foreach ($person->faces as $face)
                            <div class="border border-gray-200 rounded-xl overflow-hidden">

                                <img src="{{ route('biometrics.face', $face) }}" class="w-full h-40 object-cover"
                                    alt="Registered face">

                                <div class="p-3">

                                    @if ($face->is_primary)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            Primary
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">
                                            Face Sample
                                        </span>
                                    @endif

                                </div>

                            </div>
                        @endforeach

                    </div>
                @else
                    <div class="text-center py-10">

                        <div class="text-gray-400 mb-3">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 7h3l2-3h8l2 3h3a1 1 0 011 1v11a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z" />

                            </svg>

                        </div>

                        <p class="text-sm text-gray-500">
                            No registered face samples found.
                        </p>

                    </div>

                @endif

            </div>

        </div>


        <!-- REGISTERED VOICE SAMPLES -->

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mt-6">

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-lg font-semibold text-gray-900">
                            Registered Voice Samples
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Voice recordings used for voice recognition.
                        </p>

                    </div>


                    <!-- ADD VOICE -->

                    @auth
                    <a href="{{ route('people.voices.create', $person) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 1v11m0 0a4 4 0 004-4V5a4 4 0 00-8 0v3a4 4 0 004 4zm7-4v3a7 7 0 01-14 0V8m7 7v4m-4 0h8" />

                        </svg>

                        Add Voice

                    </a>
                    @endauth

                </div>

            </div>


            <div class="p-6">

                @if ($person->voices->count())

                    <div class="space-y-3">

                        @foreach ($person->voices as $voice)
                            <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between">

                                <div class="flex items-center gap-4">

                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 1v11m0 0a4 4 0 004-4V5a4 4 0 00-8 0v3a4 4 0 004 4zm7-4v3a7 7 0 01-14 0V8m7 7v4m-4 0h8" />

                                        </svg>

                                    </div>


                                    <div>

                                        <div class="text-sm font-semibold text-gray-900">
                                            Voice Sample
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $voice->created_at?->format('M d, Y h:i A') }}
                                        </div>

                                    </div>

                                </div>


                                <div class="flex items-center gap-3">

                                    @if ($voice->is_primary)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                            Primary
                                        </span>
                                    @endif

                                    <audio controls class="h-9">

                                        <source src="{{ route('biometrics.voice', $voice) }}">

                                        Your browser does not support audio playback.

                                    </audio>

                                </div>

                            </div>
                        @endforeach

                    </div>
                @else
                    <div class="text-center py-10">

                        <div class="text-gray-400 mb-3">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 1v11m0 0a4 4 0 004 4V5a4 4 0 00-8 0v3a4 4 0 004 4zm7-4v3a7 7 0 01-14 0V8m7 7v4m-4 0h8" />

                            </svg>

                        </div>

                        <p class="text-sm text-gray-500">
                            No registered voice samples found.
                        </p>

                    </div>

                @endif

            </div>

        </div>


        <!-- RECOGNITION HISTORY -->

        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-xl font-bold text-gray-900">
                            Recognition History
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Previous face and voice recognition activity for this personnel.
                        </p>

                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $person->recognitionLogs->count() }} record(s)
                    </div>

                </div>

            </div>


            @if ($person->recognitionLogs->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Recognition Type
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Face Score
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Voice Score
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Combined Score
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Result
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Source
                                </th>

                            </tr>

                        </thead>


                        <tbody class="bg-white divide-y divide-gray-200">

                            @foreach ($person->recognitionLogs as $log)
                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $log->created_at?->format('M d, Y') }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $log->created_at?->format('h:i A') }}
                                        </div>

                                    </td>


                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $log->recognition_type === 'Face'
                                                ? 'bg-blue-100 text-blue-700'
                                                : ($log->recognition_type === 'Voice'
                                                    ? 'bg-purple-100 text-purple-700'
                                                    : 'bg-indigo-100 text-indigo-700') }}">

                                            {{ $log->recognition_type }}

                                        </span>

                                    </td>


                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if ($log->face_score !== null)
                                            <span class="text-sm font-semibold text-gray-900">
                                                {{ number_format((float) $log->face_score, 5) }}
                                            </span>

                                            <div class="text-xs text-gray-500">
                                                {{ number_format((float) $log->face_score * 100, 2) }}%
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">
                                                —
                                            </span>
                                        @endif

                                    </td>


                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if ($log->voice_score !== null)
                                            <span class="text-sm font-semibold text-gray-900">
                                                {{ number_format((float) $log->voice_score, 5) }}
                                            </span>

                                            <div class="text-xs text-gray-500">
                                                {{ number_format((float) $log->voice_score * 100, 2) }}%
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">
                                                —
                                            </span>
                                        @endif

                                    </td>


                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if ($log->combined_score !== null)
                                            <span class="text-sm font-semibold text-gray-900">
                                                {{ number_format((float) $log->combined_score, 5) }}
                                            </span>

                                            <div class="text-xs text-gray-500">
                                                {{ number_format((float) $log->combined_score * 100, 2) }}%
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">
                                                —
                                            </span>
                                        @endif

                                    </td>


                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if ($log->result === 'Matched')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                Matched
                                            </span>
                                        @elseif ($log->result === 'Review Required')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                Review Required
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                Not Matched
                                            </span>
                                        @endif

                                    </td>


                                    <td class="px-6 py-4 whitespace-nowrap">

                                        <span class="text-sm text-gray-600">
                                            {{ $log->source ?: '—' }}
                                        </span>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>
            @else
                <div class="px-6 py-12 text-center">

                    <div class="text-gray-400 text-4xl mb-3">
                        🕘
                    </div>

                    <h3 class="text-sm font-semibold text-gray-700">
                        No Recognition History
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        This personnel has not been used for recognition yet.
                    </p>

                </div>

            @endif

        </div>


    </div>

</body>

</html>
