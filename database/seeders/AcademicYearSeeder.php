<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $academicYearId = (string) Str::uuid();

        DB::table('academic_years')->insertOrIgnore([
            'id'         => $academicYearId,
            'label'      => '2025-2026',
            'year_start' => 2025,
            'year_end'   => 2026,
            'is_active'  => true,
        ]);

        $academicYearId = DB::table('academic_years')
            ->where('label', '2025-2026')
            ->value('id');

        $semesters = [
            [
                'academic_year_id' => $academicYearId,
                'label'            => '1st Semester',
                'start_date'       => '2025-08-01',
                'end_date'         => '2025-12-20',
                'is_active'        => false,
            ],
            [
                'academic_year_id' => $academicYearId,
                'label'            => '2nd Semester',
                'start_date'       => '2026-02-09',
                'end_date'         => '2026-06-21',
                'is_active'        => true,
            ],
            [
                'academic_year_id' => $academicYearId,
                'label'            => 'Summer',
                'start_date'       => '2026-06-22',
                'end_date'         => '2026-07-31',
                'is_active'        => false,
            ],
        ];

        foreach ($semesters as $semester) {
            DB::table('semesters')->updateOrInsert(
                [
                    'academic_year_id' => $semester['academic_year_id'],
                    'label' => $semester['label'],
                ],
                [
                    'id' => DB::table('semesters')
                        ->where('academic_year_id', $semester['academic_year_id'])
                        ->where('label', $semester['label'])
                        ->value('id') ?? (string) Str::uuid(),
                    'start_date' => $semester['start_date'],
                    'end_date' => $semester['end_date'],
                    'is_active' => $semester['is_active'],
                ]
            );
        }
    }
}
