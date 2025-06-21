<?php

namespace App\Observers;

use App\Models\GeneralCleaningTask;
use App\Models\ActualResult;
use App\Models\ResourceTracking; // تأكد من استيراد ResourceTracking إذا لم يكن موجوداً

class GeneralCleaningTaskObserver
{
    /**
     * Handle the GeneralCleaningTask "saved" event (created or updated).
     */
    public function saved(GeneralCleaningTask $task): void
    {
        // تأكد من وجود الوحدة والتاريخ
        if (!$task->unit_id || !$task->date) {
            // error_log("DEBUG: GeneralCleaningTaskObserver: Missing unit_id or date for task ID: {$task->id}\n");
            return;
        }

        // استدعاء دالة recalculateForUnitAndDate لتحديث ActualResult
        // هذه الدالة ستتولى حساب المهام المكتملة، البحث عن الهدف، وحساب مقاييس جيلبرت، وتحديث ActualResult.
        // نمرر الـ ID الخاص بالمهمة (task->id) كـ relatedGoalId إذا كان هناك حاجة للربط بهدف محدد،
        // ولكن الأهم هو unit_id و date.
        // في هذا السياق، قد لا يكون هناك 'هدف' مباشر للمهمة نفسها، ولكن المهمة مرتبطة بوحدة في تاريخ معين.
        // دالة recalculateForUnitAndDate ستبحث عن UnitGoal المناسب بناءً على unit_id والتاريخ.
        ActualResult::recalculateForUnitAndDate(
            $task->unit_id,
            $task->date
            // لا نمرر $task->unit_goal_id هنا، لأن recalculateForUnitAndDate تجده بنفسها
            // إذا كنت تريد ربطها بهدف محدد للمهمة نفسها (إذا كان للمهمة عمود unit_goal_id)، يمكن تمريره هنا
            // $task->unit_goal_id // قم بإلغاء التعليق إذا كانت مهام التنظيف ترتبط مباشرة بهدف وحدة محدد
        );

        // === تحديث أو إنشاء سجل تتبع الموارد (بقي كما هو) ===
        // هذا الجزء منفصل عن ActualResult ويمكن أن يبقى هنا أو ينقل إلى Observer خاص بـ ResourceTracking
        $allTasks = GeneralCleaningTask::where('unit_id', $task->unit_id)
            ->where('date', $task->date)
            ->get();

        $cleaningMaterials = 0;
        $waterConsumption = 0;
        $equipmentUsage = 0;

        foreach ($allTasks as $t) {
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
                'working_hours'      => 0, // يمكنك جمع ساعات العمل إذا كانت موجودة في المهام
                'cleaning_materials' => $cleaningMaterials,
                'water_consumption'  => $waterConsumption,
                'equipment_usage'    => $equipmentUsage,
                'notes'              => 'تم التحديث تلقائياً من المهام',
            ]
        );
    }

    /**
     * Handle the GeneralCleaningTask "deleted" event.
     */
    public function deleted(GeneralCleaningTask $task): void
    {
        // إعادة حساب النتائج بعد الحذف
        // يفضل استدعاء recalculateForUnitAndDate مباشرة
        ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
    }

    /**
     * Handle the GeneralCleaningTask "restored" event.
     */
    public function restored(GeneralCleaningTask $task): void
    {
        ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
    }

    /**
     * Handle the GeneralCleaningTask "forceDeleted" event.
     */
    public function forceDeleted(GeneralCleaningTask $task): void
    {
        ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
    }
}
