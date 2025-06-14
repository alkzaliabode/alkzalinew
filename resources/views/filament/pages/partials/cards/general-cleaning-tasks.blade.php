<a href="{{ \App\Filament\Resources\GeneralCleaningTaskResource::getUrl('index') }}"
   class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl
          hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-105 group"
   title="مهام النظافة العامة">
    <x-heroicon-o-sparkles class="h-6 w-6 text-primary-500 group-hover:text-primary-600 transition" />
    <h3 class="text-lg font-bold mt-2 text-gray-900 dark:text-white">مهام النظافة العامة</h3>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 text-center">متابعة مهام النظافة</p>
    @php $totalGeneralCleaningTasks = \App\Models\GeneralCleaningTask::count(); @endphp
    <span class="text-xl font-extrabold text-primary-700 dark:text-primary-300 mt-2">{{ $totalGeneralCleaningTasks }}</span>
</a>
