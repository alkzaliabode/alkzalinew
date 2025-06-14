<x-filament-panels::page>
    <div class="fi-dashboard-page px-6 py-8 max-w-7xl mx-auto">

        {{-- لوحة التحكم للشعبة الخدمية --}}
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">لوحة تحكم الشعبة الخدمية</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">نظرة عامة سريعة على البيانات والمهام والرسوم البيانية</p>
        </header>

        {{-- بطاقات إحصائية --}}
        @include('filament.pages.partials.dashboard-cards')

        {{-- الرسوم البيانية --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">
            @livewire(\App\Filament\Widgets\GilbertTriangleWidget::class)
            @livewire(\App\Filament\Widgets\SatisfactionPieChart::class)
        </div>

        <div class="mt-10">
            @livewire(\App\Filament\Widgets\SurveyStats::class)
        </div>

    </div>
</x-filament-panels::page>
