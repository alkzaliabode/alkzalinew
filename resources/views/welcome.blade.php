<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>الشعبة الخدمية - لوحة التحكم</title> {{-- تم تغيير العنوان --}}

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <style>
            /* Minimal Tailwind CSS equivalent for demonstration. In a real project, you'd compile this. */
            body { font-family: 'Figtree', sans-serif; background-color: #f3f4f6; color: #1f2937; }
            .dark\:bg-black { background-color: #000; }
            .dark\:text-white { color: #fff; }
            .min-h-screen { min-height: 100vh; }
            .flex { display: flex; }
            .flex-col { flex-direction: column; }
            .items-center { align-items: center; }
            .justify-center { justify-content: center; }
            .selection\:bg-\[\#FF2D20\]::selection { background-color: #FF2D20; }
            .selection\:text-white::selection { color: #fff; }
            .py-10 { padding-top: 2.5rem; padding-bottom: 2.5rem; }
            .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
            .font-semibold { font-weight: 600; }
            .text-black { color: #000; }
            .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
            .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .rounded-md { border-radius: 0.375rem; }
            .ring-1 { border-width: 1px; }
            .ring-transparent { border-color: transparent; }
            .transition { transition-property: all; transition-duration: 150ms; }
            .hover\:text-black\/70:hover { color: rgba(0,0,0,0.7); }
            .focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
            .focus-visible\:ring-\[\#FF2D20\]:focus-visible { outline-color: #FF2D20; }
            /* Dark mode styles */
            @media (prefers-color-scheme: dark) {
                .dark\:bg-black { background-color: #000; }
                .dark\:text-white { color: #fff; }
                .dark\:hover\:text-white\/80:hover { color: rgba(255,255,255,0.8); }
                .dark\:focus-visible\:ring-white:focus-visible { outline-color: #fff; }
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <header class="py-10">
                <div class="flex justify-center">
                    {{-- يمكنك وضع شعارك الخاص هنا بدلاً من نص Laravel SVG --}}
                    {{-- مثال: صورة شعار --}}
                    <img src="{{ asset('images/your_custom_logo.png') }}" alt="الشعار الخاص بك" class="h-16 w-auto">
                    {{-- أو يمكنك وضع نص هنا بدلاً من الشعار --}}
                    {{-- <h1 class="text-3xl font-bold text-black dark:text-white">الشعبة الخدمية</h1> --}}
                </div>
            </header>

            <main class="mt-6 text-center">
                <h2 class="text-xl font-semibold text-black dark:text-white mb-4">أهلاً بك في نظام الشعبة الخدمية</h2>
                <p class="text-sm/relaxed text-black dark:text-white/70">
                    هذا النظام مخصص لإدارة الشعبة الخدمية. يرجى تسجيل الدخول للوصول إلى لوحة التحكم.
                </p>

                @if (Route::has('login'))
                    <nav class="mt-6 flex justify-center gap-4">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                لوحة التحكم
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                تسجيل الدخول
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    التسجيل
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </main>

            <footer class="mt-6 text-center text-sm text-black dark:text-white/70">
                <p>&copy; {{ date('Y') }} الشعبة الخدمية. جميع الحقوق محفوظة.</p>
            </footer>
        </div>
    </body>
</html>