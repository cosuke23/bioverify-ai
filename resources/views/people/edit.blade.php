@extends('layouts.pcg')

@section('title', 'Edit ' . $person->full_name)

@section('crumbs')
    <a href="{{ route('people.index') }}">Personnel</a><span aria-hidden="true">/</span>
    <a href="{{ route('people.show', $person) }}" class="hidden sm:inline">{{ $person->full_name }}</a><span
        aria-hidden="true" class="hidden sm:inline">/</span>
    <strong>Edit</strong>
@endsection

@push('head')
    <style>
        .form-section {
            display: grid;
            gap: 20px;
            padding: 24px 22px;
            border-bottom: 1px solid var(--line-soft);
        }

        @media (min-width: 900px) {
            .form-section {
                grid-template-columns: 240px 1fr;
                gap: 32px;
            }
        }

        .form-section-title {
            margin: 4px 0 0;
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
        }

        .form-section-note {
            margin: 6px 0 0;
            font-size: 13.5px;
            color: var(--muted);
        }

        .req {
            color: var(--danger);
        }

        .hint {
            font-size: 12.5px;
            color: var(--muted);
        }

        .action-bar {
            position: sticky;
            bottom: 0;
            z-index: 5;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 22px;
            border-top: 1px solid var(--line);
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(8px);
        }
    </style>
@endpush

@section('content')
    <header class="page-head rise">
        <div>
            <div class="eyebrow">Personnel Records</div>
            <h1 class="page-title">Edit personnel record</h1>
            <p class="page-lede">
                Updating <strong class="text-slate-800">{{ $person->full_name }}</strong>
                <span class="tag ml-1">{{ $person->person_id }}</span>
            </p>
        </div>

        <a href="{{ route('people.show', $person) }}" class="btn btn-ghost">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <path d="M15 18l-6-6 6-6" />
            </svg>
            Back to profile
        </a>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger mb-5 rise" role="alert">
            <div>
                <p class="font-semibold">Please correct the following:</p>
                <ul class="list-disc list-inside text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('people.update', $person) }}" class="card rise rise-2">
        @csrf
        @method('PUT')

        {{-- Identity --}}
        <section class="form-section" aria-labelledby="sec-identity">
            <div>
                <div class="eyebrow">Step 1</div>
                <h2 class="form-section-title" id="sec-identity">Identity</h2>
                <p class="form-section-note">Official name and the personnel identifier used across BioVerify.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="field md:col-span-2">
                    <label for="person_id" class="label">Person ID <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" id="person_id" name="person_id"
                        value="{{ old('person_id', $person->person_id) }}" required maxlength="50" autocomplete="off"
                        class="input mono @error('person_id') is-invalid @enderror"
                        @error('person_id') aria-invalid="true" aria-describedby="person_id-error" @enderror>
                    @error('person_id')
                        <p class="field-error" id="person_id-error">{{ $message }}</p>
                    @else
                        <p class="hint">Must be unique. Changing it affects how this person appears in recognition
                            results.</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="first_name" class="label">First name <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                        value="{{ old('first_name', $person->first_name) }}" required maxlength="100"
                        autocomplete="given-name" class="input @error('first_name') is-invalid @enderror"
                        @error('first_name') aria-invalid="true" aria-describedby="first_name-error" @enderror>
                    @error('first_name')
                        <p class="field-error" id="first_name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="middle_name" class="label">Middle name</label>
                    <input type="text" id="middle_name" name="middle_name"
                        value="{{ old('middle_name', $person->middle_name) }}" maxlength="100"
                        autocomplete="additional-name" class="input @error('middle_name') is-invalid @enderror"
                        @error('middle_name') aria-invalid="true" aria-describedby="middle_name-error" @enderror>
                    @error('middle_name')
                        <p class="field-error" id="middle_name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="last_name" class="label">Last name <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                        value="{{ old('last_name', $person->last_name) }}" required maxlength="100"
                        autocomplete="family-name" class="input @error('last_name') is-invalid @enderror"
                        @error('last_name') aria-invalid="true" aria-describedby="last_name-error" @enderror>
                    @error('last_name')
                        <p class="field-error" id="last_name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="suffix" class="label">Suffix</label>
                    <input type="text" id="suffix" name="suffix" value="{{ old('suffix', $person->suffix) }}"
                        maxlength="20" placeholder="Jr., Sr., III" autocomplete="honorific-suffix"
                        class="input @error('suffix') is-invalid @enderror"
                        @error('suffix') aria-invalid="true" aria-describedby="suffix-error" @enderror>
                    @error('suffix')
                        <p class="field-error" id="suffix-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Contact --}}
        <section class="form-section" aria-labelledby="sec-contact">
            <div>
                <div class="eyebrow">Step 2</div>
                <h2 class="form-section-title" id="sec-contact">Contact</h2>
                <p class="form-section-note">Optional. Used by administrators to reach the person.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="field">
                    <label for="email" class="label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $person->email) }}"
                        maxlength="255" autocomplete="email" class="input @error('email') is-invalid @enderror"
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')
                        <p class="field-error" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="mobile_number" class="label">Mobile number</label>
                    <input type="text" id="mobile_number" name="mobile_number"
                        value="{{ old('mobile_number', $person->mobile_number) }}" maxlength="30" inputmode="tel"
                        autocomplete="tel" class="input mono @error('mobile_number') is-invalid @enderror"
                        @error('mobile_number') aria-invalid="true" aria-describedby="mobile_number-error" @enderror>
                    @error('mobile_number')
                        <p class="field-error" id="mobile_number-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Status --}}
        <section class="form-section" aria-labelledby="sec-status">
            <div>
                <div class="eyebrow">Step 3</div>
                <h2 class="form-section-title" id="sec-status">Status</h2>
                <p class="form-section-note">Only active personnel can pass biometric verification.</p>
            </div>

            <div class="field max-w-sm">
                <label for="status" class="label">Record status <span class="req" aria-hidden="true">*</span></label>
                <select id="status" name="status" required class="select @error('status') is-invalid @enderror"
                    @error('status') aria-invalid="true" aria-describedby="status-error" @enderror>
                    <option value="Active" {{ old('status', $person->status) === 'Active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="Inactive" {{ old('status', $person->status) === 'Inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('status')
                    <p class="field-error" id="status-error">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <div class="action-bar">
            <p class="hint"><span class="req" aria-hidden="true">*</span> Required field</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('people.show', $person) }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
                    Save changes
                </button>
            </div>
        </div>
    </form>
@endsection
