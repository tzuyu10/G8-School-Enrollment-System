<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use App\Models\College;
use App\Models\EnrollmentApplication;
use App\Models\Program;
use App\Models\Semester;
use App\Models\SubjectEnrollment;
use App\Models\SubjectEnrollmentStatus;
use App\Models\SubjectOffering;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
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
            ->unique(fn ($semester) => $semester->academic_year_id . '|' . $semester->label)
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
        $offerings = SubjectOffering::with(['subject', 'section', 'faculty'])
            ->whereHas('section', function ($q) use ($activeSemester) {
                if ($activeSemester) {
                    $q->where('semester_id', $activeSemester->id);
                }
            })
            ->orderBy('section_id')
            ->get();

        $passedSubjectCodes = $this->passedSubjectCodes($user->id);
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
                'code' => $offering->subject->code,
                'title' => $offering->subject->title,
                'units' => $offering->subject->units,
                'schedule' => $offering->schedule ?: 'TBA',
                'room' => $offering->room ?: 'TBA',
                'faculty' => $offering->faculty->full_name ?? 'TBA',
                'prerequisites' => implode(', ', $prerequisites) ?: 'None',
                'eligible' => empty($missing),
                'missing' => implode(', ', $missing),
            ];
        })->values()->toJson();

        return view('student.enroll', [
            'user'           => $user,
            'activeSemester' => $activeSemester,
            'semesters'      => $semesters,
            'colleges'       => $colleges,
            'programs'       => $programs,
            'yearLevels'     => $yearLevels,
            'offeringsJson'   => $offeringsJson,
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
        ]);

        $user = $request->user();

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
        $offerings = SubjectOffering::with(['subject', 'section'])
            ->whereIn('id', $request->subject_offering_ids)
            ->get();

        $invalidOffering = $offerings->first(fn ($offering) =>
            $offering->section->semester_id !== $request->semester_id
            || $offering->section->program_id !== $request->program_id
            || $offering->section->year_level_id !== $request->year_level_id
        );

        if ($invalidOffering || $offerings->count() !== count(array_unique($request->subject_offering_ids))) {
            return back()->withErrors([
                'subject_offering_ids' => 'Please select subjects from the chosen program, year level, and semester.',
            ])->withInput();
        }

        if ($offerings->pluck('section_id')->unique()->count() !== 1) {
            return back()->withErrors([
                'subject_offering_ids' => 'Please select subjects from one section schedule only.',
            ])->withInput();
        }

        $passedSubjectCodes = $this->passedSubjectCodes($user->id);
        $blocked = $offerings->filter(function ($offering) use ($passedSubjectCodes) {
            return !empty(array_diff($offering->subject->prerequisites, $passedSubjectCodes));
        });

        if ($blocked->isNotEmpty()) {
            return back()->withErrors([
                'subject_offering_ids' => 'Prerequisite not satisfied for: ' . $blocked->pluck('subject.code')->implode(', '),
            ])->withInput();
        }

        DB::transaction(function () use ($user, $request, $pendingStatus, $requestedStatus, $offerings) {
            $application = EnrollmentApplication::create([
                'student_id'    => $user->id,
                'semester_id'   => $request->semester_id,
                'program_id'    => $request->program_id,
                'year_level_id' => $request->year_level_id,
                'status_id'     => $pendingStatus->id,
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

    private function passedSubjectCodes(string $studentId): array
    {
        return SubjectEnrollment::whereHas('enrollmentApplication', fn ($q) => $q->where('student_id', $studentId))
            ->whereNotNull('grade')
            ->where('grade', '<=', 3.00)
            ->with('subjectOffering.subject')
            ->get()
            ->pluck('subjectOffering.subject.code')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
