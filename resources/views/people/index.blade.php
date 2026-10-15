@extends('layouts.pcg')

@section('title', 'Personnel Records')

@section('crumbs')
    <span>Operations</span><span aria-hidden="true">/</span><strong>Personnel Records</strong>
@endsection

@push('head')
    <style>
        .roster-avatar {
            display: grid;
            place-items: center;
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--pcg-blue);
            color: var(--pcg-gold);
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .roster-avatar.is-inactive {
            background: var(--line-soft);
            color: var(--muted);
        }

        .roster-name {
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
        }

        .roster-name:hover {
            color: var(--pcg-blue);
        }

        .roster-tools {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .roster-search {
            position: relative;
            flex: 1 1 220px;
            min-width: 0;
        }

        .roster-search svg {
            position: absolute;
            left: 12px;
            top: 50%;
            width: 18px;
            height: 18px;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
        }

        .roster-search .input {
            min-height: 40px;
            padding-left: 38px;
        }

        .roster-tools .select {
            width: auto;
            min-height: 40px;
            padding-right: 32px;
        }

        .stat-note {
            margin-top: 6px;
            font-size: 12.5px;
            color: var(--muted);
        }

        .empty-state {
            display: grid;
            justify-items: center;
            gap: 10px;
            padding: 56px 24px;
            text-align: center;
        }

        .empty-icon {
            display: grid;
            place-items: center;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--info-soft);
            color: var(--pcg-blue);
        }

        .empty-icon svg {
            width: 28px;
            height: 28px;
        }

        .row-actions {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
        }

        .row-actions form {
            margin: 0;
        }

        .row-actions .icon-btn {
            width: 34px;
            height: 34px;
        }

        .row-actions .icon-btn.is-danger:hover {
            border-color: var(--danger);
            color: var(--danger);
        }

        .roster-foot {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 22px;
            border-top: 1px solid var(--line-soft);
        }

        .roster-foot nav {
            flex: 1 1 auto;
        }
    </style>
@endpush

@section('content')
    @php
        $pageItems = collect($people->items());
        $pageActive = $pageItems->where('status', 'Active')->count();
        $pageInactive = $pageItems->count() - $pageActive;
    @endphp

    <header class="page-head rise">
        <div>
            <div class="eyebrow">Operations</div>
            <h1 class="page-title">Personnel Records</h1>
            <p class="page-lede">
                Roster of Coast Guard personnel registered for biometric verification. Open a record to manage
                face and voice enrolment or review recognition activity.
            </p>
        </div>

        <a href="{{ route('people.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M19 8v6M22 11h-6" />
            </svg>
            Enrol personnel
        </a>
    </header>

    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-6" aria-label="Roster summary">
        <div class="stat rise rise-2">
            <div class="stat-label">Total on record</div>
            <div class="stat-value">{{ number_format($people->total()) }}</div>
            <div class="stat-note">All registered personnel</div>
        </div>

        <div class="stat is-ok rise rise-2">
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ $pageActive }}</div>
            <div class="stat-note">On this page</div>
        </div>

        <div class="stat is-danger rise rise-3">
            <div class="stat-label">Inactive</div>
            <div class="stat-value">{{ $pageInactive }}</div>
            <div class="stat-note">On this page</div>
        </div>

        <div class="stat is-gold rise rise-3">
            <div class="stat-label">Page</div>
            <div class="stat-value">
                {{ $people->currentPage() }}<span class="muted" style="font-size:22px"> / {{ max($people->lastPage(), 1) }}</span>
            </div>
            <div class="stat-note">{{ $people->perPage() }} records per page</div>
        </div>
    </section>

    <section class="card rise rise-4" aria-labelledby="roster-title">
        <div class="card-head flex-wrap">
            <div>
                <h2 class="card-title" id="roster-title">Roster</h2>
                <p class="muted text-sm">
                    @if ($people->total())
                        Showing {{ $people->firstItem() }}–{{ $people->lastItem() }} of {{ $people->total() }}
                    @else
                        No personnel registered yet
                    @endif
                </p>
            </div>

            @if ($people->count())
                <div class="roster-tools w-full sm:w-auto" role="search">
                    <div class="roster-search">
                        <label for="roster-filter" class="sr-only">Filter personnel on this page</label>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.5-3.5" />
                        </svg>
                        <input type="search" id="roster-filter" class="input" placeholder="Name, ID, email or mobile"
                            autocomplete="off">
                    </div>

                    <label for="roster-status" class="sr-only">Filter by status</label>
                    <select id="roster-status" class="select">
                        <option value="">All statuses</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            @endif
        </div>

        @if ($people->count())
            <div class="table-wrap">
                <table class="table" id="roster-table">
                    <thead>
                        <tr>
                            <th scope="col">Personnel</th>
                            <th scope="col">PCG ID</th>
                            <th scope="col" class="hidden md:table-cell">Mobile</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="hidden lg:table-cell">Registered</th>
                            <th scope="col" class="text-right"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($people as $person)
                            @php
                                $initials = strtoupper(
                                    mb_substr($person->first_name ?? '', 0, 1) . mb_substr($person->last_name ?? '', 0, 1),
                                );
                                $isActive = $person->status === 'Active';
                            @endphp
                            <tr data-roster-row data-status="{{ $person->status }}"
                                data-search="{{ \Illuminate\Support\Str::lower(implode(' ', [$person->full_name, $person->person_id, $person->email, $person->mobile_number])) }}">
                                <td>
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="roster-avatar {{ $isActive ? '' : 'is-inactive' }}"
                                            aria-hidden="true">{{ $initials ?: '?' }}</span>
                                        <div class="min-w-0">
                                            <a href="{{ route('people.show', $person) }}" class="roster-name block truncate">
                                                {{ $person->full_name }}
                                            </a>
                                            <div class="muted text-xs truncate">{{ $person->email ?: 'No email on file' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="tag">{{ $person->person_id }}</span></td>
                                <td class="hidden md:table-cell mono">{{ $person->mobile_number ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $isActive ? 'badge-ok' : 'badge-muted' }}">
                                        {{ $person->status }}
                                    </span>
                                </td>
                                <td class="hidden lg:table-cell muted text-sm whitespace-nowrap">
                                    {{ $person->created_at?->format('M d, Y') ?? '—' }}
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('people.show', $person) }}" class="icon-btn"
                                            title="View profile" aria-label="View {{ $person->full_name }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('people.edit', $person) }}" class="icon-btn"
                                            title="Edit record" aria-label="Edit {{ $person->full_name }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" />
                                            </svg>
                                        </a>
                                        @if ($isActive)
                                            <form method="POST" action="{{ route('people.destroy', $person) }}"
                                                onsubmit="return confirm('Deactivate {{ addslashes($person->full_name) }}? They will no longer pass biometric verification.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn is-danger" title="Deactivate"
                                                    aria-label="Deactivate {{ $person->full_name }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        aria-hidden="true">
                                                        <circle cx="12" cy="12" r="9" />
                                                        <path d="m5.7 5.7 12.6 12.6" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div id="roster-no-match" class="empty-state" hidden>
                    <p class="font-semibold">No personnel on this page match your filter.</p>
                    <p class="muted text-sm">Clear the filter or move to another page.</p>
                </div>
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M19 8v6M22 11h-6" />
                    </svg>
                </span>
                <h3 class="card-title">No personnel on record</h3>
                <p class="muted text-sm max-w-sm">
                    Enrol the first member of the roster, then add face and voice samples from their profile.
                </p>
                <a href="{{ route('people.create') }}" class="btn btn-primary mt-2">Enrol personnel</a>
            </div>
        @endif

        @if ($people->hasPages())
            <div class="roster-foot">
                {{ $people->links() }}
            </div>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            'use strict';

            const search = document.getElementById('roster-filter');
            const status = document.getElementById('roster-status');
            const rows = Array.from(document.querySelectorAll('[data-roster-row]'));
            const empty = document.getElementById('roster-no-match');

            if (!search || !status || !rows.length) return;

            function apply() {
                const term = search.value.trim().toLowerCase();
                const wanted = status.value;
                let visible = 0;

                rows.forEach((row) => {
                    const show = (!term || row.dataset.search.includes(term)) &&
                        (!wanted || row.dataset.status === wanted);
                    row.hidden = !show;
                    if (show) visible++;
                });

                if (empty) empty.hidden = visible !== 0;
            }

            search.addEventListener('input', apply);
            status.addEventListener('change', apply);
        })();
    </script>
@endpush
