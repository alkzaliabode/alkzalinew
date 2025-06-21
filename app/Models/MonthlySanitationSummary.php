<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlySanitationSummary extends Model
{
    protected $table = 'monthly_sanitation_summary';

    // ✅ تم حذف 'public $timestamps = false;' للسماح لـ Laravel بالتعامل مع created_at و updated_at تلقائيًا
    //    إذا كانت هذه الأعمدة موجودة في جدول قاعدة البيانات الخاص بك (أضفتها في Migration).

    protected $primaryKey = 'id'; // يجب تحديد المفتاح الرئيسي
    public $incrementing = false; // لأنه نص وليس رقم تلقائي
    protected $keyType = 'string'; // نوع النص

    protected $guarded = []; // يسمح بتعبئة جميع الحقول بشكل جماعي

    // ✅ تم حذف دالة save() التي كانت ترجع false.
    //    هذا الموديل يمثل جدولاً فعليًا في قاعدة البيانات، وليس View للقراءة فقط.
    //    بإزالة هذه الدالة، يمكنك الآن استخدام MonthlySanitationSummary::create(), updateOrCreate(), إلخ.
}
