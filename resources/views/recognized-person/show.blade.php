<!DOCTYPE html>

<html lang="en">



<head>



    <meta charset="UTF-8">



    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <title>

        Recognized Personnel - BioVerify AI

    </title>



    <script src="https://cdn.tailwindcss.com"></script>




    <style>
        :root {
            --pcg-blue: #0033A0;
            --pcg-blue-dark: #00246f;
            --pcg-gold: #f4c542;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 50;
            display: flex;
            flex-direction: column;
            width: 280px;
            color: #fff;
            background: linear-gradient(180deg, var(--pcg-blue), var(--pcg-blue-dark));
            box-shadow: 8px 0 28px rgba(15, 23, 42, .12);
            transition: width .25s ease, transform .25s ease;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
            font-weight: 900;
            color: var(--pcg-gold);
        }

        .gold-line {
            height: 3px;
            flex: 0 0 3px;
            background: var(--pcg-gold);
        }

        .section-label {
            color: #bfdbfe;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 46px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 12px;
            color: #dbeafe;
            text-decoration: none;
            white-space: nowrap;
            transition: background .2s, color .2s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .10);
            color: #fff;
        }

        .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .15);
            border-color: rgba(255, 255, 255, .14);
            box-shadow: inset 3px 0 0 var(--pcg-gold);
        }

        .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            min-width: 24px;
            font-size: 21px;
        }

        .service-card {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .service-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, .12);
        }

        .page-content {
            max-width: 1152px;
            margin: 0 auto 0 280px;
            padding: 32px 24px;
            transition: margin-left .25s ease;
        }

        body.sidebar-collapsed .sidebar {
            width: 82px;
        }

        body.sidebar-collapsed .page-content {
            margin-left: 82px;
        }

        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .nav-badge {
            display: none;
        }

        body.sidebar-collapsed .sidebar-workspace {
            justify-content: center;
            padding-left: 10px;
            padding-right: 10px;
        }

        body.sidebar-collapsed .nav-link {
            justify-content: center;
            padding-left: 8px;
            padding-right: 8px;
        }

        body.sidebar-collapsed .sidebar-toggle-icon {
            transform: rotate(180deg);
        }

        #sidebarOverlay {
            display: none;
        }

        #mobileSidebarToggle {
            display: none;
        }

        @media (max-width: 767px) {
            .sidebar {
                width: 280px;
                transform: translateX(-100%);
            }

            body.mobile-sidebar-open .sidebar {
                transform: translateX(0);
            }

            .page-content,
            body.sidebar-collapsed .page-content {
                margin-left: 0;
                padding: 72px 14px 24px;
            }

            body.sidebar-collapsed .sidebar {
                width: 280px;
            }

            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .nav-badge {
                display: initial;
            }

            body.sidebar-collapsed .sidebar-workspace {
                justify-content: space-between;
                padding-left: 20px;
                padding-right: 20px;
            }

            body.sidebar-collapsed .nav-link {
                justify-content: flex-start;
                padding: 10px 12px;
            }

            #sidebarOverlay {
                position: fixed;
                inset: 0;
                z-index: 40;
                background: rgba(2, 6, 23, .6);
            }

            body.mobile-sidebar-open #sidebarOverlay {
                display: block;
            }

            #mobileSidebarToggle {
                display: flex;
                position: fixed;
                top: 14px;
                left: 14px;
                z-index: 60;
                width: 44px;
                height: 44px;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                color: white;
                background: var(--pcg-blue);
                box-shadow: 0 5px 18px rgba(15, 23, 42, .2);
            }

            .page-content>.bg-white:first-child {
                padding: 18px !important;
            }
        }
    </style>

</head>



<body class="bg-gray-100 min-h-screen">
    <button id="mobileSidebarToggle" type="button" aria-label="Open navigation menu" aria-expanded="false">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round">
            <path d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>



    <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-slate-950/60 md:hidden" aria-hidden="true"></div>



    <!-- Sidebar -->

    <aside id="pcgSidebar" class="sidebar text-white">



        <div class="sidebar-brand px-5 py-6">

            <div class="flex items-center gap-3">

                <div class="brand-mark">PCG</div>

                <div class="sidebar-label min-w-0">

                    <h1 class="whitespace-nowrap text-sm font-black tracking-[.13em]">Pong Pagong</h1>

                    <p class="mt-1 whitespace-nowrap text-xs text-blue-200">Knowledge Assistant</p>

                    <p class="mt-2 whitespace-nowrap text-[9px] font-bold uppercase tracking-[.13em] text-amber-300">

                        Personnel Support Workspace

                    </p>

                </div>

            </div>

        </div>



        <div class="gold-line"></div>



        <div class="sidebar-workspace flex items-center justify-between px-5 pb-2 pt-5">

            <p class="sidebar-label section-label">Workspace</p>



            <button id="sidebarToggle" type="button"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-blue-100 transition hover:bg-white/10 hover:text-white"
                aria-label="Collapse sidebar" aria-expanded="true" title="Collapse sidebar">

                <svg class="sidebar-toggle-icon h-4 w-4 transition-transform duration-300" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">

                    <path d="m15 18-6-6 6-6" />

                </svg>

            </button>

        </div>



        <nav class="sidebar-nav flex-1 space-y-1 overflow-x-hidden overflow-y-auto px-3 pb-5">
            <a href="{{ url('/pdf-documents') }}" class="nav-link">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6" />
                        <path d="M8 13h8" />
                        <path d="M8 17h8" />
                        <path d="M8 9h2" />
                    </svg>
                </span>

                <span class="nav-text">PDF Documents</span>
            </a>
            <a href="{{ route('ai-chat.index') }}" class="nav-link active" aria-current="page" title="Pong Pagong">

                <span class="nav-icon">✳</span>

                <span class="sidebar-label nav-text">Pong Pagong</span>

                <span
                    class="nav-badge ml-auto rounded-md bg-white/10 px-2 py-1 text-[9px] font-bold text-amber-200">AI</span>

            </a>

            <a href="{{ route('biometric.login') }}" class="nav-link" title="Biometric Login">

                <span class="nav-icon">⌘</span>

                <span class="sidebar-label nav-text">Biometric Login</span>

            </a>



            <a href="{{ route('face.recognition.index') }}" class="nav-link active" aria-current="page"
                title="Face Recognition">

                <span class="nav-icon">◎</span>

                <span class="sidebar-label nav-text">Face Recognition</span>

            </a>



            <a href="{{ route('people.index') }}" class="nav-link" title="Personnel Records">

                <span class="nav-icon">♙</span>

                <span class="sidebar-label nav-text">Personnel Records</span>

            </a>



            <div class="sidebar-label px-3 pb-3 pt-9">

                <p class="section-label">Environment</p>

            </div>



            <div class="service-card mx-1 rounded-2xl p-4">

                <div class="flex items-center gap-3">

                    <span class="service-dot"></span>

                    <div class="sidebar-label min-w-0">

                        <p class="whitespace-nowrap text-xs font-bold text-white">AI Workspace</p>

                        <p class="mt-1 whitespace-nowrap text-[10px] text-blue-200">Configured service</p>

                    </div>

                </div>



                <p class="sidebar-label mt-4 text-[11px] leading-5 text-blue-100/70">

                    Pong Pagong uses your configured AI service and searchable knowledge base.

                </p>



                <div class="sidebar-label mt-4 border-t border-white/10 pt-3">

                    <p class="text-[10px] font-bold uppercase tracking-wider text-amber-200">Security reminder

                    </p>

                    <p class="mt-2 text-[11px] leading-5 text-blue-100/70">

                        Never submit passwords, classified material, or unnecessary personal and biometric data.

                    </p>

                </div>

            </div>

        </nav>



        <div class="sidebar-footer border-t border-white/10 px-5 py-4">

            <div class="flex items-center gap-2">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-xs font-bold text-amber-200">P</span>

                <div class="sidebar-label min-w-0">

                    <p class="whitespace-nowrap text-[11px] font-bold text-white">Pong Pagong</p>

                    <p class="mt-1 whitespace-nowrap text-[9px] text-blue-200/60">Knowledge &amp; technical

                        support

                    </p>

                </div>

            </div>

        </div>

    </aside>

    <div class="page-content">



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



                                <svg xmlns="http://www\.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">



                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7h3l2-3h8l2 3h3a1 1 0 011 1v11a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z" />



                                    <circle cx="12" cy="13" r="3" />



                                </svg>



                                Add Photo



                            </a>





                            <!-- ADD VOICE -->



                            <a href="{{ route('people.voices.create', $person) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition">



                                <svg xmlns="http://www\.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">



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



                            <svg xmlns="http://www\.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none"
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



                            <svg xmlns="http://www\.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
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



                                        <svg xmlns="http://www\.w3.org/2000/svg" class="w-5 h-5 text-purple-600"
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



                            <svg xmlns="http://www\.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none"
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




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const sidebar = document.getElementById('pcgSidebar');
            const collapseButton = document.getElementById('sidebarToggle');
            const mobileButton = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (collapseButton) {
                collapseButton.addEventListener('click', function() {
                    if (window.matchMedia('(max-width: 767px)').matches) {
                        body.classList.remove('mobile-sidebar-open');
                        if (mobileButton) mobileButton.setAttribute('aria-expanded', 'false');
                        return;
                    }
                    const collapsed = body.classList.toggle('sidebar-collapsed');
                    collapseButton.setAttribute('aria-expanded', String(!collapsed));
                    collapseButton.setAttribute('aria-label', collapsed ? 'Expand sidebar' :
                        'Collapse sidebar');
                    collapseButton.setAttribute('title', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
                });
            }

            function setMobileSidebar(open) {
                body.classList.toggle('mobile-sidebar-open', open);
                if (mobileButton) mobileButton.setAttribute('aria-expanded', String(open));
                if (overlay) overlay.setAttribute('aria-hidden', String(!open));
            }
            if (mobileButton) mobileButton.addEventListener('click', function() {
                setMobileSidebar(!body.classList.contains('mobile-sidebar-open'));
            });
            if (overlay) overlay.addEventListener('click', function() {
                setMobileSidebar(false);
            });
            document.querySelectorAll('#pcgSidebar .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.matchMedia('(max-width: 767px)').matches) setMobileSidebar(false);
                });
            });
            window.addEventListener('resize', function() {
                if (!window.matchMedia('(max-width: 767px)').matches) setMobileSidebar(false);
            });
        });
    </script>

</body>


<script>
    (function() {
        'use strict';

        if (window.bioVerifyWelcomeInitialized) {
            return;
        }

        window.bioVerifyWelcomeInitialized = true;

        let welcomeStarted = false;
        let welcomeTimer = null;

        const synth = window.speechSynthesis;

        if (!synth || !('SpeechSynthesisUtterance' in window)) {
            console.warn('Speech synthesis is not supported.');
            return;
        }

        const welcomeMessage =
            'Welcome. Welcome to Pong AI. Master Boss Sensei ' +
            'HERMES AI Ready.';

        function getPreferredVoice() {
            const voices = synth.getVoices();

            // 1. Siri voice, if the browser exposes one
            const siriVoice = voices.find(voice =>
                /siri/i.test(voice.name) &&
                /female|siri/i.test(voice.name)
            );

            if (siriVoice) return siriVoice;

            // 2. Preferred female English voices
            const preferredFemale = voices.find(voice =>
                /^en[-_]/i.test(voice.lang) &&
                /zira|samantha|aria|jenny|female/i.test(voice.name)
            );

            if (preferredFemale) return preferredFemale;

            // 3. Google UK English Female
            const googleFemale = voices.find(voice =>
                /google uk english female/i.test(voice.name)
            );

            if (googleFemale) return googleFemale;

            // 4. Any available English voice
            return voices.find(voice =>
                /^en[-_]/i.test(voice.lang)
            ) || voices[0] || null;
        }

        function speakWelcome() {
            if (welcomeStarted) return;

            const voice = getPreferredVoice();

            if (!voice && synth.getVoices().length === 0) {
                return;
            }

            welcomeStarted = true;
            window.bioVerifyWelcomeStarted = true;

            const greeting =
                new SpeechSynthesisUtterance(welcomeMessage);

            if (voice) {
                greeting.voice = voice;
                greeting.lang = voice.lang;
                console.log(
                    'Selected welcome voice:',
                    voice.name,
                    voice.lang
                );
            } else {
                greeting.lang = 'en-US';
            }

            greeting.rate = 0.92;
            greeting.pitch = 1.05;
            greeting.volume = 1;

            greeting.onstart = function() {
                console.log('BioVerify AI welcome voice started.');
            };

            greeting.onend = function() {
                console.log('BioVerify AI welcome voice completed.');
            };

            greeting.onerror = function(event) {
                console.warn('Welcome voice error:', event.error);
            };

            synth.cancel();
            synth.speak(greeting);
        }

        function scheduleWelcome() {
            if (welcomeStarted) return;

            clearTimeout(welcomeTimer);
            welcomeTimer = setTimeout(speakWelcome, 800);
        }

        if (document.readyState === 'loading') {
            document.addEventListener(
                'DOMContentLoaded',
                scheduleWelcome, {
                    once: true
                }
            );
        } else {
            scheduleWelcome();
        }

        if (typeof synth.addEventListener === 'function') {
            synth.addEventListener('voiceschanged', scheduleWelcome);
        }

        console.log(
            'Available speech voices:',
            synth.getVoices().map(voice => ({
                name: voice.name,
                language: voice.lang
            }))
        );
    })();
</script>



</html>
