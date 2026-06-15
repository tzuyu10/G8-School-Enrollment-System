<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('enrollment_applications')) {
            Schema::create('enrollment_applications', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('student_id')->constrained('profiles');
                $table->foreignUuid('semester_id')->constrained('semesters');
                $table->foreignUuid('program_id')->constrained('programs');
                $table->foreignUuid('year_level_id')->constrained('year_levels');
                $table->text('student_type')->notNullable();
                $table->foreignUuid('status_id')->constrained('application_statuses');
                $table->timestamp('submitted_at')->default(DB::raw('now()'));
                $table->foreignUuid('reviewed_by')->nullable()->constrained('profiles');
                $table->timestamp('reviewed_at')->nullable();
                $table->text('remarks')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_applications');
    }
};
