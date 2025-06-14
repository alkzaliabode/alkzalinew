<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActualResult extends Model
{
    protected $fillable = [
        'date',
        'completed_tasks',
        'quality_rating',
        'efficiency_score',
        'unit_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // حساب نسبة الإنجاز
    public function getCompletionRateAttribute()
    {
        $goal = UnitGoal::where('date', $this->date)
                      ->where('unit_id', $this->unit_id)
                      ->first();
        
        return $goal ? round(($this->completed_tasks / $goal->target_tasks) * 100, 2) : 0;
    }
}