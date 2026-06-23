@extends('common.main')

@section('title', 'Enroll | PUP Enrollment Portal')

@section('content')
    @php
        $programOptions = $programs->map(function ($program) {
            return [
                'id' => (string) $program->id,
                'college_id' => (string) $program->college_id,
                'college_code' => $program->college->code ?? '',
                'label' => trim($program->code . ($program->major ? ' - ' . $program->major : '') . ' (' . ($program->college->code ?? '') . ')'),
            ];
        })->values();

        $selectedPriorYearId = (string) old('year_level_id', $yearLevels->first()?->id);
        $hasPassingPriorGrade = collect(old('prior_grades', []))
            ->filter(fn ($grade) => $grade !== null && $grade !== '' && is_numeric($grade) && (float) $grade <= 3.00)
            ->isNotEmpty();
    @endphp

    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">

    <h1 class="h3 fw-bold mb-1">Enrollment Form</h1>
    <p class="text-muted mb-4">Fill out your enrollment details for this semester.</p>

    @if ($errors->any())
        <div class="alert alert-danger">Please review the form and try again.</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('enroll.submit') }}" method="POST" enctype="multipart/form-data">
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
                                {{ $sem->label }} - {{ $sem->academicYear->label ?? '' }}
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
                            <option value="{{ $program->id }}"
                                data-college-id="{{ $program->college_id }}"
                                data-college-code="{{ $program->college->code ?? '' }}"
                                @selected((string) old('program_id') === (string) $program->id)>
                                {{ $program->code }}@if($program->major) - {{ $program->major }}@endif
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
                            <option value="{{ $yl->id }}" @selected((string) old('year_level_id') === (string) $yl->id)>{{ $yl->label }}</option>
                        @endforeach
                    </select>
                    @error('year_level_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="section_filter">Available Section Schedule</label>
                    <select id="section_filter" name="section_id" class="form-select">
                        <option value="">Select program, year level, and semester first</option>
                    </select>
                    <div class="form-text">
                        @if ($isIrregular)
                            Irregular students may select individual available subjects across sections.
                        @else
                            Regular students use one fixed section schedule.
                        @endif
                    </div>
                </div>

                @if ($isIrregular)
                    <div class="border rounded p-3 mb-4">
                        <div class="fw-semibold mb-1">Prior Grades for Prerequisite Crediting</div>
                        <p class="text-muted small mb-3">
                            Encode only previously taken CS or computer-related subjects. Passing grades entered here are used for prerequisite checking during this enrollment.
                        </p>
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-5 col-xl-4">
                                <label class="form-label small fw-semibold" for="prior_year_filter">Subject year level</label>
                                <select id="prior_year_filter" class="form-select form-select-sm">
                                    @foreach ($yearLevels as $yl)
                                        <option value="{{ $yl->id }}" @selected($selectedPriorYearId === (string) $yl->id)>{{ $yl->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7 col-xl-8">
                                <div class="form-check">
                                    <input id="prior_show_all" class="form-check-input" type="checkbox">
                                    <label class="form-check-label small" for="prior_show_all">Show all subjects</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            @foreach ($priorSubjects as $subject)
                                @php
                                    $showPriorSubject = (string) $subject->prior_year_level_id === $selectedPriorYearId;
                                @endphp
                                <div class="col-md-6 col-xl-4 prior-subject-field {{ !$showPriorSubject ? 'd-none' : '' }}"
                                    data-year-level-id="{{ $subject->prior_year_level_id }}"
                                    data-year-level-label="{{ $subject->prior_year_level_label }}"
                                    style="{{ !$showPriorSubject ? 'display: none;' : '' }}">
                                    <label class="form-label small mb-1" for="prior_{{ $subject->id }}">
                                        {{ $subject->code }}
                                        <span class="text-muted">({{ $subject->prior_year_level_label }})</span>
                                    </label>
                                    <input id="prior_{{ $subject->id }}" name="prior_grades[{{ $subject->code }}]"
                                        type="number" min="1" max="5" step="0.25"
                                        class="form-control form-control-sm"
                                        value="{{ old('prior_grades.' . $subject->code) }}"
                                        placeholder="{{ $subject->title }}">
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-info small mt-3 mb-0">
                            If no prior CS/computing subject is credited, enroll in available lower-year subjects first and continue ladderized as prerequisites are completed.
                        </div>
                    </div>

                    <div id="tor_document_group" class="mb-4" @hidden(!$hasPassingPriorGrade) style="{{ !$hasPassingPriorGrade ? 'display: none;' : '' }}">
                        <label class="form-label fw-semibold" for="tor_document">TOR / Transfer Credentials</label>
                        <input id="tor_document" name="tor_document" type="file"
                            class="form-control @error('tor_document') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Required when you enter any passing prior grade. Accepted files: PDF, JPG, PNG up to 5 MB.</div>
                        @error('tor_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endif

                @error('subject_offering_ids')
                    <div class="alert alert-danger small">{{ $message }}</div>
                @enderror

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div class="fw-semibold">Subject Offerings</div>
                    <div id="unit_counter" class="badge text-bg-secondary px-3 py-2">Selected Units: 0 / 26</div>
                </div>

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
                <div id="selected_offerings_inputs"></div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Submit Application</button>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const offerings = {!! $offeringsJson !!};
        const yearLevels = {!! $yearLevelsJson !!};
        const curriculum = {!! $curriculumJson !!};
        const programs = @json($programOptions);
        const internallyPassedCodes = new Set(@json($passedSubjectCodes));
        const oldSelected = @json(old('subject_offering_ids', []));
        const oldProgram = @json(old('program_id'));
        const oldSection = @json(old('section_id'));
        const isIrregular = @json($isIrregular);
        const selectedOfferings = new Set(oldSelected);
        const selectedProgram = { value: oldProgram || '' };
        const controls = {
            semester: document.getElementById('semester_id'),
            college: document.getElementById('college_filter'),
            program: document.getElementById('program_id'),
            year: document.getElementById('year_level_id'),
            section: document.getElementById('section_filter'),
            body: document.getElementById('offerings_body'),
            hidden: document.getElementById('selected_offerings_inputs'),
            unitCounter: document.getElementById('unit_counter'),
            priorYear: document.getElementById('prior_year_filter'),
            priorShowAll: document.getElementById('prior_show_all'),
            torGroup: document.getElementById('tor_document_group'),
        };

        const priorInputs = [...document.querySelectorAll('input[name^="prior_grades["]')];
        const priorSubjectFields = [...document.querySelectorAll('.prior-subject-field')];

        // --- SCHEDULE ARITHMETIC PARSER ENGINE ---
        function parseSchedule(scheduleStr) {
            if (!scheduleStr || scheduleStr === 'None' || scheduleStr === 'TBA') return [];

            const daysMatch = scheduleStr.match(/^[A-Za-z]+/);
            if (!daysMatch) return [];
            const daysStr = daysMatch[0];

            const days = [];

            // 1. Check for Thursday specific multi-character tokens first
            if (daysStr.includes('Thu') || daysStr.includes('Th')) {
                days.push('Th');
            }

            // 2. Identify as Tuesday ONLY if it contains 'T' and does NOT represent Thursday tokens
            if (daysStr.includes('T') && !daysStr.includes('Thu') && !daysStr.endsWith('Th') && daysStr !== 'TTh') {
                if (daysStr.includes('Tue') || daysStr === 'T' || daysStr.includes('MTW')) {
                    days.push('T');
                }
            }

            // 3. Handle explicit combination tokens
            if (daysStr === 'TTh') {
                days.push('T', 'Th');
            }

            // 4. Fallback safe mapping for all other standard days
            if (daysStr.includes('M')) {
                days.push('M');
            }
            if (daysStr.includes('W')) {
                days.push('W');
            }
            if (daysStr.includes('F')) {
                days.push('F');
            }
            if (daysStr.includes('Sat')) {
                days.push('Sat');
            }

            const timePart = scheduleStr.replace(/^[A-Za-z]+\s+/, '');
            const parts = timePart.split('-');
            if (parts.length !== 2) return [];

            function toMinutes(timeStr) {
                timeStr = timeStr.trim();
                const ampmMatch = timeStr.match(/(AM|PM)/i);
                let isPM = false;
                if (ampmMatch) {
                    isPM = ampmMatch[0].toUpperCase() === 'PM';
                    timeStr = timeStr.replace(/(AM|PM)/i, '').trim();
                }
                const [hrsStr, minsStr] = timeStr.split(':');
                let hours = parseInt(hrsStr, 10);
                const minutes = parseInt(minsStr, 10) || 0;

                return {
                    hours,
                    minutes,
                    isPM
                };
            }

            let start = toMinutes(parts[0]);
            let end = toMinutes(parts[1]);

            if (!parts[0].toLowerCase().includes('am') && !parts[0].toLowerCase().includes('pm')) {
                start.isPM = end.isPM;
            }

            function normalize24h(t) {
                let h = t.hours;
                if (t.isPM && h < 12) h += 12;
                if (!t.isPM && h === 12) h = 0;
                return h * 60 + t.minutes;
            }

            const startMin = normalize24h(start);
            const endMin = normalize24h(end);

            return [...new Set(days)].map(day => ({
                day,
                start: startMin,
                end: endMin
            }));
        }

        function schedulesOverlap(schedA, schedB) {
            const rangesA = parseSchedule(schedA);
            const rangesB = parseSchedule(schedB);

            for (const rA of rangesA) {
                for (const rB of rangesB) {
                    if (rA.day === rB.day) {
                        if (rA.start < rB.end && rA.end > rB.start) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        // --- GRADUATE PREREQUISITE LOOPS ---
        function passedPriorCodes() {
            return new Set(priorInputs
                .filter(input => input.value !== '' && Number(input.value) <= 3)
                .map(input => input.name.match(/^prior_grades\[(.+)]$/)?.[1])
                .filter(Boolean));
        }

        function hasPassingPriorGrade() {
            return priorInputs.some(input => input.value !== '' && Number(input.value) <= 3);
        }

        function refreshTorField() {
            if (!controls.torGroup) return;
            const shouldShow = hasPassingPriorGrade();
            controls.torGroup.hidden = !shouldShow;
            controls.torGroup.style.display = shouldShow ? '' : 'none';
        }

        function subjectFor(code) {
            return curriculum.find(subject => subject.code === code);
        }

        function passedCodes() {
            return new Set([...internallyPassedCodes, ...passedPriorCodes()]);
        }

        function missingPrerequisiteChain(subjectCode, passed = passedCodes(), visited = new Set()) {
            if (visited.has(subjectCode)) return [];

            const subject = subjectFor(subjectCode);
            const prerequisites = subject?.prerequisite_codes || [];
            const missing = [];
            visited.add(subjectCode);

            prerequisites.forEach(code => {
                if (!passed.has(code)) {
                    missing.push(code);
                    return;
                }
                missing.push(...missingPrerequisiteChain(code, passed, new Set(visited)));
            });

            return [...new Set(missing)];
        }

        function firstUnearnedRoot(code, passed = passedCodes(), visited = new Set()) {
            if (visited.has(code)) return code;

            visited.add(code);
            const subject = subjectFor(code);
            const prerequisites = subject?.prerequisite_codes || [];

            if (passed.has(code)) {
                for (const prerequisite of prerequisites) {
                    const root = firstUnearnedRoot(prerequisite, passed, new Set(visited));
                    if (root && !passed.has(root)) return root;
                }
                return null;
            }

            for (const prerequisite of prerequisites) {
                const root = firstUnearnedRoot(prerequisite, passed, new Set(visited));
                if (root && !passed.has(root)) return root;
            }

            return code;
        }

        function evaluatedOffering(item) {
            const missing = missingPrerequisiteChain(item.code);
            const alreadyCredited = passedPriorCodes().has(item.code);

            return {
                ...item,
                alreadyCredited,
                currentEligible: missing.length === 0 && !alreadyCredited,
                currentMissing: missing.join(', '),
            };
        }

        function syncHiddenSelected() {
            controls.hidden.innerHTML = '';
            selectedOfferings.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'subject_offering_ids[]';
                input.value = id;
                controls.hidden.appendChild(input);
            });
            refreshUnitCounter();
        }

        function selectedUnits() {
            return [...selectedOfferings].reduce((total, id) => {
                const offering = offerings.find(item => item.id === id);
                return total + Number(offering?.units || 0);
            }, 0);
        }

        function selectedSubjectCodes(exceptId = null) {
            return new Set([...selectedOfferings]
                .filter(id => id !== exceptId)
                .map(id => offerings.find(item => item.id === id)?.code)
                .filter(Boolean));
        }

        function refreshUnitCounter() {
            const units = selectedUnits();
            controls.unitCounter.textContent = `Selected Units: ${units} / 26`;
            controls.unitCounter.classList.toggle('text-bg-danger', units > 26);
            controls.unitCounter.classList.toggle('text-bg-warning', units > 21 && units <= 26);
            controls.unitCounter.classList.toggle('text-bg-secondary', units <= 21);
        }

        function refreshPriorSubjectFilter() {
            if (!controls.priorYear || !controls.priorShowAll) return;

            const selectedYear = String(controls.priorYear.value || controls.year.value || yearLevels[0]?.id || '');
            const isChecked = controls.priorShowAll.checked;

            priorSubjectFields.forEach(field => {
                const fieldYearId = String(field.dataset.yearLevelId || '');
                const shouldShow = isChecked || (fieldYearId === selectedYear);
                
                field.style.setProperty('display', shouldShow ? '' : 'none', 'important');
                field.classList.toggle('d-none', !shouldShow);
                field.hidden = !shouldShow;
            });
        }

        function refreshPrograms() {
            const selectedCollegeId = String(controls.college.value || '');
            const hasCollegeFilter = Boolean(selectedCollegeId);
            const currentProgramId = String(controls.program.value || oldProgram || '');

            const visiblePrograms = programs.filter(program =>
                !hasCollegeFilter || String(program.college_id) === selectedCollegeId
            );
            
            const isCurrentVisible = visiblePrograms.some(p => String(p.id) === currentProgramId);

            controls.program.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.disabled = true;
            placeholder.selected = !isCurrentVisible;
            placeholder.textContent = visiblePrograms.length ? 'Select program' : 'No programs for selected college';
            controls.program.appendChild(placeholder);

            visiblePrograms.forEach(program => {
                const option = document.createElement('option');
                option.value = program.id;
                option.setAttribute('data-college-id', program.college_id);
                option.setAttribute('data-college-code', program.college_code || '');
                option.textContent = program.label;
                
                if (String(program.id) === currentProgramId) {
                    option.selected = true;
                }
                controls.program.appendChild(option);
            });

            selectedProgram.value = controls.program.value;
            refreshSections();
        }

        function matchingOfferings() {
            return offerings.filter(item =>
                String(item.semester_id) === String(controls.semester.value) &&
                String(item.program_id) === String(controls.program.value) &&
                String(item.year_level_id) === String(controls.year.value)
            );
        }

        function selectedYearSort() {
            return yearLevels.find(year => String(year.id) === String(controls.year.value))?.sort_order ?? null;
        }

        function rootBackSubjects() {
            const currentSort = selectedYearSort();
            if (!isIrregular || currentSort === null || !controls.section.value) return [];

            const currentSectionOfferings = matchingOfferings().filter(item => String(item.section_id) === String(controls.section.value));
            const requiredRootCodes = [...new Set(currentSectionOfferings.flatMap(item =>
                missingPrerequisiteChain(item.code).map(code => firstUnearnedRoot(code)).filter(Boolean)
            ))];

            return offerings.filter(item =>
                String(item.program_id) === String(controls.program.value) &&
                Number(item.year_sort) < Number(currentSort) &&
                requiredRootCodes.includes(item.code)
            );
        }

        function flexibleBackfillSubjects() {
            if (!isIrregular || !controls.section.value) return [];

            return offerings.filter(item => {
                const isBackfillType = String(item.program_id) === String(controls.program.value) &&
                    (!item.prerequisite_codes || item.prerequisite_codes.length === 0) &&
                    (item.type === 'GE' || item.code.startsWith('GEED') || item.code.startsWith('ELEC'));
                    
                if (!isBackfillType) return false;
                return selectedOfferings.has(item.id) || selectedUnits() < 21;
            });
        }

        function refreshSections() {
            const rows = matchingOfferings();
            const sections = [...new Map(rows.map(item => [item.section_id, item.section])).entries()];
            const current = controls.section.value;

            controls.section.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = sections.length
                ? (isIrregular ? 'All available sections' : 'Select section schedule')
                : 'No available schedules';
            controls.section.appendChild(placeholder);

            sections.forEach(([id, name]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = name;
                controls.section.appendChild(option);
            });

            const matchTarget = String(current || oldSection || '');
            if (sections.some(([id]) => String(id) === matchTarget)) {
                controls.section.value = matchTarget;
            } else if (!isIrregular && sections.length === 1) {
                controls.section.value = sections[0][0];
            }

            refreshOfferings();
        }

        function refreshOfferings() {
            const rows = [
                ...matchingOfferings().filter(item => String(item.section_id) === String(controls.section.value)),
                ...rootBackSubjects(),
                ...flexibleBackfillSubjects(),
            ].filter((item, index, all) => all.findIndex(row => String(row.id) === String(item.id)) === index);

            offerings.map(evaluatedOffering).forEach(item => {
                if (selectedOfferings.has(item.id) && (!item.currentEligible || item.full || item.alreadyCredited)) {
                    selectedOfferings.delete(item.id);
                }
            });

            controls.body.innerHTML = '';

            if (!rows.length) {
                controls.body.innerHTML = '<tr><td colspan="8" class="text-muted text-center py-3">No schedule selected.</td></tr>';
                return;
            }

            const currentlySelectedOfferings = offerings.filter(o => selectedOfferings.has(o.id));

            rows.map(evaluatedOffering).forEach(item => {
                const tr = document.createElement('tr');
                if (!isIrregular && item.currentEligible && !oldSelected.length) {
                    selectedOfferings.add(item.id);
                }
                const checked = selectedOfferings.has(item.id) ? 'checked' : '';
                const projectedUnits = selectedUnits() + (checked ? 0 : Number(item.units || 0));
                const exceedsCap = projectedUnits > 26;
                const duplicateSubject = !checked && selectedSubjectCodes(item.id).has(item.code);
                
                let hasScheduleConflict = false;
                if (!checked) {
                    hasScheduleConflict = currentlySelectedOfferings.some(selectedItem => 
                        schedulesOverlap(item.schedule, selectedItem.schedule)
                    );
                }

                const disabled = checked || (!item.full && item.currentEligible && !exceedsCap && !duplicateSubject && !hasScheduleConflict) ? '' : 'disabled';
                
                let statusBadge = item.prerequisites;
                let rowClass = '';
                let titleMsg = '';
                
                if (item.alreadyCredited) {
                    statusBadge = `<span class="text-info">Already credited</span>`;
                    titleMsg = `${item.code} is already credited in prior grades`;
                } else if (duplicateSubject) {
                    statusBadge = `<span class="text-warning">Already selected ${item.code}</span>`;
                    titleMsg = `Already selected ${item.code}`;
                } else if (hasScheduleConflict) {
                    statusBadge = `<span class="text-danger fw-bold">Schedule Conflict</span>`;
                    rowClass = 'table-danger';
                    titleMsg = 'This subject schedule overlaps with an already selected offering';
                } else if (exceedsCap && !checked) {
                    statusBadge = '<span class="text-warning">Exceeds 26-unit cap</span>';
                    titleMsg = 'Adding this subject exceeds the 26-unit cap';
                } else if (item.full) {
                    statusBadge = `<span class="text-danger">Full (${item.taken}/${item.capacity})</span>`;
                    titleMsg = 'Section is full';
                } else if (!item.currentEligible) {
                    statusBadge = `<span class="text-danger">${item.prerequisites}<br><small>Missing: ${item.currentMissing}</small></span>`;
                    titleMsg = `Missing prerequisite: ${item.currentMissing}`;
                }

                tr.className = rowClass;
                tr.innerHTML = `
                    <td class="text-center">
                        <input class="form-check-input offering-check" type="checkbox" value="${item.id}" ${checked} ${disabled} title="${titleMsg}">
                    </td>
                    <td class="fw-semibold">${item.code}</td>
                    <td>${item.title}</td>
                    <td class="text-center">${item.units}</td>
                    <td>${item.section}<br><small class="text-muted">${item.schedule}</small></td>
                    <td>${item.room}</td>
                    <td>${item.faculty}</td>
                    <td>${statusBadge}</td>
                `;
                controls.body.appendChild(tr);
            });

            controls.body.querySelectorAll('.offering-check').forEach(input => {
                input.addEventListener('change', () => {
                    if (input.checked) {
                        selectedOfferings.add(input.value);
                    } else {
                        selectedOfferings.delete(input.value);
                    }
                    syncHiddenSelected();
                    refreshOfferings();
                });
            });

            syncHiddenSelected();
        }

        [controls.semester, controls.program, controls.year].forEach(control => {
            control.addEventListener('change', () => {
                if (control === controls.program) {
                    selectedProgram.value = controls.program.value;
                }
                refreshSections();
            });
        });
        
        controls.year.addEventListener('change', () => {
            if (controls.priorYear && !controls.priorShowAll?.checked) {
                controls.priorYear.value = controls.year.value;
                refreshPriorSubjectFilter();
            }
        });

        controls.college.addEventListener('change', refreshPrograms);
        controls.section.addEventListener('change', () => {
            if (!isIrregular) {
                selectedOfferings.clear();
            }
            refreshOfferings();
        });

        priorInputs.forEach(input => input.addEventListener('input', () => {
            refreshOfferings();
            refreshTorField();
        }));

        controls.priorYear?.addEventListener('change', refreshPriorSubjectFilter);
        controls.priorShowAll?.addEventListener('change', refreshPriorSubjectFilter);
        
        refreshPrograms();
        refreshPriorSubjectFilter();
        refreshTorField();
        
        window.addEventListener('pageshow', refreshPrograms);
    </script>
@endsection