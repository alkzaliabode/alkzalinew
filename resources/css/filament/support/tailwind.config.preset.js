// ../support/tailwind.config.preset.js

import defaultTheme from 'tailwindcss/defaultTheme'

/**
 * @type {import('tailwindcss').Config}
 */
export default {
    // *** أضف هذا السطر هنا ***
    darkMode: 'class', // Tell Tailwind to use class-based dark mode strategy

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', ...defaultTheme.fontFamily.sans],
            },
            // إذا كنت تريد إضافة الألوان المخصصة (primary, secondary, إلخ) هنا في الـ preset
            // فيمكنك إضافتها تحت 'extend':
            // colors: {
            //   primary: { /* ... */ },
            //   secondary: { /* ... */ },
            //   // ... وهكذا
            // },
        },
    },
    // إذا كان لديك أي plugins مشتركة لجميع إعدادات Tailwind، ضعها هنا في الـ preset
    // plugins: [
    //   // ...
    // ],
}