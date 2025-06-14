<?php

namespace App\Observers;

use App\Models\EmployeeTask;
use App\Models\GeneralCleaningTask;
use App\Models\SanitationFacilityTask;

/**
 * مراقب لمهام الموظفين
 */
class EmployeeTaskObserver
{
    public function saved(EmployeeTask $employeeTask)
    {
        // إذا كانت المهمة مرتبطة بمهمة نظافة عامة
        if ($employeeTask->general_cleaning_task_id) {
            $task = GeneralCleaningTask::find($employeeTask->general_cleaning_task_id);
            if ($task) {
                // استدعي Observer الخاص بمهمة النظافة العامة
                (new \App\Observers\GeneralCleaningTaskObserver)->saved($task);
            }
        }

        // إذا كانت المهمة مرتبطة بمهمة منشأة صحية
        if ($employeeTask->sanitation_facility_task_id) {
            $task = SanitationFacilityTask::find($employeeTask->sanitation_facility_task_id);
            if ($task) {
                // استدعي Observer الخاص بمهمة المنشآت الصحية
                (new \App\Observers\SanitationFacilityTaskObserver)->saved($task);
            }
        }
    }

    public function deleted(EmployeeTask $employeeTask)
    {
        $this->saved($employeeTask);
    }
}