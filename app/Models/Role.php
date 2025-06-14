<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // إذا كنت تستخدم team_id وتريد علاقة مع الفريق
    // يمكنك تعديل أو إضافة علاقات هنا مستقبلاً

    protected $fillable = [
        'name',
        'guard_name',
        'team_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'team_id' => 'integer',
    ];
    public function setTeamIdAttribute($value)
{
    $this->attributes['team_id'] = is_array($value) ? null : $value;
}
}
// يمكنك إضافة أي خصائص أو علاقات إضافية تحتاجها هنا