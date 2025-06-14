<?php

namespace App\Observers;

use App\Models\GeneralCleaningTask;
use App\Models\ActualResult;
use App\Models\ResourceTracking;

class GeneralCleaningTaskObserver
{
    /**
     * عند إنشاء أو تحديث مهمة نظافة عامة
     */
    public function saved(GeneralCleaningTask $task): void
    {
        // تأكد من وجود الوحدة والتاريخ
        if (!$task->unit_id || !$task->date) {
            return;
        }

        // حساب عدد المهام المكتملة للوحدة في نفس اليوم
        $completedTasks = GeneralCleaningTask::where('unit_id', $task->unit_id)
            ->where('date', $task->date)
            ->where('status', 'مكتمل')
            ->count();

        // حساب متوسط تقييم الموظفين لجميع المهام المكتملة في نفس اليوم
        $averageRating = \App\Models\EmployeeTask::whereHas('generalCleaningTask', function ($q) use ($task) {
                $q->where('unit_id', $task->unit_id)
                  ->where('date', $task->date)
                  ->where('status', 'مكتمل');
            })
            ->avg('employee_rating') ?? 0;

        // حساب درجة الكفاءة (مثال: نسبة التقييم إلى 100)
        $efficiencyScore = $averageRating > 0 ? round(($averageRating / 5) * 100) : 0;

        // تحديث أو إنشاء سجل النتائج الفعلية
        ActualResult::updateOrCreate(
            [
                'unit_id' => $task->unit_id,
                'date'    => $task->date,
            ],
            [
                'completed_tasks'  => $completedTasks,
                'quality_rating'   => round($averageRating),
                'efficiency_score' => $efficiencyScore,
            ]
        );

        // === تحديث أو إنشاء سجل تتبع الموارد ===
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
     * عند حذف المهمة
     */
    public function deleted(GeneralCleaningTask $task): void
    {
        // إعادة حساب النتائج بعد الحذف
        $this->saved($task);
    }

    public function restored(GeneralCleaningTask $task): void
    {
        $this->saved($task);
    }

    public function forceDeleted(GeneralCleaningTask $task): void
    {
        $this->saved($task);
    }
}