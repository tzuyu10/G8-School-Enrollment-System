<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->text('label')->notNullable();
                $table->integer('year_start')->notNullable();
                $table->integer('year_end')->notNullable();
                $table->boolean('is_active')->default(false);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
