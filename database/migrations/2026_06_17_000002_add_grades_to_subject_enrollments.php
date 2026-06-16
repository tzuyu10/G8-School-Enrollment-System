<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_enrollments', 'grade')) {
                $table->decimal('grade', 3, 2)->nullable()->after('status_id');
            }

            if (!Schema::hasColumn('subject_enrollments', 'remarks')) {
                $table->text('remarks')->nullable()->after('grade');
            }

            if (!Schema::hasColumn('subject_enrollments', 'graded_by')) {
                $table->foreignUuid('graded_by')->nullable()->after('remarks')->constrained('profiles');
            }

            if (!Schema::hasColumn('subject_enrollments', 'graded_at')) {
                $table->timestamp('graded_at')->nullable()->after('graded_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subject_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('subject_enrollments', 'graded_by')) {
                $table->dropConstrainedForeignId('graded_by');
            }

            foreach (['graded_at', 'remarks', 'grade'] as $column) {
                if (Schema::hasColumn('subject_enrollments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
