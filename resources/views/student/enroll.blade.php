@extends('common.main')

@section('title', 'Enroll | PUP Enrollment Portal')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">

    <h1 class="h3 fw-bold mb-1">Enrollment Form</h1>
    <p class="text-muted mb-4">Fill out your enrollment details for this semester.</p>

    @if ($errors->any())
        <div class="alert alert-danger">Please review the form and try again.</div>
    @endif

    <div class="card shadow-sm" style="max-width: 600px;">
        <div class="card-body p-4">
            <form action="{{ route('enroll.submit') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Student Type</label>
                    <input type="text" class="form-control bg-light"
                        value="{{ ucfirst($user->studentProfile?->student_type ?? 'N/A') }}"
                        readonly disabled>
                    <div class="form-text">Inherited from your registration.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="semester_id">Semester <span class="text-danger">*</span></label>
                    <select id="semester_id" name="semester_id"
                        class="form-select @error('semester_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Select semester</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}" @selected(old('semester_id', $activeSemester?->id) === $sem->id)>
                                {{ $sem->label }} — {{ $sem->academicYear->label ?? '' }}
                                @if ($sem->is_active) (Active) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="program_id">Program <span class="text-danger">*</span></label>
                    <select id="program_id" name="program_id"
                        class="form-select @error('program_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Select program</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" @selected(old('program_id') === $program->id)>
                                {{ $program->code }}@if($program->major) — {{ $program->major }}@endif
                                ({{ $program->college->code ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="year_level_id">Year Level <span class="text-danger">*</span></label>
                    <select id="year_level_id" name="year_level_id"
                        class="form-select @error('year_level_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Select year level</option>
                        @foreach ($yearLevels as $yl)
                            <option value="{{ $yl->id }}" @selected(old('year_level_id') === $yl->id)>{{ $yl->label }}</option>
                        @endforeach
                    </select>
                    @error('year_level_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">Submit Application</button>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection