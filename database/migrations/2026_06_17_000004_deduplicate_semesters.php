<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $groups = DB::table('semesters')
            ->select('academic_year_id', 'label')
            ->groupBy('academic_year_id', 'label')
            ->get();

        foreach ($groups as $group) {
            $ids = DB::table('semesters')
                ->where('academic_year_id', $group->academic_year_id)
                ->where('label', $group->label)
                ->orderByDesc('is_active')
                ->orderBy('start_date')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            if (count($ids) <= 1) {
                continue;
            }

            $canonicalId = $ids[0];
            $duplicateIds = array_slice($ids, 1);

            DB::table('sections')
                ->whereIn('semester_id', $duplicateIds)
                ->update(['semester_id' => $canonicalId]);

            DB::table('enrollment_applications')
                ->whereIn('semester_id', $duplicateIds)
                ->update(['semester_id' => $canonicalId]);

            DB::table('semesters')
                ->whereIn('id', $duplicateIds)
                ->delete();
        }

        if (!$this->indexExists('semesters', 'semesters_academic_year_label_unique')) {
            Schema::table('semesters', function ($table) {
                $table->unique(['academic_year_id', 'label'], 'semesters_academic_year_label_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('semesters', 'semesters_academic_year_label_unique')) {
            Schema::table('semesters', function ($table) {
                $table->dropUnique('semesters_academic_year_label_unique');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn ($item) => ($item['name'] ?? null) === $index);
    }
};
