<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepartmentGoal extends Model
{
    protected $fillable = [
        'main_goal_id',
        'goal_text',
    ];

    /**
     * علاقة بـ main_goal
     */
    public function mainGoal(): BelongsTo
    {
        return $this->belongsTo(\App\Models\MainGoal::class, 'main_goal_id');
    }

    /**
     * علاقة بـ unit_goals
     */
    public function unitGoals(): HasMany
    {
        return $this->hasMany(\App\Models\UnitGoal::class, 'department_goal_id');
    }
}