{{-- هذا يمثل ملف Blade الخاص بلوحة المعلومات الرئيسية، على سبيل المثال:
     resources/views/filament/pages/dashboard.blade.php أو أي صفحة تجمع هذه البطاقات --}}

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

    {{-- بطاقة الموظفين --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.employees')
    </div>

    {{-- بطاقة مهام الموظفين --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.employee-tasks')
    </div>

    {{-- بطاقة مهام النظافة العامة --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.general-cleaning-tasks')
    </div>

    {{-- بطاقة مهام المنشآت الصحية --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.sanitation-facility-tasks')
    </div>

    {{-- بطاقة النتائج الفعلية --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.actual-results')
    </div>

    {{-- بطاقة الحالة اليومية --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.daily-status')
    </div>

    {{-- بطاقة الاستبيان --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.survey')
    </div>

    {{-- بطاقة هدف الوحدة --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.unit-goal')
    </div>

    {{-- بطاقة الموارد --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.resources')
    </div>

    {{-- بطاقة النظافة الشهرية --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.monthly-cleaning')
    </div>

    {{-- بطاقة المنشآت الشهرية --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.monthly-facilities')
    </div>

    {{-- إضافة بطاقة التقارير المصورة هنا --}}
    <div class="square-card p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300">
        @include('filament.pages.partials.cards.image-reports')
    </div>

</div>
