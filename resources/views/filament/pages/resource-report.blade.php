<x-filament::page>
    <h2 class="text-lg font-bold mb-4">تقرير الموارد المستخدمة في المهام</h2>

    <table class="w-full table-auto border-collapse text-right text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">التاريخ</th>
                <th class="border p-2">الوحدة</th>
                <th class="border p-2">نوع المهمة</th>
                <th class="border p-2">المورد</th>
                <th class="border p-2">الكمية</th>
                <th class="border p-2">وحدة المورد</th> <!-- 👈 جديد -->

            </tr>
        </thead>
        <tbody>
            @foreach ($this->resources as $res)
                <tr>
                    <td class="border p-2">{{ $res['date'] }}</td>
                    <td class="border p-2">{{ $res['unit'] }}</td>
                    <td class="border p-2">{{ $res['task_type'] }}</td>
                    <td class="border p-2">{{ $res['item'] }}</td>
                    <td class="border p-2">{{ $res['quantity'] }}</td>
                    <td class="border p-2">{{ $res['resource_unit'] }}</td> <!-- 👈 جديد -->

                </tr>
            @endforeach
        </tbody>
    </table>
</x-filament::page>
