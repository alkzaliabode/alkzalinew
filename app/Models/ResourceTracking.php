<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceTracking extends Model
{
    protected $fillable = [
        'date',
        'unit_id',
        'working_hours',
        'cleaning_materials',
        'water_consumption',
        'equipment_usage',
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // حساب الكفاءة الإجمالية
    public function getEfficiencyAttribute(): float
    {
        $completedTasks = GeneralCleaningTask::whereDate('date', $this->date)
                          ->where('unit_id', $this->unit_id)
                          ->where('status', 'مكتمل')
                          ->count();
        
        return $this->working_hours > 0 
            ? round($completedTasks / $this->working_hours, 2)
            : 0;
    }
}