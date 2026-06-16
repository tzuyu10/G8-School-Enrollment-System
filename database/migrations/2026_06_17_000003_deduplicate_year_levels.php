<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $groups = DB::table('year_levels')
            ->select('label', DB::raw('MIN(sort_order) as sort_order'))
            ->groupBy('label')
            ->get();

        foreach ($groups as $group) {
            $ids = DB::table('year_levels')
                ->where('label', $group->label)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            if (count($ids) <= 1) {
                continue;
            }

            $canonicalId = $ids[0];
            $duplicateIds = array_slice($ids, 1);

            DB::table('sections')
                ->whereIn('year_level_id', $duplicateIds)
                ->update(['year_level_id' => $canonicalId]);

            DB::table('enrollment_applications')
                ->whereIn('year_level_id', $duplicateIds)
                ->update(['year_level_id' => $canonicalId]);

            DB::table('year_levels')
                ->whereIn('id', $duplicateIds)
                ->delete();

            DB::table('year_levels')
                ->where('id', $canonicalId)
                ->update(['sort_order' => $group->sort_order]);
        }

        if (!$this->indexExists('year_levels', 'year_levels_label_unique')) {
            Schema::table('year_levels', function ($table) {
                $table->unique('label', 'year_levels_label_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('year_levels', 'year_levels_label_unique')) {
            Schema::table('year_levels', function ($table) {
                $table->dropUnique('year_levels_label_unique');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn ($item) => ($item['name'] ?? null) === $index);
    }
};
