<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SanitationFacilityTask;
use App\Models\GeneralCleaningTask;
use App\Models\ActualResult;

class RecalculateActualResults extends Command
{
    protected $signature = 'recalculate:actual-results';
    protected $description = 'Recalculate actual results for all units and dates based on completed tasks.';

    public function handle()
    {
        $this->info("🔄 بدء إعادة حساب النتائج الفعلية...");

        // جلب كل التواريخ والوحدات من المهام المكتملة
        $datesUnits = collect();

        $datesUnits = $datesUnits->merge(
            SanitationFacilityTask::where('status', 'مكتمل')
                ->get(['unit_id', 'date'])
        );

        $datesUnits = $datesUnits->merge(
            GeneralCleaningTask::where('status', 'مكتمل')
                ->get(['unit_id', 'date'])
        );

        // إزالة التكرار
        $unique = $datesUnits->unique(function ($item) {
            return $item->unit_id . '-' . $item->date;
        });

        $count = 0;

        foreach ($unique as $entry) {
            ActualResult::recalculateForUnitAndDate($entry->unit_id, $entry->date);
            $this->line("✅ أعيد حساب النتيجة للوحدة ID: {$entry->unit_id} - التاريخ: {$entry->date}");
            $count++;
        }

        $this->info("✅ تم تحديث {$count} نتيجة فعلية بنجاح.");
    }
}
