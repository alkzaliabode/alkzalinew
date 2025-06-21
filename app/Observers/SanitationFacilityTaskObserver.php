<?php

namespace App\Observers;

use App\Models\SanitationFacilityTask;
use App\Models\ActualResult;
use App\Models\ResourceTracking;

class SanitationFacilityTaskObserver
{
    /**
     * Handle the SanitationFacilityTask "saved" event (created or updated).
     */
    public function saved(SanitationFacilityTask $task): void
    {
        // تأكد من وجود الوحدة والتاريخ
        if (!$task->unit_id || !$task->date) {
            // error_log("DEBUG: SanitationFacilityTaskObserver: Missing unit_id or date for task ID: {$task->id}\n");
            return;
        }

        // استدعاء دالة recalculateForUnitAndDate لتحديث ActualResult
        // هذه الدالة ستتولى حساب المهام المكتملة، البحث عن الهدف، وحساب مقاييس جيلبرت، وتحديث ActualResult.
        ActualResult::recalculateForUnitAndDate(
            $task->unit_id,
            $task->date
            // كما في Observer الآخر، لا نمرر $task->unit_goal_id هنا
            // لأن دالة recalculateForUnitAndDate تجده بنفسها بناءً على الوحدة والتاريخ
        );

        // === تحديث أو إنشاء سجل تتبع الموارد (بقي كما هو) ===
        $allTasks = SanitationFacilityTask::where('unit_id', $task->unit_id)
            ->where('date', $task->date)
            ->get();

        $cleaningMaterials = 0;
        $waterConsumption = 0;
        $equipmentUsage = 0;
        $workingHours = 0;

        foreach ($allTasks as $t) {
            $workingHours += (float) ($t->working_hours ?? 0);

            if (is_array($t->resources_used)) {
                foreach ($t->resources_used as $res) {
                    if (($res['name'] ?? '') === 'مواد تنظيف') {
                        $cleaningMaterials += (float) ($res['quantity'] ?? 0);
                    }
                    if (($res['name'] ?? '') === 'ماء' || ($res['name'] ?? '') === 'مياه') {
                        $waterConsumption += (float) ($res['quantity'] ?? 0);
                    }
                    if (($res['name'] ?? '') === 'معدات') {
                        $equipmentUsage += (float) ($res['quantity'] ?? 0);
                    }
                }
            }
        }

        ResourceTracking::updateOrCreate(
            [
                'unit_id' => $task->unit_id,
                'date'    => $task->date,
            ],
            [
                'working_hours'      => $workingHours,
                'cleaning_materials' => $cleaningMaterials,
                'water_consumption'  => $waterConsumption,
                'equipment_usage'    => $equipmentUsage,
                'notes'              => 'تم التحديث تلقائياً من مهام المنشآت الصحية',
            ]
        );
    }

    /**
     * Handle the SanitationFacilityTask "deleted" event.
     */
    public function deleted(SanitationFacilityTask $task): void
    {
        // إعادة حساب النتائج بعد الحذف
        ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
    }

    /**
     * Handle the SanitationFacilityTask "restored" event.
     */
    public function restored(SanitationFacilityTask $task): void
    {
        ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
    }

    /**
     * Handle the SanitationFacilityTask "forceDeleted" event.
     */
    public function forceDeleted(SanitationFacilityTask $task): void
    {
        ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
    }
}
