<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('personal_access_tokens')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN tokenable_id TYPE uuid USING tokenable_id::text::uuid');

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id CHAR(36) NOT NULL');

            return;
        }

        if ($driver === 'sqlite') {
            return;
        }

        Schema::table('personal_access_tokens', function ($table) {
            $table->uuid('tokenable_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('personal_access_tokens')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN tokenable_id TYPE bigint USING tokenable_id::text::bigint');

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id BIGINT UNSIGNED NOT NULL');

            return;
        }

        if ($driver === 'sqlite') {
            return;
        }

        Schema::table('personal_access_tokens', function ($table) {
            $table->unsignedBigInteger('tokenable_id')->change();
        });
    }
};
