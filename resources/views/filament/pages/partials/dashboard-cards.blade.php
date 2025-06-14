<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    {{-- 
        |-----------------------------------------------------------
        | بطاقات لوحة القيادة - كل بطاقة مستوردة من ملف جزئي مستقل
        |-----------------------------------------------------------
        | يسهل هذا الفصل من صيانة كل بطاقة على حدة وتوسعة اللوحة.
    --}}

    {{-- بطاقة الموظفين --}}
    @include('filament.pages.partials.cards.employees')

    {{-- بطاقة مهام الموظفين --}}
    @include('filament.pages.partials.cards.employee-tasks')

    {{-- بطاقة النظافة العامة --}}
    @include('filament.pages.partials.cards.general-cleaning-tasks')

    {{-- بطاقة المنشآت الصحية --}}
    @include('filament.pages.partials.cards.sanitation-facility-tasks')

    {{-- بطاقة النتائج الفعلية --}}
    @include('filament.pages.partials.cards.actual-results')

    {{-- بطاقة المواقف اليومية --}}
    @include('filament.pages.partials.cards.daily-status')

    {{-- بطاقة استبيانات الزوار --}}
    @include('filament.pages.partials.cards.survey')

    {{-- بطاقة أهداف الوحدة --}}
    @include('filament.pages.partials.cards.unit-goal')

    {{-- بطاقة تتبع الموارد --}}
    @include('filament.pages.partials.cards.resources')

    {{-- بطاقة تقارير النظافة الشهرية --}}
    @include('filament.pages.partials.cards.monthly-cleaning')

    {{-- بطاقة تقارير المنشآت الصحية الشهرية --}}
    @include('filament.pages.partials.cards.monthly-facilities')
</div>
