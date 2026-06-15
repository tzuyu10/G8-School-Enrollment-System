<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('program_id')->nullable()->constrained('programs');
                $table->text('code')->unique()->notNullable();
                $table->text('title')->notNullable();
                $table->integer('units')->notNullable();
                $table->text('type')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
