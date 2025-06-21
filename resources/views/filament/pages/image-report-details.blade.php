<div class="p-6 space-y-6 bg-gray-50 rounded-lg dark:bg-gray-900">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 md:mb-0">
            تقرير مصور - {{ $unitName }}
        </h2>
        <div class="flex flex-wrap gap-2">
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                {{ $record->date ? $record->date->format('Y-m-d') : 'بدون تاريخ' }}
            </span>
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $record->unit_type == 'cleaning' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                {{ $unitName }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">معلومات المهمة</h3>
            <div class="space-y-3 text-gray-600 dark:text-gray-400">
                <p><span class="font-medium text-gray-800 dark:text-gray-200">الموقع:</span> {{ $record->location ?? 'غير محدد' }}</p>
                <p><span class="font-medium text-gray-800 dark:text-gray-200">نوع المهمة:</span>
                    <span class="{{ $record->task_type == 'إدامة' ? 'text-blue-600 dark:text-blue-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                        {{ $record->task_type ?? 'غير محدد' }}
                    </span>
                </p>
                <p><span class="font-medium text-gray-800 dark:text-gray-200">الحالة:</span>
                    <span class="{{ $record->status == 'مكتمل' ? 'text-green-600 dark:text-green-400' : ($record->status == 'قيد التنفيذ' ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                        {{ $record->status ?? 'غير محدد' }}
                    </span>
                </p>
                @if($record->notes)
                <p><span class="font-medium text-gray-800 dark:text-gray-200">ملاحظات:</span> {{ $record->notes }}</p>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">صور قبل التنفيذ ({{ $record->before_images_count }})</h3>
            @if($record->before_images_count > 0)
                <div class="grid grid-cols-2 gap-4">
                    @foreach($record->before_images_urls as $image)
                        @if($image['exists'])
                        <div class="relative group">
                            <img src="{{ $image['url'] }}"
                                alt="صورة قبل التنفيذ"
                                class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600 shadow">
                            <a href="{{ $image['url'] }}" target="_blank"
                            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="bg-white p-2 rounded-full shadow-lg dark:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                        @else
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 dark:bg-red-900 dark:border-red-600 col-span-2">
                                <div class="flex items-center">
                                    <p class="ml-3 text-sm text-red-700 dark:text-red-200">
                                        صورة قبل التنفيذ غير موجودة في المسار المحدد: <span class="font-mono text-xs">{{ $image['path'] ?? 'مسار غير معروف' }}</span>
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 dark:bg-blue-900 dark:border-blue-600">
                    <div class="flex items-center">
                        <p class="ml-3 text-sm text-blue-700 dark:text-blue-200">
                            لا توجد صور قبل التنفيذ لهذا التقرير.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mt-6">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">صور بعد التنفيذ ({{ $record->after_images_count }})</h3>
        @if($record->after_images_count > 0)
            <div class="grid grid-cols-2 gap-4">
                @foreach($record->after_images_urls as $image)
                    @if($image['exists'])
                    <div class="relative group">
                        <img src="{{ $image['url'] }}"
                            alt="صورة بعد التنفيذ"
                            class="w-full h-48 object-cover rounded-lg border border-blue-300 dark:border-blue-600 shadow">
                        <a href="{{ $image['url'] }}" target="_blank"
                        class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="bg-white p-2 rounded-full shadow-lg dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </a>
                    </div>
                    @else
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 dark:bg-red-900 dark:border-red-600 col-span-2">
                            <div class="flex items-center">
                                <p class="ml-3 text-sm text-red-700 dark:text-red-200">
                                    صورة بعد التنفيذ غير موجودة في المسار المحدد: <span class="font-mono text-xs">{{ $image['path'] ?? 'مسار غير معروف' }}</span>
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 dark:bg-blue-900 dark:border-blue-600">
                <div class="flex items-center">
                    <p class="ml-3 text-sm text-blue-700 dark:text-blue-200">
                        لا توجد صور بعد التنفيذ لهذا التقرير.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>