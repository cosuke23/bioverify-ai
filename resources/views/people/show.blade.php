@extends('layouts.pcg')

@section('title', $person->full_name)

@section('crumbs')
    <a href="{{ route('people.index') }}">Personnel</a><span aria-hidden="true">/</span><strong>{{ $person->full_name }}</strong>
@endsection

@push('head')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">

    {{-- Responsive extension CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css">

    <style>
        /* Profile header ------------------------------------------ */
        .profile {
            position: relative;
            overflow: hidden;
        }

        .profile::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, var(--pcg-blue) 0%, var(--pcg-blue) 70%, var(--pcg-gold) 70%);
        }

        .profile-avatar {
            display: grid;
            place-items: center;
            flex: 0 0 76px;
            width: 76px;
            height: 76px;
            border-radius: 20px;
            background: var(--pcg-blue);
            color: var(--pcg-gold);
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 700;
            letter-spacing: .04em;
            box-shadow: inset 0 0 0 2px rgba(244, 197, 66, .45);
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-name {
            margin: 4px 0 0;
            font-family: var(--font-display);
            font-size: 34px;
            font-weight: 700;
            line-height: 1.05;
            overflow-wrap: anywhere;
        }

        .meta-grid {
            display: grid;
            gap: 18px 28px;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            padding: 20px 22px;
            border-top: 1px solid var(--line-soft);
            background: #fbfcfe;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }

        .meta-label {
            font-family: var(--font-display);
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .meta-value {
            margin-top: 3px;
            font-weight: 600;
            color: var(--ink);
            overflow-wrap: anywhere;
        }

        .stat-note {
            margin-top: 6px;
            font-size: 12.5px;
            color: var(--muted);
        }

        .section-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 8px;
            margin: 32px 0 14px;
        }

        .section-title {
            margin: 4px 0 0;
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 700;
        }

        .panel-icon {
            display: grid;
            place-items: center;
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--info-soft);
            color: var(--pcg-blue);
        }

        .panel-icon svg {
            width: 22px;
            height: 22px;
        }

        .panel-icon.is-gold {
            background: var(--pcg-gold-soft);
            color: var(--pcg-gold-600);
        }

        /* Face gallery -------------------------------------------- */
        .face-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        }

        .face-tile {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
        }

        .face-tile:hover,
        .face-tile:focus-within {
            border-color: var(--pcg-blue);
            box-shadow: var(--shadow);
        }

        .face-tile.is-primary {
            border-color: var(--pcg-gold);
            box-shadow: 0 0 0 2px rgba(244, 197, 66, .45);
        }

        .face-frame {
            position: relative;
            aspect-ratio: 1;
            background: var(--line-soft);
        }

        .face-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* scanner corner brackets */
        .face-frame::before,
        .face-frame::after {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            border-color: rgba(255, 255, 255, .85);
            border-style: solid;
            pointer-events: none;
        }

        .face-frame::before {
            top: 8px;
            left: 8px;
            border-width: 2px 0 0 2px;
        }

        .face-frame::after {
            right: 8px;
            bottom: 8px;
            border-width: 0 2px 2px 0;
        }

        .face-flag {
            position: absolute;
            left: 8px;
            bottom: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            height: 22px;
            padding: 0 8px;
            border-radius: 999px;
            background: var(--pcg-gold);
            color: var(--pcg-blue-900);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .face-flag svg {
            width: 11px;
            height: 11px;
        }

        .face-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            padding: 8px 8px 8px 10px;
            font-size: 12px;
            color: var(--muted);
        }

        .face-foot form,
        .voice-row form {
            margin: 0;
        }

        .icon-btn.is-danger:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: var(--danger-soft);
        }

        .icon-btn.is-sm {
            width: 30px;
            height: 30px;
        }

        .icon-btn.is-sm svg {
            width: 15px;
            height: 15px;
        }

        /* Voice samples ------------------------------------------- */
        .voice-row {
            display: grid;
            gap: 12px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            transition: border-color .15s;
        }

        .voice-row:hover,
        .voice-row:focus-within {
            border-color: var(--pcg-blue);
        }

        .voice-row.is-primary {
            border-color: var(--pcg-gold);
        }

        .voice-row audio {
            width: 100%;
            height: 40px;
        }

        .wave {
            display: flex;
            align-items: center;
            gap: 2px;
            height: 22px;
        }

        .wave i {
            display: block;
            width: 3px;
            border-radius: 2px;
            background: var(--pcg-blue);
            opacity: .55;
        }

        .empty-state {
            display: grid;
            justify-items: center;
            gap: 8px;
            padding: 36px 16px;
            border: 1px dashed var(--line);
            border-radius: var(--radius);
            background: var(--canvas);
            text-align: center;
        }

        /* Recognition log (DataTables) ---------------------------- */
        .log-wrap {
            padding: 6px 22px 18px;
        }

        .log-wrap div.dt-container .dt-layout-row {
            margin: 12px 0;
        }

        .log-wrap div.dt-container .dt-search input,
        .log-wrap div.dt-container .dt-length select {
            min-height: 38px;
            padding: 6px 12px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--surface);
            color: var(--ink);
            font: 400 14px/1.4 var(--font-body);
        }

        .log-wrap div.dt-container .dt-search input {
            min-width: 240px;
        }

        .log-wrap div.dt-container .dt-search input:focus,
        .log-wrap div.dt-container .dt-length select:focus {
            outline: none;
            border-color: var(--pcg-blue);
            box-shadow: 0 0 0 4px rgba(0, 51, 160, .12);
        }

        .log-wrap div.dt-container .dt-info,
        .log-wrap div.dt-container .dt-length label {
            font-size: 13px;
            color: var(--muted);
        }

        .log-wrap div.dt-container .dt-paging .dt-paging-button {
            min-width: 34px;
            padding: 6px 10px;
            border: 1px solid transparent !important;
            border-radius: var(--radius-sm);
            background: transparent !important;
            color: var(--ink-2) !important;
            font-size: 13px;
        }

        .log-wrap div.dt-container .dt-paging .dt-paging-button:hover {
            border-color: var(--line) !important;
            color: var(--pcg-blue) !important;
        }

        .log-wrap div.dt-container .dt-paging .dt-paging-button.current,
        .log-wrap div.dt-container .dt-paging .dt-paging-button.current:hover {
            border-color: var(--pcg-blue) !important;
            background: var(--pcg-blue) !important;
            color: #fff !important;
        }

        .log-wrap div.dt-container .dt-paging .dt-paging-button.disabled {
            opacity: .4;
        }

        .log-wrap table.dataTable.table > thead > tr > th,
        .log-wrap table.dataTable.table > tbody > tr > td {
            padding: 12px 14px;
        }

        .log-wrap table.dataTable.table > thead > tr > th {
            border-bottom: 1px solid var(--line);
        }

        .log-wrap table.dataTable > tbody > tr > td.dtr-control:before {
            background-color: var(--pcg-blue);
        }

        @media (max-width: 639px) {
            .profile-name {
                font-size: 28px;
            }

            .log-wrap {
                padding: 4px 14px 14px;
            }

            .log-wrap div.dt-container .dt-search input {
                min-width: 0;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $initials = strtoupper(mb_substr($person->first_name ?? '', 0, 1) . mb_substr($person->last_name ?? '', 0, 1));
        $isActive = $person->status === 'Active';
        $primaryFace = $person->faces->firstWhere('is_primary', true);
        $faceCount = $person->faces->count();
        $voiceCount = $person->voices->count();
        $logCount = $person->recognitionLogs->count();
    @endphp

    {{-- Profile header --}}
    <section class="card profile rise" aria-labelledby="profile-name">
        <div class="card-pad flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="profile-avatar" aria-hidden="true">
                    @if ($primaryFace)
                        <img src="{{ route('biometrics.face', $primaryFace) }}" alt="">
                    @else
                        {{ $initials ?: '?' }}
                    @endif
                </div>

                <div class="min-w-0">
                    <div class="eyebrow">Personnel profile</div>
                    <h1 class="profile-name" id="profile-name">{{ $person->full_name }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="tag" title="Person ID">{{ $person->person_id }}</span>
                        <span class="badge {{ $isActive ? 'badge-ok' : 'badge-danger' }}">
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('people.index') }}" class="btn btn-ghost">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Roster
                </a>
                <a href="{{ route('people.edit', $person) }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" />
                    </svg>
                    Edit profile
                </a>
            </div>
        </div>

        <dl class="meta-grid">
            <div>
                <dt class="meta-label">Person ID</dt>
                <dd class="meta-value mono">{{ $person->person_id }}</dd>
            </div>
            <div>
                <dt class="meta-label">Full name</dt>
                <dd class="meta-value">{{ $person->full_name }}</dd>
            </div>
            <div>
                <dt class="meta-label">Email address</dt>
                <dd class="meta-value">
                    @if ($person->email)
                        <a href="mailto:{{ $person->email }}" class="hover:underline">{{ $person->email }}</a>
                    @else
                        <span class="muted">N/A</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="meta-label">Mobile number</dt>
                <dd class="meta-value mono">{{ $person->mobile_number ?: 'N/A' }}</dd>
            </div>
            <div>
                <dt class="meta-label">Registration date</dt>
                <dd class="meta-value">{{ $person->created_at?->format('F d, Y h:i A') ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="meta-label">Account status</dt>
                <dd class="meta-value">{{ $person->status ?: 'N/A' }}</dd>
            </div>
        </dl>
    </section>

    {{-- Enrolment readiness --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-5" aria-label="Enrolment summary">
        <div class="stat {{ $faceCount ? 'is-ok' : 'is-danger' }} rise rise-2">
            <div class="stat-label">Face samples</div>
            <div class="stat-value">{{ $faceCount }}</div>
            <div class="stat-note">{{ $faceCount ? ($primaryFace ? 'Primary sample set' : 'No primary sample marked') : 'Not enrolled for face verification' }}</div>
        </div>
        <div class="stat {{ $voiceCount ? 'is-ok' : 'is-gold' }} rise rise-3">
            <div class="stat-label">Voice samples</div>
            <div class="stat-value">{{ $voiceCount }}</div>
            <div class="stat-note">{{ $voiceCount ? 'Available for voice match' : 'Not enrolled for voice verification' }}</div>
        </div>
        <div class="stat rise rise-4">
            <div class="stat-label">Recognition events</div>
            <div class="stat-value">{{ $logCount }}</div>
            <div class="stat-note">Most recent 20 shown below</div>
        </div>
    </section>

    {{-- Biometric enrolment --}}
    <div class="section-head">
        <div>
            <div class="eyebrow">Biometric enrolment</div>
            <h2 class="section-title">Registered samples</h2>
        </div>
        <p class="muted text-sm">Samples are stored privately and served only to administrators.</p>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- Face samples --}}
        <section class="card rise rise-2" aria-labelledby="faces-title">
            <div class="card-head">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="panel-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                            <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                            <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                            <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                            <circle cx="12" cy="11" r="3" />
                            <path d="M7.5 17.5a5 5 0 0 1 9 0" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="card-title" id="faces-title">Face samples</h3>
                        <p class="muted text-xs">{{ $faceCount }} {{ \Illuminate\Support\Str::plural('sample', $faceCount) }} registered</p>
                    </div>
                </div>
                <a href="{{ route('people.faces.create', $person) }}" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Add photo
                </a>
            </div>

            <div class="card-pad">
                @if ($faceCount)
                    <ul class="face-grid" role="list">
                        @foreach ($person->faces as $face)
                            <li class="face-tile {{ $face->is_primary ? 'is-primary' : '' }}">
                                <div class="face-frame">
                                    <img src="{{ route('biometrics.face', $face) }}"
                                        alt="{{ $face->is_primary ? 'Primary face' : 'Face' }} sample of {{ $person->full_name }}"
                                        loading="lazy">
                                    @if ($face->is_primary)
                                        <span class="face-flag">
                                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path
                                                    d="M10 2l2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.4-4.5 2.4.9-5L2.8 7.2l5-.7L10 2z" />
                                            </svg>
                                            Primary
                                        </span>
                                    @endif
                                </div>
                                <div class="face-foot">
                                    <span class="truncate">
                                        {{ $face->is_primary ? 'Primary sample' : 'Face sample' }}
                                    </span>
                                    <form method="POST"
                                        action="{{ route('people.faces.destroy', [$person, $face]) }}"
                                        onsubmit="return confirm('Remove this face sample? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn is-sm is-danger"
                                            title="Remove face sample"
                                            aria-label="Remove {{ $face->is_primary ? 'primary ' : '' }}face sample">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                aria-hidden="true">
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4h8v2" />
                                                <path d="M19 6l-1 14H6L5 6" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <span class="panel-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="10" r="3.5" />
                                <path d="M5.5 20a7 7 0 0 1 13 0" />
                            </svg>
                        </span>
                        <p class="font-semibold">No face samples</p>
                        <p class="muted text-sm max-w-xs">
                            Register a clear, front-facing photo so this person can be verified at the kiosk.
                        </p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Voice samples --}}
        <section class="card rise rise-3" aria-labelledby="voices-title">
            <div class="card-head">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="panel-icon is-gold" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="2" width="6" height="12" rx="3" />
                            <path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3m-4 0h8" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="card-title" id="voices-title">Voice samples</h3>
                        <p class="muted text-xs">{{ $voiceCount }} {{ \Illuminate\Support\Str::plural('sample', $voiceCount) }} registered</p>
                    </div>
                </div>
                <a href="{{ route('people.voices.create', $person) }}" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Add voice
                </a>
            </div>

            <div class="card-pad">
                @if ($voiceCount)
                    <ul class="grid gap-3" role="list">
                        @foreach ($person->voices as $voice)
                            <li class="voice-row {{ $voice->is_primary ? 'is-primary' : '' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="wave" aria-hidden="true">
                                            @foreach ([8, 16, 11, 20, 13, 7, 17, 10, 5] as $h)
                                                <i style="height: {{ $h }}px"></i>
                                            @endforeach
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-sm">
                                                Voice sample
                                                @if ($voice->is_primary)
                                                    <span class="badge badge-ok ml-1">Primary</span>
                                                @endif
                                            </p>
                                            <p class="muted text-xs mono">
                                                {{ $voice->created_at?->format('M d, Y h:i A') ?? 'Date unavailable' }}
                                            </p>
                                        </div>
                                    </div>

                                    <form method="POST"
                                        action="{{ route('people.voices.destroy', [$person, $voice]) }}"
                                        onsubmit="return confirm('Remove this voice sample? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn is-sm is-danger"
                                            title="Remove voice sample" aria-label="Remove voice sample">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                aria-hidden="true">
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4h8v2" />
                                                <path d="M19 6l-1 14H6L5 6" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <audio controls preload="none"
                                    aria-label="Voice sample recorded {{ $voice->created_at?->format('M d, Y') ?? '' }}">
                                    <source src="{{ route('biometrics.voice', $voice) }}">
                                    Your browser does not support audio playback.
                                </audio>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <span class="panel-icon is-gold" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="2" width="6" height="12" rx="3" />
                                <path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3m-4 0h8" />
                            </svg>
                        </span>
                        <p class="font-semibold">No voice samples</p>
                        <p class="muted text-sm max-w-xs">
                            No voice recordings have been registered for this person.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- Recognition log --}}
    <div class="section-head">
        <div>
            <div class="eyebrow">Audit trail</div>
            <h2 class="section-title">Recognition history</h2>
        </div>
        <span class="badge badge-muted">{{ $logCount }} {{ \Illuminate\Support\Str::plural('record', $logCount) }}</span>
    </div>

    <section class="card rise rise-4" aria-label="Recognition history">
        <div class="log-wrap">
            <table id="recognitionHistoryTable" class="table display" style="width:100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Face Score</th>
                        <th>Voice Score</th>
                        <th>Combined Score</th>
                        <th>Result</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($person->recognitionLogs as $log)
                        <tr>
                            <td class="whitespace-nowrap" data-order="{{ $log->created_at?->timestamp ?? 0 }}">
                                {{ $log->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
                            </td>

                            <td class="whitespace-nowrap">
                                <span class="tag">{{ $log->recognition_type }}</span>
                            </td>

                            <td class="whitespace-nowrap mono">{{ $log->face_score ?? '-' }}</td>

                            <td class="whitespace-nowrap mono">{{ $log->voice_score ?? '-' }}</td>

                            <td class="whitespace-nowrap mono font-semibold">{{ $log->combined_score ?? '-' }}</td>

                            <td class="whitespace-nowrap">
                                @if ($log->result === 'Matched')
                                    <span class="badge badge-ok">Matched</span>
                                @elseif ($log->result === 'Review Required')
                                    <span class="badge badge-warn">Review Required</span>
                                @else
                                    <span class="badge badge-danger">Not Matched</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-[22px] py-3 text-xs muted" style="border-top:1px solid var(--line-soft)">
            Recognition scores and results are displayed as recorded by the recognition system.
        </div>
    </section>
@endsection

@push('scripts')
    {{-- DataTables scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($.fn.dataTable.isDataTable('#recognitionHistoryTable')) {
                return;
            }

            $('#recognitionHistoryTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [
                    [0, 'desc']
                ],
                searching: true,
                ordering: true,
                paging: true,
                info: true,

                language: {
                    search: '',
                    searchPlaceholder: 'Search recognition history...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ records',
                    infoEmpty: 'No recognition records available',
                    zeroRecords: 'No matching recognition records found',
                    emptyTable: 'No recognition activity available',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },

                columnDefs: [{
                    targets: [1, 5],
                    orderable: true
                }]
            });

            // DataTables renders the search box without a visible label.
            $('#recognitionHistoryTable').closest('.dt-container').find('.dt-search input')
                .attr('aria-label', 'Search recognition history');
        });
    </script>
@endpush
