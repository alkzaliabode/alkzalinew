<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTask extends Model
{
    protected $table = 'employee_task';
    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'general_cleaning_task_id',
        'sanitation_facility_task_id',
        'employee_rating',
        'start_time',
        'end_time',
        'notes',
    ];

    // علاقة الموظف
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // علاقة مهمة النظافة العامة
    public function generalCleaningTask(): BelongsTo
    {
        return $this->belongsTo(GeneralCleaningTask::class, 'general_cleaning_task_id');
    }

    // علاقة مهمة المنشآت الصحية
    public function sanitationFacilityTask(): BelongsTo
    {
        return $this->belongsTo(SanitationFacilityTask::class, 'sanitation_facility_task_id');
    }

    // label لنوع المهمة
    public function getTaskTypeLabelAttribute()
    {
        if ($this->generalCleaningTask) {
            return $this->generalCleaningTask->task_type;
        }
        if ($this->sanitationFacilityTask) {
            return $this->sanitationFacilityTask->task_type;
        }
        return null;
    }

    // label للموقع أو المرفق الصحي
    public function getLocationLabelAttribute()
    {
        if ($this->generalCleaningTask) {
            return $this->generalCleaningTask->location;
        }
        if ($this->sanitationFacilityTask) {
            return $this->sanitationFacilityTask->facility_name;
        }
        return null;
    }

    // label للتاريخ
    public function getDateLabelAttribute()
    {
        if ($this->generalCleaningTask) {
            return $this->generalCleaningTask->date;
        }
        if ($this->sanitationFacilityTask) {
            return $this->sanitationFacilityTask->date;
        }
        return null;
    }
}