<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subject_enrollments')) {
            Schema::create('subject_enrollments', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('enrollment_id')->constrained('enrollment_applications');
                $table->foreignUuid('subject_offering_id')->constrained('subject_offerings');
                $table->foreignUuid('status_id')->constrained('subject_enrollment_statuses');
                $table->unique(['enrollment_id', 'subject_offering_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_enrollments');
    }
};
