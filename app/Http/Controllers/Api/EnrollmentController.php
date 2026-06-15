<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatus;
use App\Models\EnrollmentApplication;
use App\Models\Section;
use App\Models\SectionAssignment;
use App\Models\StudentProfile;
use App\Models\SubjectEnrollment;
use App\Models\SubjectEnrollmentStatus;
use App\Models\SubjectOffering;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    // Student: submit enrollment application
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'semester_id'   => ['required', 'uuid', 'exists:semesters,id'],
            'program_id'    => ['required', 'uuid', 'exists:programs,id'],
            'year_level_id' => ['required', 'uuid', 'exists:year_levels,id'],
            'student_type'  => ['required', 'in:freshman,transferee,shiftee,returnee'],
        ]);

        // Check if student already has an application this semester
        $existing = EnrollmentApplication::where('student_id', $request->user()->id)
            ->where('semester_id', $request->semester_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You already have an application for this semester.',
                'application' => $existing->load('status'),
            ], 422);
        }

        $pendingStatus = ApplicationStatus::where('code', 'pending')->firstOrFail();

        $application = EnrollmentApplication::create([
            'student_id'    => $request->user()->id,
            'semester_id'   => $request->semester_id,
            'program_id'    => $request->program_id,
            'year_level_id' => $request->year_level_id,
            'student_type'  => $request->student_type,
            'status_id'     => $pendingStatus->id,
        ]);

        return response()->json([
            'message'     => 'Enrollment application submitted successfully.',
            'application' => $application->load(['semester', 'program', 'yearLevel', 'status']),
        ], 201);
    }

    // Student: check own application status
    public function status(Request $request): JsonResponse
    {
        $applications = EnrollmentApplication::where('student_id', $request->user()->id)
            ->with([
                'semester.academicYear',
                'program.college',
                'yearLevel',
                'status',
                'sectionAssignment.section',
                'subjectEnrollments.subjectOffering.subject',
                'subjectEnrollments.status',
            ])
            ->orderBy('submitted_at', 'desc')
            ->get();

        return response()->json([
            'applications' => $applications,
        ]);
    }

    // Registrar: list all applications
    public function index(Request $request): JsonResponse
    {
        $query = EnrollmentApplication::with([
            'student.studentProfile',
            'semester.academicYear',
            'program.college',
            'yearLevel',
            'status',
            'sectionAssignment.section',
        ]);

        // Filter by status code
        if ($request->has('status')) {
            $query->whereHas('status', fn($q) => $q->where('code', $request->status));
        }

        // Filter by semester
        if ($request->has('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        $applications = $query->orderBy('submitted_at', 'desc')->get();

        return response()->json([
            'applications' => $applications,
        ]);
    }

    // Registrar: approve application + assign section + assign subjects in one action
    public function approve(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'section_id' => ['required', 'uuid', 'exists:sections,id'],
        ]);

        $application = EnrollmentApplication::findOrFail($id);
        $approvedStatus = ApplicationStatus::where('code', 'approved')->firstOrFail();
        $enrolledStatus = SubjectEnrollmentStatus::where('code', 'enrolled')->firstOrFail();

        // Check if already approved
        if ($application->status->code === 'approved') {
            return response()->json([
                'message' => 'Application is already approved.',
            ], 422);
        }

        DB::transaction(function () use ($application, $request, $approvedStatus, $enrolledStatus) {
            // 1. Update application status to approved
            $application->update([
                'status_id'   => $approvedStatus->id,
                'reviewed_by' => request()->user()->id,
                'reviewed_at' => now(),
            ]);

            // 2. Assign section
            SectionAssignment::create([
                'enrollment_id' => $application->id,
                'section_id'    => $request->section_id,
                'assigned_by'   => request()->user()->id,
            ]);

            // 3. Auto-assign all subjects from the section's offerings
            $offerings = SubjectOffering::where('section_id', $request->section_id)->get();

            foreach ($offerings as $offering) {
                SubjectEnrollment::create([
                    'enrollment_id'       => $application->id,
                    'subject_offering_id' => $offering->id,
                    'status_id'           => $enrolledStatus->id,
                ]);
            }

            // 4. Set profile status to active
            $application->student->update([
                'status_id' => \App\Models\ProfileStatus::where('code', 'active')->first()->id,
            ]);

            // 5. Assign student number once, after first approved enrollment.
            $studentProfile = StudentProfile::where('profile_id', $application->student_id)
                ->lockForUpdate()
                ->first();

            if ($studentProfile && blank($studentProfile->student_number)) {
                $studentProfile->update([
                    'student_number' => $this->generateStudentNumber(),
                ]);
            }
        });

        return response()->json([
            'message'     => 'Application approved successfully.',
            'application' => $application->fresh()->load([
                'status',
                'student.studentProfile',
                'sectionAssignment.section',
                'subjectEnrollments.subjectOffering.subject',
            ]),
        ]);
    }

    private function generateStudentNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "{$year}-";
        $suffix = "-MN-0";

        $latest = StudentProfile::whereNotNull('student_number')
            ->where('student_number', 'like', "{$prefix}%{$suffix}")
            ->lockForUpdate()
            ->orderBy('student_number', 'desc')
            ->value('student_number');

        $nextSequence = 1;

        if ($latest && preg_match('/^\d{4}-(\d{5})-MN-0$/', $latest, $matches)) {
            $nextSequence = ((int) $matches[1]) + 1;
        }

        return sprintf('%s%05d%s', $prefix, $nextSequence, $suffix);
    }

    // Registrar: reject application
    public function reject(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'remarks' => ['required', 'string'],
        ]);

        $application = EnrollmentApplication::findOrFail($id);
        $rejectedStatus = ApplicationStatus::where('code', 'rejected')->firstOrFail();

        if ($application->status->code !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can be rejected.',
            ], 422);
        }

        $application->update([
            'status_id'   => $rejectedStatus->id,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        return response()->json([
            'message'     => 'Application rejected.',
            'application' => $application->fresh()->load('status'),
        ]);
    }
}
