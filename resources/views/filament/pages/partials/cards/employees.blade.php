<a href="{{ \App\Filament\Resources\EmployeeResource::getUrl('index') }}"
   class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl
          hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-105 group aspect-square"
   title="إدارة بيانات الموظفين">

    <x-heroicon-o-users class="h-6 w-6 text-primary-500 group-hover:text-primary-600 transition" />

    <h3 class="text-lg font-bold mt-2 text-gray-900 dark:text-white">الموظفون</h3>

    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 text-center">إدارة بيانات الموظفين</p>

    @php $totalEmployees = \App\Models\Employee::count(); @endphp

    <span class="text-xl font-extrabold text-primary-700 dark:text-primary-300 mt-2">{{ $totalEmployees }}</span>
</a>
