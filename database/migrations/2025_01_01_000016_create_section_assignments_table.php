<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('section_assignments')) {
            Schema::create('section_assignments', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('enrollment_id')->unique()->constrained('enrollment_applications');
                $table->foreignUuid('section_id')->constrained('sections');
                $table->foreignUuid('assigned_by')->nullable()->constrained('profiles');
                $table->timestamp('assigned_at')->default(DB::raw('now()'));
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('section_assignments');
    }
};
