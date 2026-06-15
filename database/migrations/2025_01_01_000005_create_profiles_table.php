<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('role_id')->constrained('roles');
                $table->foreignUuid('status_id')->constrained('profile_statuses');
                $table->text('full_name')->notNullable();
                $table->text('email')->unique()->notNullable();
                $table->text('password')->notNullable();
                $table->rememberToken();
                $table->timestamp('created_at')->default(DB::raw('now()'));
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
