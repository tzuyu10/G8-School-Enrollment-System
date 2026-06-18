<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReassignSubjectOfferingFacultySeeder extends Seeder
{
    public function run(): void
    {
        $facultyIds = DB::table('profiles')
            ->join('roles', 'roles.id', '=', 'profiles.role_id')
            ->where('roles.code', 'faculty')
            ->orderBy('profiles.last_name')
            ->orderBy('profiles.first_name')
            ->pluck('profiles.id')
            ->all();

        if (empty($facultyIds)) {
            $this->command?->warn('No faculty users found. Run SectionAndOfferingSeeder first.');
            return;
        }

        $loads = [];
        foreach ($facultyIds as $facultyId) {
            $loads[$facultyId] = ['subjects' => [], 'sections' => []];
        }

        $offerings = DB::table('subject_offerings')
            ->join('subjects', 'subjects.id', '=', 'subject_offerings.subject_id')
            ->select(
                'subject_offerings.id',
                'subject_offerings.section_id',
                'subjects.code as subject_code'
            )
            ->orderBy('subjects.code')
            ->orderBy('subject_offerings.section_id')
            ->get();

        foreach ($offerings as $offering) {
            $facultyId = $this->pickFaculty($facultyIds, $loads, $offering->subject_code, $offering->section_id);

            DB::table('subject_offerings')
                ->where('id', $offering->id)
                ->update(['faculty_id' => $facultyId]);
        }

        $this->command?->info('Existing subject offerings were reassigned to faculty.');
    }

    private function pickFaculty(array $facultyIds, array &$loads, string $subjectCode, string $sectionId): string
    {
        foreach ($facultyIds as $facultyId) {
            $subjects = $loads[$facultyId]['subjects'];
            $sections = $loads[$facultyId]['sections'];
            $wouldAddSubject = !in_array($subjectCode, $subjects, true);
            $wouldAddSection = !in_array($sectionId, $sections, true);

            if (count($subjects) + ($wouldAddSubject ? 1 : 0) <= 2
                && count($sections) + ($wouldAddSection ? 1 : 0) <= 6) {
                if ($wouldAddSubject) {
                    $loads[$facultyId]['subjects'][] = $subjectCode;
                }

                if ($wouldAddSection) {
                    $loads[$facultyId]['sections'][] = $sectionId;
                }

                return $facultyId;
            }
        }

        return $facultyIds[array_key_first($facultyIds)];
    }
}
