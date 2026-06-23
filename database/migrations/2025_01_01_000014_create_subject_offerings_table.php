<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subject_offerings')) {
            Schema::create('subject_offerings', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('subject_id')->constrained('subjects');
                $table->foreignUuid('section_id')->constrained('sections');
                $table->foreignUuid('faculty_id')->nullable()->constrained('profiles');
                $table->text('room')->nullable();
                $table->text('schedule')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_offerings');
    }
};
