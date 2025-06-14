<?php

namespace App\Observers;

use App\Models\SanitationFacilityTask;
use App\Models\ActualResult;
use App\Models\ResourceTracking;

class SanitationFacilityTaskObserver
{
    public function saved(SanitationFacilityTask $task): void
    {
        // تأكد من وجود الوحدة والتاريخ
        if (!$task->unit_id || !$task->date) {
            return;
        }

        // حساب عدد المهام المكتملة لهذا اليوم
        $completedTasks = SanitationFacilityTask::where('unit_id', $task->unit_id)
            ->where('date', $task->date)
            ->where('status', 'مكتمل')
            ->count();

        // حساب متوسط تقييم الموظفين لجميع المهام المكتملة في نفس اليوم
        $averageRating = \App\Models\EmployeeTask::whereHas('sanitationFacilityTask', function ($q) use ($task) {
                $q->where('unit_id', $task->unit_id)
                  ->where('date', $task->date)
                  ->where('status', 'مكتمل');
            })
            ->avg('employee_rating') ?? 0;

        $efficiencyScore = $averageRating > 0 ? round(($averageRating / 5) * 100) : 0;

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

    public function deleted(SanitationFacilityTask $task): void
    {
        $this->saved($task);
    }

    public function restored(SanitationFacilityTask $task): void
    {
        $this->saved($task);
    }

    public function forceDeleted(SanitationFacilityTask $task): void
    {
        $this->saved($task);
    }
}