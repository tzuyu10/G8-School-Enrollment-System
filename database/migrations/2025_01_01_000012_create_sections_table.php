<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('semester_id')->constrained('semesters');
                $table->foreignUuid('program_id')->constrained('programs');
                $table->foreignUuid('year_level_id')->constrained('year_levels');
                $table->foreignUuid('adviser_id')->nullable()->constrained('profiles');
                $table->text('name')->notNullable();
                $table->integer('max_capacity')->default(40);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
