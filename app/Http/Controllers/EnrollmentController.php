<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use App\Models\EnrollmentApplication;
use App\Models\Program;
use App\Models\Semester;
use App\Models\YearLevel;
use Illuminate\Http\Request;

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
            ->get();

        $programs   = Program::with('college')
            ->orderBy('code')
            ->get();

        $yearLevels = YearLevel::orderBy('sort_order')->get();

        return view('student.enroll', [
            'user'           => $user,
            'activeSemester' => $activeSemester,
            'semesters'      => $semesters,
            'programs'       => $programs,
            'yearLevels'     => $yearLevels,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'semester_id'   => ['required', 'uuid', 'exists:semesters,id'],
            'program_id'    => ['required', 'uuid', 'exists:programs,id'],
            'year_level_id' => ['required', 'uuid', 'exists:year_levels,id'],
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

        EnrollmentApplication::create([
            'student_id'    => $user->id,
            'semester_id'   => $request->semester_id,
            'program_id'    => $request->program_id,
            'year_level_id' => $request->year_level_id,
            'status_id'     => $pendingStatus->id,
        ]);

        return redirect()->route('student.dashboard')
            ->with('status', 'Enrollment application submitted successfully!');
    }
}