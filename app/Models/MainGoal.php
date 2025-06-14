<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainGoal extends Model
{
    protected $fillable = [
        'goal_text',
    ];

    /**
     * علاقة بـ department_goals
     */
    public function departmentGoals(): HasMany
    {
        return $this->hasMany(\App\Models\DepartmentGoal::class, 'main_goal_id');
    }
}