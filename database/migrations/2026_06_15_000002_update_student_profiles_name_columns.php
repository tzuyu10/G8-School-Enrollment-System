<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Replace father_name, mother_name with split fields
            $table->text('father_first_name')->nullable()->after('mother_name');
            $table->text('father_middle_name')->nullable()->after('father_first_name');
            $table->text('father_last_name')->nullable()->after('father_middle_name');
            $table->text('father_suffix')->nullable()->after('father_last_name');

            $table->text('mother_first_name')->nullable()->after('father_suffix');
            $table->text('mother_middle_name')->nullable()->after('mother_first_name');
            $table->text('mother_last_name')->nullable()->after('mother_middle_name');
            $table->text('mother_suffix')->nullable()->after('mother_last_name');

            // Guardian name split
            $table->text('guardian_first_name')->nullable()->after('mother_suffix');
            $table->text('guardian_middle_name')->nullable()->after('guardian_first_name');
            $table->text('guardian_last_name')->nullable()->after('guardian_middle_name');
            $table->text('guardian_suffix')->nullable()->after('guardian_last_name');
        });

        // Migrate existing single-field names to first/last
        DB::statement("
            UPDATE student_profiles SET
                father_first_name = split_part(COALESCE(father_name,''), ' ', 1),
                father_last_name  = CASE WHEN position(' ' IN COALESCE(father_name,'')) > 0
                    THEN trim(substring(father_name FROM position(' ' IN father_name))) ELSE '' END,
                mother_first_name = split_part(COALESCE(mother_name,''), ' ', 1),
                mother_last_name  = CASE WHEN position(' ' IN COALESCE(mother_name,'')) > 0
                    THEN trim(substring(mother_name FROM position(' ' IN mother_name))) ELSE '' END,
                guardian_first_name = split_part(COALESCE(guardian_name,''), ' ', 1),
                guardian_last_name  = CASE WHEN position(' ' IN COALESCE(guardian_name,'')) > 0
                    THEN trim(substring(guardian_name FROM position(' ' IN guardian_name))) ELSE '' END
        ");

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mother_name', 'guardian_name']);
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->text('father_name')->nullable();
            $table->text('mother_name')->nullable();
            $table->text('guardian_name')->nullable();
        });

        DB::statement("
            UPDATE student_profiles SET
                father_name   = concat_ws(' ', father_first_name, father_last_name),
                mother_name   = concat_ws(' ', mother_first_name, mother_last_name),
                guardian_name = concat_ws(' ', guardian_first_name, guardian_last_name)
        ");

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'father_first_name','father_middle_name','father_last_name','father_suffix',
                'mother_first_name','mother_middle_name','mother_last_name','mother_suffix',
                'guardian_first_name','guardian_middle_name','guardian_last_name','guardian_suffix',
            ]);
        });
    }
};
