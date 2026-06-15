<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use App\Models\EnrollmentApplication;
use App\Models\Program;
use App\Models\Semester;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EnrollmentController extends Controller
{
    public function form(Request $request)
    {
        $user = $request->user()->load('studentProfile');

        // Check if already applied this active semester
        $activeSemester = Cache::remember('active_semester', 3600, fn() =>
            Semester::where('is_active', true)->first()
        );

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

        $semesters  = Cache::remember('all_semesters', 3600, fn() =>
            Semester::with('academicYear')->orderBy('is_active', 'desc')->get()
        );
        $programs   = Cache::remember('all_programs', 3600, fn() =>
            Program::with('college')->orderBy('code')->get()
        );
        $yearLevels = Cache::remember('year_levels', 3600, fn() =>
            YearLevel::orderBy('sort_order')->get()
        );

        return view('enroll', [
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

        $pendingStatus = Cache::remember('status_pending_app', 3600, fn() =>
            ApplicationStatus::where('code', 'pending')->first()
        );

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