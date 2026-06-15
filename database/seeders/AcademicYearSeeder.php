<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $academicYearId = Str::uuid();

        DB::table('academic_years')->insertOrIgnore([
            'id'         => $academicYearId,
            'label'      => '2025-2026',
            'year_start' => 2025,
            'year_end'   => 2026,
            'is_active'  => true,
        ]);

        DB::table('semesters')->insertOrIgnore([
            [
                'id'               => Str::uuid(),
                'academic_year_id' => $academicYearId,
                'label'            => '1st Semester',
                'start_date'       => '2025-08-01',
                'end_date'         => '2025-12-20',
                'is_active'        => false,
            ],
            [
                'id'               => Str::uuid(),
                'academic_year_id' => $academicYearId,
                'label'            => '2nd Semester',
                'start_date'       => '2026-02-09',
                'end_date'         => '2026-06-21',
                'is_active'        => true,
            ],
            [
                'id'               => Str::uuid(),
                'academic_year_id' => $academicYearId,
                'label'            => 'Summer',
                'start_date'       => '2026-06-22',
                'end_date'         => '2026-07-31',
                'is_active'        => false,
            ],
        ]);
    }
}