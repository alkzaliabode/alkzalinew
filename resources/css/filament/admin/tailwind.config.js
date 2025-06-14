import preset from '../../../../vendor/filament/filament/tailwind.config.preset.js';

export default {
  presets: [preset],
  content: [
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          500: '#4f46e5',   // عدّل اللون الأساسي حسب رغبتك
          600: '#4338ca',
        },
        sidebar: '#1e293b', // إضافة لون للسايدبار
      },
      fontFamily: {
        sans: ['Cairo', 'sans-serif'],
      },
    },
  },
};
