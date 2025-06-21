<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    public function resourceTrackings(): HasMany
    {
        return $this->hasMany(ResourceTracking::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(UnitGoal::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(GeneralCleaningTask::class);
    }

    // **هنا تضيف علاقة نتائج الأداء (ActualResult)**
    public function actualResults(): HasMany
    {
        return $this->hasMany(ActualResult::class);
    }
}
