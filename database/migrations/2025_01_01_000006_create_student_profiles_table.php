<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_profiles')) {
            Schema::create('student_profiles', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('profile_id')->unique()->constrained('profiles');
                $table->text('student_number')->unique()->nullable();
                $table->text('student_type')->notNullable();
                $table->date('birthdate')->nullable();
                $table->text('gender')->nullable();
                $table->text('civil_status')->nullable();
                $table->text('nationality')->default('Filipino');
                $table->text('religion')->nullable();
                $table->text('contact_number')->nullable();
                $table->text('permanent_address')->nullable();
                $table->text('current_address')->nullable();
                $table->text('guardian_name')->nullable();
                $table->text('guardian_relation')->nullable();
                $table->text('guardian_contact')->nullable();
                $table->text('father_name')->nullable();
                $table->text('mother_name')->nullable();
                $table->text('previous_school')->nullable();
                $table->text('previous_program')->nullable();
                $table->timestamp('created_at')->default(DB::raw('now()'));
                $table->timestamp('updated_at')->default(DB::raw('now()'));
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
