<?php

namespace App\Http\Controllers;

use App\Models\Section;
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
                ->with(['subject', 'section.semester'])
                ->get(),
        ]);
    }
}
