<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentApplication;
use App\Models\Semester;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load(['role', 'status', 'studentProfile']);

        $applications = EnrollmentApplication::where('student_id', $user->id)
            ->with([
                'semester.academicYear',
                'program.college',
                'yearLevel',
                'status',
                'sectionAssignment.section',
                'subjectEnrollments.subjectOffering.subject',
                'subjectEnrollments.subjectOffering.faculty',
                'subjectEnrollments.status',
            ])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $activeSemester = Semester::where('is_active', true)->first();

        $hasActiveApplication = $activeSemester
            ? $applications
                ->where('semester_id', $activeSemester->id)
                ->filter(fn($a) => in_array($a->status->code ?? '', ['pending', 'approved']))
                ->isNotEmpty()
            : false;

        return view('student.student', compact('user', 'applications', 'hasActiveApplication'));
    }

    /**
     * Show the student's full profile.
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load(['role', 'status', 'studentProfile']);

        $profileStatus = $user->studentProfile
            ? $user->studentProfile->getProfileStatus()
            : [
                'is_complete'    => false,
                'label'          => 'Incomplete',
                'percentage'     => 0,
                'missing_fields' => [],
            ];

        return view('student.profile', compact('user', 'profileStatus'));
    }
}