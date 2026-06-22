<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use App\Models\College;
use App\Models\EnrollmentApplication;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\SubjectEnrollment;
use App\Models\SubjectEnrollmentStatus;
use App\Models\SubjectOffering;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    private const MAX_SEMESTER_UNITS = 26;

    public function form(Request $request)
    {
        $user = $request->user()->load('studentProfile');

        $activeSemester = Semester::where('is_active', true)->first();

        // Redirect if already has active application this semester
        if ($activeSemester) {
            $existing = EnrollmentApplication::where('student_id', $user->id)
                ->where('semester_id', $activeSemester->id)
                ->whereHas('status', fn($q) => $q->whereIn('code', ['pending', 'approved']))
                ->first();

            if ($existing) {
                return redirect()->route('student.dashboard')
                    ->with('status', 'You already have an active enrollment application this semester.');
            }
        }

        $semesters  = Semester::with('academicYear')
            ->orderBy('is_active', 'desc')
            ->orderBy('label')
            ->get()
            ->unique(fn($semester) => $semester->academic_year_id . '|' . $semester->label)
            ->values();

        $programs   = Program::with('college')
            ->orderBy('code')
            ->get();

        $colleges = College::orderBy('code')->get();

        $yearLevels = YearLevel::query()
            ->select('id', 'label', 'sort_order')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->unique('label')
            ->values();

        $isIrregular = $this->isIrregularStudentType($user->studentProfile?->student_type);
        $passedSubjectCodes = $this->passedSubjectCodes($user->id);
        $oldCreditedCodes = array_keys($this->validPriorGrades($request->old('prior_grades', []), $user->studentProfile?->student_type));
        $viewPassedSubjectCodes = array_values(array_unique(array_merge($passedSubjectCodes, $oldCreditedCodes)));

        $preservedSectionId = $request->old('section_id');
        $preservedSection = $preservedSectionId
            ? Section::with('yearLevel')->find($preservedSectionId)
            : null;
        $resolvedRootDependencies = $preservedSection
            ? $this->resolvedRootDependenciesForSection($preservedSection, $viewPassedSubjectCodes)
            : [];

        $offerings = SubjectOffering::with(['subject', 'section.yearLevel', 'faculty'])
            ->withCount(['subjectEnrollments as taken_seats_count' => function ($query) {
                $query->whereHas('status', fn($status) => $status->whereIn('code', ['requested', 'enrolled']));
            }])
            ->whereHas('section', function ($q) use ($semesters) {
                $q->whereIn('semester_id', $semesters->pluck('id'));
            })
            ->when($preservedSectionId, function ($query) use ($isIrregular, $preservedSectionId, $preservedSection, $resolvedRootDependencies) {
                if (!$isIrregular || !$preservedSection) {
                    $query->where('section_id', $preservedSectionId);
                    return;
                }

                $query->where(function ($scope) use ($preservedSectionId, $preservedSection, $resolvedRootDependencies) {
                    $scope->where('section_id', $preservedSectionId)
                        ->orWhere(function ($backSubjects) use ($preservedSection, $resolvedRootDependencies) {
                            $backSubjects
                                ->whereHas('section', function ($section) use ($preservedSection) {
                                    $section->where('program_id', $preservedSection->program_id)
                                        ->whereHas('yearLevel', fn($year) => $year->where('sort_order', '<', $preservedSection->yearLevel->sort_order));
                                })
                                ->whereHas('subject', fn($subject) => $subject->whereIn('code', $resolvedRootDependencies));
                        })
                        ->orWhere(function ($backfill) use ($preservedSection) {
                            $backfill
                                ->whereHas('section', fn($section) => $section->where('program_id', $preservedSection->program_id))
                                ->whereHas('subject', fn($subject) => $subject
                                    ->where(function ($flexible) {
                                        $flexible->where('type', 'GE')
                                            ->orWhere('code', 'like', 'GEED%')
                                            ->orWhere('code', 'like', 'ELEC%');
                                    })
                                    ->where(function ($root) {
                                        $root->whereNull('prerequisite_codes')
                                            ->orWhere('prerequisite_codes', '');
                                    }));
                        });
                });
            })
            ->orderBy('section_id')
            ->get();

        $offeringsJson = $offerings->map(function ($offering) use ($passedSubjectCodes) {
            $prerequisites = $offering->subject->prerequisites;
            $missing = array_values(array_diff($prerequisites, $passedSubjectCodes));

            return [
                'id' => $offering->id,
                'program_id' => $offering->section->program_id,
                'year_level_id' => $offering->section->year_level_id,
                'semester_id' => $offering->section->semester_id,
                'section_id' => $offering->section_id,
                'section' => $offering->section->name,
                'year_sort' => $offering->section->yearLevel->sort_order ?? null,
                'code' => $offering->subject->code,
                'title' => $offering->subject->title,
                'units' => $offering->subject->units,
                'type' => $offering->subject->type,
                'schedule' => $offering->schedule ?: 'TBA',
                'room' => $offering->room ?: 'TBA',
                'faculty' => $offering->faculty->full_name ?? 'TBA',
                'prerequisites' => implode(', ', $prerequisites) ?: 'None',
                'prerequisite_codes' => $prerequisites,
                'eligible' => empty($missing),
                'missing' => implode(', ', $missing),
                'capacity' => $offering->section->max_capacity,
                'taken' => $offering->taken_seats_count,
                'full' => $offering->taken_seats_count >= $offering->section->max_capacity,
            ];
        })->values()->toJson();

        $curriculumJson = \App\Models\Subject::orderBy('code')
            ->get(['code', 'title', 'prerequisite_codes'])
            ->map(fn($subject) => [
                'code' => $subject->code,
                'title' => $subject->title,
                'prerequisite_codes' => $subject->prerequisites,
            ])
            ->values()
            ->toJson();

        $priorSubjects = collect();
        if ($isIrregular) {
            $subjectYearLevels = SubjectOffering::with('section.yearLevel')
                ->get()
                ->groupBy('subject_id')
                ->map(function ($subjectOfferings) {
                    $yearLevel = $subjectOfferings
                        ->pluck('section.yearLevel')
                        ->filter()
                        ->sortBy('sort_order')
                        ->first();

                    return [
                        'id' => $yearLevel?->id,
                        'label' => $yearLevel?->label,
                        'sort_order' => $yearLevel?->sort_order,
                    ];
                });

            $priorSubjects = \App\Models\Subject::orderBy('code')
                ->get()
                ->map(function ($subject) use ($subjectYearLevels) {
                    $yearLevel = $subjectYearLevels->get($subject->id, []);
                    $subject->prior_year_level_id = $yearLevel['id'] ?? null;
                    $subject->prior_year_level_label = $yearLevel['label'] ?? 'Unassigned';
                    $subject->prior_year_level_sort = $yearLevel['sort_order'] ?? 99;

                    return $subject;
                });
        }

        return view('student.enroll', [
            'user'           => $user,
            'activeSemester' => $activeSemester,
            'semesters'      => $semesters,
            'colleges'       => $colleges,
            'programs'       => $programs,
            'yearLevels'     => $yearLevels,
            'offeringsJson'   => $offeringsJson,
            'priorSubjects'   => $priorSubjects,
            'isIrregular'     => $isIrregular,
            'yearLevelsJson'  => $yearLevels->values()->toJson(),
            'curriculumJson'   => $curriculumJson,
            'passedSubjectCodes' => $passedSubjectCodes,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'semester_id'   => ['required', 'uuid', 'exists:semesters,id'],
            'program_id'    => ['required', 'uuid', 'exists:programs,id'],
            'year_level_id' => ['required', 'uuid', 'exists:year_levels,id'],
            'subject_offering_ids' => ['required', 'array', 'min:1'],
            'subject_offering_ids.*' => ['uuid', 'exists:subject_offerings,id'],
            'section_id' => ['nullable', 'uuid', 'exists:sections,id'],
            'prior_grades' => ['nullable', 'array'],
            'prior_grades.*' => ['nullable', 'numeric', 'between:1,5'],
            'tor_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $user = $request->user()->load('studentProfile');
        $isIrregular = $this->isIrregularStudentType($user->studentProfile?->student_type);

        // Prevent duplicate application
        $existing = EnrollmentApplication::where('student_id', $user->id)
            ->where('semester_id', $request->semester_id)
            ->whereHas('status', fn($q) => $q->whereIn('code', ['pending', 'approved']))
            ->first();

        if ($existing) {
            return redirect()->route('student.dashboard')
                ->with('status', 'You already have an active enrollment application this semester.');
        }

        $pendingStatus = ApplicationStatus::where('code', 'pending')->firstOrFail();
        $requestedStatus = SubjectEnrollmentStatus::where('code', 'requested')->firstOrFail();
        $selectedOfferingIds = collect($request->subject_offering_ids)->filter()->unique()->values()->all();

        if (empty($selectedOfferingIds)) {
            return back()->withErrors([
                'subject_offering_ids' => 'Please select at least one subject to enroll.',
            ])->withInput()->with('validation_payload', $this->validationPayload(
                'FAILED',
                $user->studentProfile?->student_type,
                $request->section_id,
                ['has_prerequisite_violations' => false, 'has_schedule_conflicts' => false, 'has_unit_cap_violations' => false],
                [],
                0
            ));
        }

        $selectedYearSort = YearLevel::whereKey($request->year_level_id)->value('sort_order');

        $offerings = SubjectOffering::with(['subject', 'section.yearLevel', 'subjectEnrollments.status'])
            ->whereIn('id', $selectedOfferingIds)
            ->get();

        $invalidOffering = $offerings->first(function ($offering) use ($request, $isIrregular, $selectedYearSort) {
            if ($offering->section->program_id !== $request->program_id) {
                return true;
            }

            $matchesSelectedContext = $offering->section->semester_id === $request->semester_id
                && $offering->section->year_level_id === $request->year_level_id;

            if ($matchesSelectedContext) {
                return false;
            }

            $isLowerRootBackSubject = $isIrregular
                && empty($offering->subject->prerequisites)
                && $selectedYearSort
                && ($offering->section->yearLevel->sort_order ?? 99) < $selectedYearSort;

            $isFlexibleBackfill = $isIrregular
                && empty($offering->subject->prerequisites)
                && $this->isFlexibleBackfillSubject($offering->subject->type, $offering->subject->code);

            return !$isLowerRootBackSubject && !$isFlexibleBackfill;
        });

        if ($invalidOffering || $offerings->count() !== count($selectedOfferingIds)) {
            return back()->withErrors([
                'subject_offering_ids' => 'Please select subjects from the chosen program, year level, and semester.',
            ])->withInput()->with('validation_payload', $this->validationPayload(
                'FAILED',
                $user->studentProfile?->student_type,
                $request->section_id,
                ['has_prerequisite_violations' => false, 'has_schedule_conflicts' => false, 'has_unit_cap_violations' => false],
                [],
                0
            ));
        }

        if (!$isIrregular && $offerings->pluck('section_id')->unique()->count() !== 1) {
            return back()->withErrors([
                'subject_offering_ids' => 'Please select subjects from one section schedule only.',
            ])->withInput();
        }

        $duplicateSubjectCodes = $offerings
            ->groupBy(fn($offering) => $offering->subject->code)
            ->filter(fn($subjectOfferings) => $subjectOfferings->count() > 1)
            ->keys();

        if ($duplicateSubjectCodes->isNotEmpty()) {
            return back()->withErrors([
                'subject_offering_ids' => 'Duplicate subject selected: ' . $duplicateSubjectCodes->implode(', ') . '. Please choose only one offering per subject.',
            ])->withInput();
        }

        $totalUnits = (float) $offerings->sum(fn($offering) => (float) $offering->subject->units);
        if ($totalUnits > self::MAX_SEMESTER_UNITS) {
            return back()->withErrors([
                'subject_offering_ids' => 'Selected subjects exceed the ' . self::MAX_SEMESTER_UNITS . '-unit semester limit.',
            ])->withInput()->with('validation_payload', $this->validationPayload(
                'FAILED',
                $user->studentProfile?->student_type,
                $request->section_id,
                ['has_prerequisite_violations' => false, 'has_schedule_conflicts' => false, 'has_unit_cap_violations' => true],
                [],
                $totalUnits
            ));
        }

        $fullOffering = $offerings->first(function ($offering) {
            $takenSeats = $offering->subjectEnrollments
                ->filter(fn($enrollment) => in_array($enrollment->status?->code, ['requested', 'enrolled'], true))
                ->count();

            return $takenSeats >= $offering->section->max_capacity;
        });

        if ($fullOffering) {
            return back()->withErrors([
                'subject_offering_ids' => "{$fullOffering->subject->code} is already at section capacity.",
            ])->withInput();
        }

        $scheduleConflict = $this->scheduleConflict($offerings);
        if ($scheduleConflict) {
            return back()->withErrors([
                'subject_offering_ids' => 'Schedule conflict detected between ' . $scheduleConflict[0] . ' and ' . $scheduleConflict[1] . '.',
            ])->withInput()->with('validation_payload', $this->validationPayload(
                'FAILED',
                $user->studentProfile?->student_type,
                $request->section_id,
                ['has_prerequisite_violations' => false, 'has_schedule_conflicts' => true, 'has_unit_cap_violations' => false],
                [],
                $totalUnits
            ));
        }

        $priorGrades = $this->validPriorGrades($request->input('prior_grades', []), $user->studentProfile?->student_type);
        $creditedDuplicate = $offerings->first(fn($offering) => array_key_exists($offering->subject->code, $priorGrades));

        if ($creditedDuplicate) {
            return back()->withErrors([
                'subject_offering_ids' => "{$creditedDuplicate->subject->code} is marked as already passed in prior grades. Remove the prior grade or choose a different subject.",
            ])->withInput()->with('validation_payload', $this->validationPayload(
                'FAILED',
                $user->studentProfile?->student_type,
                $request->section_id,
                ['has_prerequisite_violations' => false, 'has_schedule_conflicts' => false, 'has_unit_cap_violations' => false, 'has_unverified_credit_anomalies' => true],
                [],
                $totalUnits
            ));
        }

        if (!empty($priorGrades) && !$request->hasFile('tor_document')) {
            return back()->withErrors([
                'tor_document' => 'Upload your TOR or transfer credentials to support self-reported prior grades.',
            ])->withInput()->with('validation_payload', $this->validationPayload(
                'FAILED',
                $user->studentProfile?->student_type,
                $request->section_id,
                ['has_prerequisite_violations' => false, 'has_schedule_conflicts' => false, 'has_unit_cap_violations' => false, 'has_unverified_credit_anomalies' => true],
                [],
                $totalUnits
            ));
        }

        $passedSubjectCodes = array_values(array_unique(array_merge(
            $this->passedSubjectCodes($user->id),
            array_keys($priorGrades)
        )));

        // Anti-Double Enrollment Lock: Prevents simultaneously picking a prerequisite and its target
        $attemptedCompletedDuplicate = $offerings->first(fn($offering) => in_array($offering->subject->code, $passedSubjectCodes));
        if ($attemptedCompletedDuplicate) {
            return back()->withErrors([
                'subject_offering_ids' => "You cannot enroll in {$attemptedCompletedDuplicate->subject->code} because it is already completed or credited via prior history."
            ])->withInput();
        }

        $blocked = $offerings->filter(function ($offering) use ($passedSubjectCodes) {
            return !empty($this->missingPrerequisiteChain($offering->subject->code, $passedSubjectCodes));
        });

        if ($blocked->isNotEmpty()) {
            $blocks = $blocked->flatMap(function ($offering) use ($passedSubjectCodes) {
                return collect($this->missingPrerequisiteChain($offering->subject->code, $passedSubjectCodes))
                    ->map(fn($missing) => [
                        'selected_course_code' => $offering->subject->code,
                        'missing_dependency_code' => $missing,
                        'ui_action_required' => "Display error flag: 'Prerequisite not satisfied for: {$offering->subject->code}' and preserve current section scope.",
                    ]);
            })->values()->all();

            return back()->withErrors([
                'subject_offering_ids' => 'Prerequisite not satisfied for: ' . $blocked->pluck('subject.code')->implode(', '),
            ])->withInput()->with('validation_payload', $this->validationPayload(
                'FAILED',
                $user->studentProfile?->student_type,
                $request->section_id,
                ['has_prerequisite_violations' => true, 'has_schedule_conflicts' => false, 'has_unit_cap_violations' => false],
                $blocks,
                $totalUnits
            ));
        }

        $torDocumentPath = $request->hasFile('tor_document')
            ? $request->file('tor_document')->store('tor-documents', 'public')
            : null;

        DB::transaction(function () use ($user, $request, $pendingStatus, $requestedStatus, $offerings, $priorGrades, $torDocumentPath) {
            $application = EnrollmentApplication::create([
                'student_id'    => $user->id,
                'semester_id'   => $request->semester_id,
                'program_id'    => $request->program_id,
                'year_level_id' => $request->year_level_id,
                'status_id'     => $pendingStatus->id,
                'prior_subject_grades' => $priorGrades,
                'prior_subject_grades_verified' => false,
                'tor_document_path' => $torDocumentPath,
            ]);
            
            foreach ($offerings as $offering) {
                SubjectEnrollment::create([
                    'enrollment_id' => $application->id,
                    'subject_offering_id' => $offering->id,
                    'status_id' => $requestedStatus->id,
                ]);
            }
        });

        return redirect()->route('student.dashboard')
            ->with('status', 'Enrollment application submitted successfully!');
    }

    private function validationPayload(string $status, ?string $studentType, ?string $sectionId, array $errors, array $blocks, float $units): array
    {
        return [
            'validation_status' => $status,
            'student_status' => $studentType,
            'error_summary' => $errors,
            'view_filter_context' => [
                'preserve_section_id' => $sectionId,
                'section_label_enforced' => $sectionId ? Section::whereKey($sectionId)->value('name') : null,
                'back_subjects_injected' => $this->isIrregularStudentType($studentType),
                'elective_backfill_active' => $this->isIrregularStudentType($studentType),
                'resolved_root_dependencies' => [],
                'provisional_credits_claimed' => [],
                'tor_document_attached' => false,
                'prevent_global_fetch_leak' => true,
            ],
            'prerequisite_evaluation' => [
                'active_blocks' => $blocks,
                'disabled_due_to_existing_credit' => [],
            ],
            'schedule_analysis' => [
                'total_selected_units' => $units,
                'accepted_offerings' => [],
            ],
        ];
    }

    private function passedSubjectCodes(string $studentId): array
    {
        // Safe Plucking Engine: Direct field projection maps explicitly avoiding models formatting drift
        return DB::table('subject_enrollments')
            ->join('enrollment_applications', 'subject_enrollments.enrollment_id', '=', 'enrollment_applications.id')
            ->join('subject_offerings', 'subject_enrollments.subject_offering_id', '=', 'subject_offerings.id')
            ->join('subjects', 'subject_offerings.subject_id', '=', 'subjects.id')
            ->where('enrollment_applications.student_id', $studentId)
            ->whereNotNull('subject_enrollments.grade')
            ->where('subject_enrollments.grade', '<=', 3.00)
            ->pluck('subjects.code')
            ->unique()
            ->values()
            ->all();
    }

    private function missingPrerequisiteChain(string $subjectCode, array $passedSubjectCodes, array $visited = []): array
    {
        if (in_array($subjectCode, $visited, true)) {
            return [];
        }

        $subject = \App\Models\Subject::where('code', $subjectCode)->first();
        if (!$subject) {
            return [];
        }

        $missing = [];
        foreach ($subject->prerequisites as $prerequisiteCode) {
            if (!in_array($prerequisiteCode, $passedSubjectCodes, true)) {
                $missing[] = $prerequisiteCode;
                continue;
            }

            $missing = array_merge(
                $missing,
                $this->missingPrerequisiteChain($prerequisiteCode, $passedSubjectCodes, [...$visited, $subjectCode])
            );
        }

        return array_values(array_unique($missing));
    }

    private function resolvedRootDependenciesForSection(Section $section, array $passedSubjectCodes): array
    {
        return SubjectOffering::with('subject')
            ->where('section_id', $section->id)
            ->get()
            ->flatMap(function ($offering) use ($passedSubjectCodes) {
                return collect($this->missingPrerequisiteChain($offering->subject->code, $passedSubjectCodes))
                    ->map(fn($code) => $this->firstUnearnedRootDependency($code, $passedSubjectCodes))
                    ->filter();
            })
            ->unique()
            ->values()
            ->all();
    }

    private function firstUnearnedRootDependency(string $subjectCode, array $passedSubjectCodes, array $visited = []): ?string
    {
        if (in_array($subjectCode, $visited, true)) {
            return $subjectCode;
        }

        $subject = \App\Models\Subject::where('code', $subjectCode)->first();
        if (!$subject) {
            return $subjectCode;
        }

        if (in_array($subjectCode, $passedSubjectCodes, true)) {
            foreach ($subject->prerequisites as $prerequisiteCode) {
                $root = $this->firstUnearnedRootDependency($prerequisiteCode, $passedSubjectCodes, [...$visited, $subjectCode]);
                if ($root && !in_array($root, $passedSubjectCodes, true)) {
                    return $root;
                }
            }

            return null;
        }

        foreach ($subject->prerequisites as $prerequisiteCode) {
            $root = $this->firstUnearnedRootDependency($prerequisiteCode, $passedSubjectCodes, [...$visited, $subjectCode]);
            if ($root && !in_array($root, $passedSubjectCodes, true)) {
                return $root;
            }
        }

        return $subjectCode;
    }

    private function validPriorGrades(array $priorGrades, ?string $studentType): array
    {
        if (!$this->isIrregularStudentType($studentType)) {
            return [];
        }

        return collect($priorGrades)
            ->filter(fn($grade) => $grade !== null && $grade !== '' && (float) $grade <= 3.00)
            ->map(fn($grade) => (float) $grade)
            ->all();
    }

    private function isIrregularStudentType(?string $studentType): bool
    {
        return $studentType
            && ($studentType === 'transferee'
                || $studentType === 'shiftee'
                || str_starts_with($studentType, 'transferee_')
                || str_starts_with($studentType, 'shiftee_'));
    }

    private function isFlexibleBackfillSubject(?string $type, string $code): bool
    {
        return strtoupper((string) $type) === 'GE'
            || str_starts_with(strtoupper($code), 'GEED')
            || str_starts_with(strtoupper($code), 'ELEC');
    }

    private function scheduleConflict($offerings): ?array
    {
        $parsed = $offerings->map(fn($offering) => [
            'code' => $offering->subject->code,
            'slots' => $this->parseSchedule($offering->schedule),
        ]);

        foreach ($parsed as $leftIndex => $left) {
            foreach ($parsed->slice($leftIndex + 1) as $right) {
                foreach ($left['slots'] as $leftSlot) {
                    foreach ($right['slots'] as $rightSlot) {
                        if (
                            $leftSlot['day'] === $rightSlot['day']
                            && $leftSlot['start'] < $rightSlot['end']
                            && $rightSlot['start'] < $leftSlot['end']
                        ) {
                            return [$left['code'], $right['code']];
                        }
                    }
                }
            }
        }

        return null;
    }

    private function parseSchedule(?string $schedule): array
    {
        if (!$schedule || strtoupper($schedule) === 'TBA') {
            return [];
        }

        if (!preg_match('/^([A-Za-z]+)\s+(.+)$/', trim($schedule), $matches)) {
            return [];
        }

        $timeRange = preg_replace('/\s+/', ' ', trim($matches[2]));
        if (!preg_match('/^(.+?)-(.+)$/', $timeRange, $timeMatches)) {
            return [];
        }

        [$startText, $endText] = [trim($timeMatches[1]), trim($timeMatches[2])];

        // Explicit 12-hour boundary translation logic
        $endIsPM = preg_match('/\bPM\b/i', $endText);
        $startHasMeridian = preg_match('/\b(AM|PM)\b/i', $startText);

        if (!$startHasMeridian && $endIsPM) {
            $startHour = (int) current(explode(':', $startText));
            $endHour = (int) current(explode(':', $endText));

            // Evaluates cascading afternoon periods (e.g. 11:30 - 1:30 PM matches 11:30 AM)
            if ($startHour > $endHour && $startHour !== 12) {
                $startText .= ' AM';
            } else {
                $startText .= ' PM';
            }
        } elseif (!$startHasMeridian && !$endIsPM) {
            $startText .= ' AM';
        }

        $start = strtotime($startText);
        $end = strtotime($endText);
        if ($start === false || $end === false) {
            return [];
        }

        $days = $this->parseScheduleDays($matches[1]);

        return collect($days)->map(fn($day) => [
            'day' => $day,
            'start' => (int) date('Hi', $start),
            'end' => (int) date('Hi', $end),
        ])->all();
    }

    private function parseScheduleDays(string $days): array
    {
        $days = trim($days);
        $tokens = [];

        // 1. Handle explicit 3-letter abbreviations
        $threeLetterMap = [
            'Mon' => 'Mon',
            'Tue' => 'Tue',
            'Wed' => 'Wed',
            'Thu' => 'Thu',
            'Fri' => 'Fri',
            'Sat' => 'Sat',
            'Sun' => 'Sun',
        ];

        foreach ($threeLetterMap as $search => $resolved) {
            if (stripos($days, $search) !== false) {
                $tokens[] = $resolved;
                $days = str_ireplace($search, '', $days);
            }
        }

        // 2. Handle 2-letter compact representations
        if (stripos($days, 'Th') !== false) {
            $tokens[] = 'Thu';
            $days = str_ireplace('Th', '', $days);
        }

        // 3. Fallback for single-letter compact codes
        foreach (str_split($days) as $char) {
            $upperChar = strtoupper($char);
            $resolved = match ($upperChar) {
                'M' => 'Mon',
                'T' => 'Tue',
                'W' => 'Wed',
                'H' => 'Thu',
                'F' => 'Fri',
                'S' => 'Sat',
                default => null,
            };

            if ($resolved) {
                $tokens[] = $resolved;
            }
        }

        return array_values(array_unique(array_filter($tokens)));
    }
}
