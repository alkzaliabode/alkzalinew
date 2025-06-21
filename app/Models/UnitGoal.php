<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitGoal extends Model
{
    protected $fillable = [
        'department_goal_id',
        'unit_id',
        'unit_name',
        'goal_text',
        'date',
        'target_tasks', // ✅ إضافة هنا
    ];

    /**
     * نسبة تحقق الهدف (من جميع المهام المرتبطة)
     */
    public function getProgressPercentageAttribute()
    {
        // مجموع المهام المكتملة من كلا النوعين (نظافة عامة وصحية)
        $sanitationCompleted = $this->sanitationFacilityTasks()->where('status', 'مكتمل')->count();
        $cleaningCompleted = $this->generalCleaningTasks()->where('status', 'مكتمل')->count();
        $completedTasks = $sanitationCompleted + $cleaningCompleted;

        // المهام المستهدفة تأتي الآن من عمود 'target_tasks'
        $totalTarget = $this->target_tasks;

        if ($totalTarget === 0) {
            return 0;
        }

        // حساب نسبة الإنجاز
        return round(($completedTasks / $totalTarget) * 100, 2);
    }

    /**
     * علاقة بـ department_goal
     */
    public function departmentGoal(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DepartmentGoal::class, 'department_goal_id');
    }

    /**
     * علاقة بـ الوحدة (Unit)
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_id');
    }

    /**
     * علاقة بالمهام الصحية
     */
    public function sanitationFacilityTasks()
    {
        return $this->hasMany(\App\Models\SanitationFacilityTask::class, 'related_goal_id');
    }

    /**
     * علاقة بمهام النظافة العامة
     */
    public function generalCleaningTasks()
    {
        return $this->hasMany(\App\Models\GeneralCleaningTask::class, 'related_goal_id');
    }
}
