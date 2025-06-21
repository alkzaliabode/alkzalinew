{{-- resources/views/filament/pages/partials/cards/image-reports.blade.php --}}

<a href="{{ \App\Filament\Pages\ProfessionalImageReports::getUrl() }}"
   class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl
          hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-105 group aspect-square"
   title="التقارير المصورة">

    {{-- أيقونة مميزة لتقرير الصور (Heroicon) --}}
    <x-heroicon-o-photo class="h-6 w-6 text-primary-500 group-hover:text-primary-600 transition" />

    {{-- عنوان البطاقة --}}
    <h3 class="text-lg font-bold mt-2 text-gray-900 dark:text-white">التقارير المصورة</h3>

    {{-- وصف البطاقة --}}
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 text-center">استعراض وإدارة التقارير المصورة</p>

    {{-- إحصائية إجمالية لعدد التقارير المصورة --}}
    @php
        // تأكد من استيراد الموديل إذا لم يكن مستوردًا بالفعل في الملف الرئيسي الذي يتضمن هذا الـ partial
        // وإلا قد تحتاج إلى استخدام \App\Models\TaskImageReport::count()
        $totalImageReports = \App\Models\TaskImageReport::count();
    @endphp

    <span class="text-xl font-extrabold text-primary-700 dark:text-primary-300 mt-2">{{ $totalImageReports }}</span>
</a>
