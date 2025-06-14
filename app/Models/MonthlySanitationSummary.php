<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlySanitationSummary extends Model
{
    protected $table = 'monthly_sanitation_summary';

    public $timestamps = false;

    protected $primaryKey = 'id'; // لازم نحدد المفتاح الرئيسي
    public $incrementing = false; // لأنه نص وليس رقم تلقائي
    protected $keyType = 'string'; // نوع النص

    protected $guarded = [];

    public function save(array $options = [])
    {
        return false; // لأنه View للقراءة فقط
    }
}