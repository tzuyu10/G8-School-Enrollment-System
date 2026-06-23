<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BaseLookupSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['id' => (string) Str::uuid(), 'code' => 'student', 'label' => 'Student', 'description' => 'Student user'],
            ['id' => (string) Str::uuid(), 'code' => 'faculty', 'label' => 'Faculty', 'description' => 'Faculty user'],
            ['id' => (string) Str::uuid(), 'code' => 'registrar', 'label' => 'Registrar', 'description' => 'Registrar user'],
            ['id' => (string) Str::uuid(), 'code' => 'admin', 'label' => 'Admin', 'description' => 'System administrator'],
        ]);

        DB::table('profile_statuses')->insertOrIgnore([
            ['id' => (string) Str::uuid(), 'code' => 'pending', 'label' => 'Pending', 'color' => 'warning'],
            ['id' => (string) Str::uuid(), 'code' => 'active', 'label' => 'Active', 'color' => 'success'],
            ['id' => (string) Str::uuid(), 'code' => 'inactive', 'label' => 'Inactive', 'color' => 'secondary'],
        ]);

        DB::table('application_statuses')->insertOrIgnore([
            ['id' => (string) Str::uuid(), 'code' => 'pending', 'label' => 'Pending', 'color' => 'warning'],
            ['id' => (string) Str::uuid(), 'code' => 'approved', 'label' => 'Approved', 'color' => 'success'],
            ['id' => (string) Str::uuid(), 'code' => 'rejected', 'label' => 'Rejected', 'color' => 'danger'],
        ]);

        DB::table('subject_enrollment_statuses')->insertOrIgnore([
            ['id' => (string) Str::uuid(), 'code' => 'requested', 'label' => 'Requested', 'color' => 'warning'],
            ['id' => (string) Str::uuid(), 'code' => 'enrolled', 'label' => 'Enrolled', 'color' => 'success'],
            ['id' => (string) Str::uuid(), 'code' => 'dropped', 'label' => 'Dropped', 'color' => 'danger'],
        ]);

        foreach ([
            ['label' => '1st Year', 'sort_order' => 1],
            ['label' => '2nd Year', 'sort_order' => 2],
            ['label' => '3rd Year', 'sort_order' => 3],
            ['label' => '4th Year', 'sort_order' => 4],
        ] as $yearLevel) {
            DB::table('year_levels')->updateOrInsert(
                ['label' => $yearLevel['label']],
                [
                    'id' => DB::table('year_levels')->where('label', $yearLevel['label'])->value('id') ?? (string) Str::uuid(),
                    'sort_order' => $yearLevel['sort_order'],
                ]
            );
        }
    }
}
