<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SanitationFacilityTask extends Model
{
    protected $fillable = [
        'date', 'shift', 'task_type', 'facility_name', 'details', 'status', 'notes',
        'related_goal_id', 'progress', 'result_value',
        'resources_used', 'verification_status', 'before_images', 'after_images','seats_count',
    'sinks_count',
    'mixers_count',
    'mirrors_count',
    'doors_count',
    'toilets_count',
    ];

    protected $casts = [
        'resources_used' => 'array',
        'before_images' => 'array',
        'after_images' => 'array',
    ];
  


    public function employeeTasks()
{
    return $this->hasMany(\App\Models\EmployeeTask::class, 'sanitation_facility_task_id');
}
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_task', 'sanitation_facility_task_id', 'employee_id')
            ->withPivot(['employee_rating', 'start_time', 'end_time', 'notes'])
            ->withTimestamps();
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