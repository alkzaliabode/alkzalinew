<a href="{{ url('/admin/resource-report') }}"
   class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl
          hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-105 group aspect-square"
   title="تقرير الموارد">

    <x-heroicon-o-archive-box class="h-8 w-8 text-primary-500 group-hover:text-primary-600 transition" />

    <h3 class="text-lg font-bold mt-2 text-gray-900 dark:text-white">تتبع الموارد</h3>

    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 text-center">إدارة وتتبع الموارد المتاحة</p>

    @php
        use App\Models\SanitationFacilityTask;
        use App\Models\GeneralCleaningTask;

        $sanitationCount = SanitationFacilityTask::get()->sum(function($task) {
            return is_array($task->resources_used) ? count($task->resources_used) : 0;
        });

        $generalCleaningCount = GeneralCleaningTask::get()->sum(function($task) {
            return is_array($task->resources_used) ? count($task->resources_used) : 0;
        });

        $totalResources = $sanitationCount + $generalCleaningCount;
    @endphp

    @if ($totalResources > 0)
        <span class="text-xl font-extrabold text-primary-700 dark:text-primary-300 mt-2">
            {{ $totalResources }}
        </span>
    @else
        <span class="text-xl font-extrabold text-red-500 dark:text-red-400 mt-2">
            لا توجد موارد
        </span>
    @endif

</a>
