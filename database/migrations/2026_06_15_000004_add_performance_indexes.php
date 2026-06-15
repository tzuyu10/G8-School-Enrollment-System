<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->index('role_id');
            $table->index('status_id');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->index('profile_id');
        });

        Schema::table('enrollment_applications', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('semester_id');
            $table->index('status_id');
        });

        Schema::table('section_assignments', function (Blueprint $table) {
            $table->index('section_id');
        });

        Schema::table('subject_enrollments', function (Blueprint $table) {
            $table->index('enrollment_id');
            $table->index('subject_offering_id');
        });

        Schema::table('subject_offerings', function (Blueprint $table) {
            $table->index('section_id');
            $table->index('faculty_id');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->index('semester_id');
            $table->index('program_id');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex(['role_id']);
            $table->dropIndex(['status_id']);
        });
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropIndex(['profile_id']);
        });
        Schema::table('enrollment_applications', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['semester_id']);
            $table->dropIndex(['status_id']);
        });
        Schema::table('section_assignments', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
        });
        Schema::table('subject_enrollments', function (Blueprint $table) {
            $table->dropIndex(['enrollment_id']);
            $table->dropIndex(['subject_offering_id']);
        });
        Schema::table('subject_offerings', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
            $table->dropIndex(['faculty_id']);
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex(['semester_id']);
            $table->dropIndex(['program_id']);
        });
    }
};
