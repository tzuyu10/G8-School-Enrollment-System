<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use App\Models\EnrollmentApplication;
use App\Models\ProfileStatus;
use App\Models\Section;
use App\Models\SectionAssignment;
use App\Models\StudentProfile;
use App\Models\SubjectEnrollment;
use App\Models\SubjectEnrollmentStatus;
use App\Models\SubjectOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegistrarController extends Controller
{
    public function index()
    {
        $pendingApplications = EnrollmentApplication::whereHas(
            'status',
            fn($q) => $q->where('code', 'pending')
        )->count();

        $recentApplications = EnrollmentApplication::with([
            'student.studentProfile',
            'semester.academicYear',
            'program',
            'yearLevel',
            'status',
            'sectionAssignment.section',
        ])
            ->orderBy('submitted_at', 'desc')
            ->limit(20)
            ->get();

        $sections = Section::with(['program', 'yearLevel', 'semester'])
            ->orderBy('name')
            ->get();

        return view('registrar', compact('pendingApplications', 'recentApplications', 'sections'));
    }

    public function approve(Request $request, string $id)
    {
        $request->validate([
            'section_id' => ['required', 'uuid', 'exists:sections,id'],
        ]);

        $application    = EnrollmentApplication::with('status', 'student')->findOrFail($id);
        $approvedStatus = ApplicationStatus::where('code', 'approved')->firstOrFail();
        $enrolledStatus = SubjectEnrollmentStatus::where('code', 'enrolled')->firstOrFail();

        if ($application->status->code === 'approved') {
            return back()->with('error', 'Application is already approved.');
        }

        DB::transaction(function () use ($application, $request, $approvedStatus, $enrolledStatus) {
            // 1. Approve application
            $application->update([
                'status_id'   => $approvedStatus->id,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // 2. Assign section
            SectionAssignment::create([
                'enrollment_id' => $application->id,
                'section_id'    => $request->section_id,
                'assigned_by'   => auth()->id(),
            ]);

            // 3. Auto-enroll subjects
            $offerings = SubjectOffering::where('section_id', $request->section_id)->get();
            foreach ($offerings as $offering) {
                SubjectEnrollment::firstOrCreate(
                    [
                        'enrollment_id'       => $application->id,
                        'subject_offering_id' => $offering->id,
                    ],
                    ['status_id' => $enrolledStatus->id]
                );
            }

            // 4. Activate student profile
            $application->student->update([
                'status_id' => ProfileStatus::where('code', 'active')->value('id'),
            ]);

            // 5. Assign student number if not yet assigned
            $studentProfile = StudentProfile::where('profile_id', $application->student_id)
                ->lockForUpdate()
                ->first();

            if ($studentProfile && blank($studentProfile->student_number)) {
                $studentProfile->update([
                    'student_number' => $this->generateStudentNumber(),
                ]);
            }
        });

        // Clear cached sections
        Cache::forget('all_sections');

        return redirect()
            ->route('registrar.dashboard')
            ->with('status', 'Application approved successfully.');
    }

    public function reject(Request $request, string $id)
    {
        $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        $application    = EnrollmentApplication::with('status')->findOrFail($id);
        $rejectedStatus = ApplicationStatus::where('code', 'rejected')->firstOrFail();

        if ($application->status->code !== 'pending') {
            return back()->with('error', 'Only pending applications can be rejected.');
        }

        $application->update([
            'status_id'   => $rejectedStatus->id,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        return redirect()
            ->route('registrar.dashboard')
            ->with('status', 'Application rejected.');
    }

    private function generateStudentNumber(): string
    {
        $year   = now()->format('Y');
        $prefix = "{$year}-";
        $suffix = "-MN-0";

        $latest = StudentProfile::whereNotNull('student_number')
            ->where('student_number', 'like', "{$prefix}%{$suffix}")
            ->lockForUpdate()
            ->orderBy('student_number', 'desc')
            ->value('student_number');

        $next = 1;
        if ($latest && preg_match('/^\d{4}-(\d{5})-MN-0$/', $latest, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return sprintf('%s%05d%s', $prefix, $next, $suffix);
    }
}
