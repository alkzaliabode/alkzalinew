<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyGeneralCleaningSummary extends Model
{
    protected $table = 'monthly_general_cleaning_summary';

    public $timestamps = false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];
}