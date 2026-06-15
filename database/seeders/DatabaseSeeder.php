<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BaseLookupSeeder::class,
            AcademicYearSeeder::class,
            CollegeAndProgramSeeder::class,
            SubjectSeeder::class,
            SectionAndOfferingSeeder::class,
        ]);
    }
}
