<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentApplication;
use Illuminate\Http\Request;

class RecordsController extends Controller
{
    public function records(Request $request)
    {
        $applications = EnrollmentApplication::where('student_id', $request->user()->id)
            ->with([
                'semester.academicYear',
                'program',
                'yearLevel',
                'status',
                'sectionAssignment.section',
                'subjectEnrollments.subjectOffering.subject',
                'subjectEnrollments.subjectOffering.faculty',
                'subjectEnrollments.status',
                'subjectEnrollments.grader',
            ])
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($application) {
                $enrollments = $application->subjectEnrollments->filter(function ($enrollment) {
                    return $enrollment->status
                        && $enrollment->status->code === 'enrolled'
                        && $enrollment->grade !== null;
                });

                $totalGradePoints = 0;
                $totalUnits = 0;

                foreach ($enrollments as $enrollment) {
                    $units = (float) ($enrollment->subjectOffering->subject->units ?? 0);

                    $totalGradePoints += (float) $enrollment->grade * $units;
                    $totalUnits += $units;
                }

                $application->gwa = $totalUnits > 0
                    ? round($totalGradePoints / $totalUnits, 2)
                    : null;

                return $application;
            });
        return view('student.records', compact('applications'));
    }
}
