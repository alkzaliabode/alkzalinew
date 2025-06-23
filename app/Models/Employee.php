<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Authenticatable
{
    protected $fillable = [
         'name',
    'email',
    'password',
    'job_title',
    'unit_id',
    'role',
    'is_active',
    'employee_number',
        
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * جميع مهام النظافة العامة المرتبطة بالموظف
     */
    public function generalCleaningTasks(): BelongsToMany
    {
        return $this->belongsToMany(
            GeneralCleaningTask::class,
            'employee_task',
            'employee_id',
            'general_cleaning_task_id'
        )
        ->withPivot([
            'employee_rating',
            'start_time',
            'end_time',
            'notes'
        ]);
    }

    /**
     * جميع مهام المنشآت الصحية المرتبطة بالموظف
     */
    public function sanitationFacilityTasks(): BelongsToMany
    {
        return $this->belongsToMany(
            SanitationFacilityTask::class,
            'employee_task',
            'employee_id',
            'sanitation_facility_task_id'
        )
        ->withPivot([
            'employee_rating',
            'start_time',
            'end_time',
            'notes'
        ]);
    }

    /**
     * جميع المهام (نظافة عامة + منشآت صحية)
     */
    public function allTasks()
    {
        // دمج المهام من العلاقتين في Collection واحدة
        return $this->generalCleaningTasks->merge($this->sanitationFacilityTasks);
    }

    /**
     * علاقة الوحدة
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * متوسط تقييم الموظف من جميع المهام
     */
    public function getAverageRatingAttribute(): float
    {
        $ratings = collect();

        foreach ($this->generalCleaningTasks as $task) {
            if ($task->pivot->employee_rating !== null) {
                $ratings->push($task->pivot->employee_rating);
            }
        }
        foreach ($this->sanitationFacilityTasks as $task) {
            if ($task->pivot->employee_rating !== null) {
                $ratings->push($task->pivot->employee_rating);
            }
        }

        return $ratings->count() > 0 ? round($ratings->avg(), 2) : 0;
    }
}