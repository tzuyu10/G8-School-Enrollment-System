<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->text('first_name')->nullable()->after('id');
            $table->text('middle_name')->nullable()->after('first_name');
            $table->text('last_name')->nullable()->after('middle_name');
            $table->text('suffix')->nullable()->after('last_name');
        });

        // Migrate existing full_name → first_name + last_name
        DB::statement("
            UPDATE profiles
            SET first_name = split_part(full_name, ' ', 1),
                last_name  = CASE
                    WHEN array_length(string_to_array(trim(full_name), ' '), 1) > 1
                    THEN trim(substring(full_name FROM position(' ' IN full_name)))
                    ELSE ''
                END
            WHERE full_name IS NOT NULL
        ");

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->text('full_name')->nullable();
        });

        DB::statement("
            UPDATE profiles
            SET full_name = concat_ws(' ', first_name, middle_name, last_name, suffix)
        ");

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'suffix']);
        });
    }
};
