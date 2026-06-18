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
            ->get();

        return view('student.records', compact('applications'));
    }
}
