<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollment_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollment_applications', 'tor_document_path')) {
                $table->text('tor_document_path')->nullable();
            }

            if (!Schema::hasColumn('enrollment_applications', 'prior_subject_grades_verified')) {
                $table->boolean('prior_subject_grades_verified')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollment_applications', function (Blueprint $table) {
            if (Schema::hasColumn('enrollment_applications', 'tor_document_path')) {
                $table->dropColumn('tor_document_path');
            }

            if (Schema::hasColumn('enrollment_applications', 'prior_subject_grades_verified')) {
                $table->dropColumn('prior_subject_grades_verified');
            }
        });
    }
};
