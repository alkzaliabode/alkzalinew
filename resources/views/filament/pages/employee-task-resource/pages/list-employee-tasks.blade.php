<x-filament-panels::page>
    <div class="space-y-4">
        <h2 class="text-xl font-bold mb-4">قائمة مهام الموظفين</h2>
        <table class="min-w-full bg-white border border-gray-200 rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">الموظف</th>
                    <th class="px-4 py-2 border-b">المهمة</th>
                    <th class="px-4 py-2 border-b">الوحدة</th>
                    <th class="px-4 py-2 border-b">الحالة</th>
                    <th class="px-4 py-2 border-b">تاريخ التنفيذ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employeeTasks as $task)
                    <tr>
                        <td class="px-4 py-2 border-b">{{ $task->employee->name ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->task->task_type ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->task->unit->name ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->task->status ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->task->date ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>