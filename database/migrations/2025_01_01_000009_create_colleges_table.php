<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('colleges')) {
            Schema::create('colleges', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->text('code')->unique()->notNullable();
                $table->text('name')->notNullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
