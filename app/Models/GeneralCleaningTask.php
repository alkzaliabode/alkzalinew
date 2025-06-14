<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralCleaningTask extends Model
{
    protected $fillable = [
       'date', 'shift', 'task_type', 'location', 'quantity', 'status', 'notes',
        'responsible_persons', 'related_goal_id', 'progress', 'result_value',
        'resources_used', 'verification_status', 'before_images', 'after_images', 'unit_id',
        'working_hours', // ✅ أضف هذا السطر هنا
        // ✅ الأعمدة الجديدة
        'mats_count',
        'pillows_count',
        'fans_count',
        'windows_count',
        'carpets_count',
        'blankets_count',
        'beds_count',
        'beneficiaries_count',
        'filled_trams_count',
        'carpets_laid_count',
        'large_containers_count',
        'small_containers_count',
        'maintenance_details',
    ];

    protected $casts = [
        'resources_used' => 'array',
        'before_images' => 'array',
        'after_images' => 'array',
    ];

    /**
     * علاقة المهام المنفذة (جدول وسيط employee_task)
     */
    public function employeeTasks(): HasMany
    {
        return $this->hasMany(EmployeeTask::class, 'general_cleaning_task_id');
    }

    /**
     * علاقة الموظفين (Pivot: employee_task)
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'employee_task',
            'general_cleaning_task_id',
            'employee_id'
        )->withPivot([
            'employee_rating',
            'start_time',
            'end_time',
            'notes'
        ]);
    }

    /**
     * علاقة الوحدة
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * علاقة الهدف المرتبط
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(UnitGoal::class, 'related_goal_id');
    }
}
