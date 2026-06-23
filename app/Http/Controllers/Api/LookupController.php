<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function semesters(): JsonResponse
    {
        return response()->json([
            'semesters' => Semester::with('academicYear')
                ->orderBy('is_active', 'desc')
                ->get(),
        ]);
    }

    public function colleges(): JsonResponse
    {
        return response()->json([
            'colleges' => College::with('programs')->get(),
        ]);
    }

    public function programs(): JsonResponse
    {
        return response()->json([
            'programs' => Program::with('college')->get(),
        ]);
    }

    public function yearLevels(): JsonResponse
    {
        return response()->json([
            'year_levels' => YearLevel::orderBy('sort_order')->get(),
        ]);
    }

    public function sections(Request $request): JsonResponse
    {
        $query = Section::with(['program', 'yearLevel', 'semester', 'adviser']);

        if ($request->has('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        return response()->json([
            'sections' => $query->get(),
        ]);
    }

    public function subjects(Request $request): JsonResponse
    {
        $query = Subject::with('program');

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id)
                  ->orWhereNull('program_id');
        }

        return response()->json([
            'subjects' => $query->get(),
        ]);
    }
}