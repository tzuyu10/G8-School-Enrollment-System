@extends('common.main')

@section('title', 'Enroll | PUP Enrollment Portal')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">

    <h1 class="h3 fw-bold mb-1">Enrollment Form</h1>
    <p class="text-muted mb-4">Fill out your enrollment details for this semester.</p>

    @if ($errors->any())
        <div class="alert alert-danger">Please review the form and try again.</div>
    @endif

    <div class="card shadow-sm">
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
                    <label class="form-label fw-semibold" for="college_filter">College</label>
                    <select id="college_filter" class="form-select">
                        <option value="">All colleges</option>
                        @foreach ($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->code }} - {{ $college->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Use this to filter the program list.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="program_id">Program <span class="text-danger">*</span></label>
                    <select id="program_id" name="program_id"
                        class="form-select @error('program_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Select program</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" data-college-id="{{ $program->college_id }}" @selected(old('program_id') === $program->id)>
                                {{ $program->code }}@if($program->major) — {{ $program->major }}@endif
                                ({{ $program->college->code ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
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

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="section_filter">Available Section Schedule</label>
                    <select id="section_filter" class="form-select">
                        <option value="">Select program, year level, and semester first</option>
                    </select>
                    <div class="form-text">Subjects are selected individually, but schedules stay fixed to one section.</div>
                </div>

                @error('subject_offering_ids')
                    <div class="alert alert-danger small">{{ $message }}</div>
                @enderror

                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 52px;">Add</th>
                                <th>Code</th>
                                <th>Subject</th>
                                <th>Units</th>
                                <th>Schedule</th>
                                <th>Room</th>
                                <th>Faculty</th>
                                <th>Prerequisite</th>
                            </tr>
                        </thead>
                        <tbody id="offerings_body">
                            <tr>
                                <td colspan="8" class="text-muted text-center py-3">No schedule selected.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">Submit Application</button>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const offerings = {!! $offeringsJson !!};
        const oldSelected = @json(old('subject_offering_ids', []));
        const controls = {
            semester: document.getElementById('semester_id'),
            college: document.getElementById('college_filter'),
            program: document.getElementById('program_id'),
            year: document.getElementById('year_level_id'),
            section: document.getElementById('section_filter'),
            body: document.getElementById('offerings_body'),
        };

        function refreshPrograms() {
            const selectedCollegeId = controls.college.value;
            let selectedProgramIsVisible = false;

            [...controls.program.options].forEach(option => {
                if (!option.value) {
                    return;
                }

                const visible = !selectedCollegeId || option.dataset.collegeId === selectedCollegeId;
                option.hidden = !visible;
                option.disabled = !visible;

                if (visible && option.value === controls.program.value) {
                    selectedProgramIsVisible = true;
                }
            });

            if (!selectedProgramIsVisible) {
                controls.program.value = '';
            }

            refreshSections();
        }

        function matchingOfferings() {
            return offerings.filter(item =>
                item.semester_id === controls.semester.value &&
                item.program_id === controls.program.value &&
                item.year_level_id === controls.year.value
            );
        }

        function refreshSections() {
            const rows = matchingOfferings();
            const sections = [...new Map(rows.map(item => [item.section_id, item.section])).entries()];
            const current = controls.section.value;

            controls.section.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = sections.length ? 'Select section schedule' : 'No available schedules';
            controls.section.appendChild(placeholder);

            sections.forEach(([id, name]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = name;
                controls.section.appendChild(option);
            });

            if (sections.some(([id]) => id === current)) {
                controls.section.value = current;
            } else if (sections.length === 1) {
                controls.section.value = sections[0][0];
            }

            refreshOfferings();
        }

        function refreshOfferings() {
            const rows = matchingOfferings().filter(item => item.section_id === controls.section.value);
            controls.body.innerHTML = '';

            if (!rows.length) {
                controls.body.innerHTML = '<tr><td colspan="8" class="text-muted text-center py-3">No schedule selected.</td></tr>';
                return;
            }

            rows.forEach(item => {
                const tr = document.createElement('tr');
                const disabled = item.eligible ? '' : 'disabled';
                const title = item.eligible ? '' : `Missing prerequisite: ${item.missing}`;
                const checked = oldSelected.includes(item.id) || item.eligible ? 'checked' : '';
                tr.innerHTML = `
                    <td class="text-center">
                        <input class="form-check-input" type="checkbox" name="subject_offering_ids[]" value="${item.id}" ${checked} ${disabled} title="${title}">
                    </td>
                    <td class="fw-semibold">${item.code}</td>
                    <td>${item.title}</td>
                    <td class="text-center">${item.units}</td>
                    <td>${item.schedule}</td>
                    <td>${item.room}</td>
                    <td>${item.faculty}</td>
                    <td>${item.eligible ? item.prerequisites : `<span class="text-danger">${item.prerequisites}<br><small>Missing: ${item.missing}</small></span>`}</td>
                `;
                controls.body.appendChild(tr);
            });
        }

        [controls.semester, controls.program, controls.year].forEach(control => {
            control.addEventListener('change', refreshSections);
        });
        controls.college.addEventListener('change', refreshPrograms);
        controls.section.addEventListener('change', refreshOfferings);
        refreshPrograms();
        refreshSections();
    </script>

@endsection
