<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SubjectEnrollment;
use App\Models\SubjectOffering;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        return view('faculty.faculty', [
            'advisedSections' => Section::where('adviser_id', $request->user()->id)
                ->with(['program', 'yearLevel', 'semester'])
                ->get(),
            'subjectOfferings' => SubjectOffering::where('faculty_id', $request->user()->id)
                ->with([
                    'subject',
                    'section.semester',
                    'subjectEnrollments.enrollmentApplication.student.studentProfile',
                    'subjectEnrollments.status',
                ])
                ->get(),
        ]);
    }

    public function updateGrade(Request $request, string $id)
    {
        $data = $request->validate([
            'grade' => ['nullable', 'numeric', 'between:1,5'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $enrollment = SubjectEnrollment::with('subjectOffering')->findOrFail($id);

        abort_unless($enrollment->subjectOffering->faculty_id === $request->user()->id, 403);

        $enrollment->update([
            'grade' => $data['grade'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'graded_by' => $request->user()->id,
            'graded_at' => now(),
        ]);

        return back()->with('status', 'Grade saved.');
    }
}
