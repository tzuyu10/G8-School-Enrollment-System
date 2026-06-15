<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('year_levels')) {
            Schema::create('year_levels', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->text('label')->notNullable();
                $table->integer('sort_order')->notNullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('year_levels');
    }
};
