{{-- resources/views/filament/pages/professional-image-reports.blade.php --}}

{{-- هذا هو مكون الصفحة الأساسي في Filament. يجب أن يحيط بجميع محتوى الصفحة --}}
<x-filament-panels::page>
    {{-- هذا السطر هو الأهم! يقوم هذا السطر باستدعاء وعرض الجدول الذي قمت بتعريفه
        في الدالة `table()` داخل فئة ProfessionalImageReports.php --}}
    {{ $this->table }}

    {{-- يمكنك إضافة أي عناصر إضافية هنا إذا كنت تريدها أن تظهر أعلى أو أسفل الجدول
        على سبيل المثال:
        <h1 class="text-3xl font-bold text-gray-900 mb-6">التقارير المصورة</h1>
        <p class="text-gray-600 mb-8">استعرض التقارير المصورة مع خيارات التصفية.</p>
    --}}
</x-filament-panels::page>
