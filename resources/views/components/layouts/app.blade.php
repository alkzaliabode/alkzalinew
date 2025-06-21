<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data x-bind:class="{'dark': localStorage.getItem('theme') === 'dark'}">
<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    {{--
        |--------------------------------------------------------------------------
        | إخفاء العناصر عند التحميل (Alpine.js)
        |--------------------------------------------------------------------------
        |
        | تمنع هذه القاعدة ظهور العناصر التي تحتوي على توجيه `x-cloak` قبل أن
        | يتم تهيئة Alpine.js بالكامل، مما يمنع "وميض" المحتوى غير المهيأ.
    --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{--
        |--------------------------------------------------------------------------
        | ملفات التنسيق (CSS)
        |--------------------------------------------------------------------------
        |
        | هنا يتم استدعاء خطوط Google (خط Cairo مثالي للغة العربية)،
        | بالإضافة إلى أنماط Filament الأساسية وأنماط التطبيق المخصصة.
    --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    @filamentStyles {{-- يقوم Filament بحقن أنماطه الأساسية هنا --}}
    @vite('resources/css/filament/admin/theme.css') {{-- استدعاء الثيم المخصص لـ Filament --}}
    @vite('resources/css/app.css') {{-- استدعاء ملف CSS العام لتطبيقك --}}
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white transition duration-300 ease-in-out antialiased">

    {{--
        |--------------------------------------------------------------------------
        | شريط الرأس (Header)
        |--------------------------------------------------------------------------
        |
        | يمثل شريط التنقل العلوي الذي يحتوي على عنوان لوحة التحكم وروابط سريعة.
        | يتميز بكونه "لزجاً" (sticky) في الأعلى ويدعم الوضع الليلي.
    --}}
    <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-50">
        <div class="mx-auto px-4 py-4 flex justify-between items-center max-w-7xl">
            <h1 class="text-xl font-bold text-indigo-600 dark:text-indigo-300">لوحة التحكم</h1>

            {{-- روابط التنقل في شريط الرأس --}}
            <nav class="space-x-4 rtl:space-x-reverse text-sm">
                {{-- ملاحظة: هذه الروابط هي عناصر نائب (placeholders) ويجب استبدالها بمسارات حقيقية. --}}
                <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">الرئيسية</a>
                <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">الإعدادات</a>
                <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">تسجيل الخروج</a>
            </nav>
        </div>
    </header>
     
    {{--
        |--------------------------------------------------------------------------
        | محتوى الصفحة الرئيسي (Main Content)
        |--------------------------------------------------------------------------
        |
        | هذا القسم هو الحاوية الرئيسية للمحتوى الخاص بكل صفحة.
        | يستخدم `{{ $slot }}` لحقن المحتوى الخاص بالصفحات الفرعية.
        | `animate-fade-in` يفترض وجود أنماط CSS مخصصة لتأثير ظهور تدريجي.
    --}}
    <main class="p-6 animate-fade-in">
        {{ $slot }}
    </main>

    {{--
        |--------------------------------------------------------------------------
        | الإشعارات (Notifications)
        |--------------------------------------------------------------------------
        |
        | مكون Livewire المخصص لعرض الإشعارات المنبثقة للمستخدم.
    --}}
    @livewire('notifications')

    {{--
        |--------------------------------------------------------------------------
        | ملفات السكربت (JavaScript)
        |--------------------------------------------------------------------------
        |
        | هنا يتم استدعاء سكربتات Filament الأساسية وسكربتات JavaScript الخاصة بتطبيقك.
    --}}
    @filamentScripts {{-- يقوم Filament بحقن سكربتاته الأساسية هنا --}}
    @vite('resources/js/app.js') {{-- استدعاء ملف JavaScript العام لتطبيقك --}}
</body>
</html>
