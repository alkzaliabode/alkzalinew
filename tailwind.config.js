/** @type {import('tailwindcss').Config} */
import plugin from 'tailwindcss/plugin'
import defaultTheme from 'tailwindcss/defaultTheme'
import colors from 'tailwindcss/colors' // استيراد الألوان الافتراضية لـ Tailwind مباشرة

export default {
  content: [
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './resources/views/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Cairo', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: {
          50: '#fff7ed',
          100: '#ffedd5',
          200: '#fed7aa',
          300: '#fdba74',
          400: '#fb923c',
          500: '#f97316', // برتقالي رئيسي
          600: '#ea580c',
          700: '#c2410c',
          800: '#9a3412',
          900: '#7c2d12',
          950: '#5c1e0a',
        },
        secondary: {
          50: colors.blue[50],
          100: colors.blue[100],
          200: colors.blue[200],
          300: colors.blue[300],
          400: colors.blue[400],
          500: '#3B82F6', // الأزرق الرئيسي
          600: colors.blue[600],
          700: colors.blue[700],
          800: colors.blue[800],
          900: colors.blue[900],
          950: colors.blue[950],
        },
        success: {
          50: colors.emerald[50],
          100: colors.emerald[100],
          200: colors.emerald[200],
          300: colors.emerald[300],
          400: colors.emerald[400],
          500: '#10B981', // أخضر للنجاح
          600: colors.emerald[600],
          700: colors.emerald[700],
          800: colors.emerald[800],
          900: colors.emerald[900],
          950: colors.emerald[950],
        },
        danger: {
          50: colors.red[50],
          100: colors.red[100],
          200: colors.red[200],
          300: colors.red[300],
          400: colors.red[400],
          500: '#EF4444', // أحمر للخطأ
          600: colors.red[600],
          700: colors.red[700],
          800: colors.red[800],
          900: colors.red[900],
          950: colors.red[950],
        },
        info: {
          50: colors.cyan[50],
          100: colors.cyan[100],
          200: colors.cyan[200],
          300: colors.cyan[300],
          400: colors.cyan[400],
          500: colors.cyan[500],
          600: colors.cyan[600],
          700: colors.cyan[700],
          800: colors.cyan[800],
          900: colors.cyan[900],
          950: colors.cyan[950],
        },
        warning: {
          50: colors.yellow[50],
          100: colors.yellow[100],
          200: colors.yellow[200],
          300: colors.yellow[300],
          400: colors.yellow[400],
          500: colors.yellow[500],
          600: colors.yellow[600],
          700: colors.yellow[700],
          800: colors.yellow[800],
          900: colors.yellow[900],
          950: colors.yellow[950],
        },
        gray: {
          ...colors.gray,
        },
      },
      backgroundImage: {
        'login-bg': "url('/images/login-bg.jpg')",
      },
    },
  },
  plugins: [
    plugin(function ({ addComponents, theme }) { // *** أضف 'theme' هنا ***
      addComponents({
        '.icon-fix': {
          display: 'inline-block',
          verticalAlign: 'middle',
        },
        '.card': {
          backgroundColor: theme('colors.white'), // استخدم theme() للوصول
          boxShadow: theme('boxShadow.md'),
          borderRadius: theme('borderRadius.2xl'),
          padding: theme('spacing.4'),
        },
        '.btn-primary': {
          backgroundColor: theme('colors.primary.500'), // *** الوصول إلى primary من theme() ***
          color: theme('colors.white'),
          fontWeight: theme('fontWeight.semibold'),
          paddingLeft: theme('spacing.4'),
          paddingRight: theme('spacing.4'),
          paddingTop: theme('spacing.2'),
          paddingBottom: theme('spacing.2'),
          borderRadius: theme('borderRadius.lg'),
          transitionProperty: theme('transitionProperty.colors'),
          transitionDuration: theme('transitionDuration.150'),
          transitionTimingFunction: theme('transitionTimingFunction.ease-out'),
          '&:hover': {
            backgroundColor: theme('colors.primary.600'), // *** الوصول إلى primary من theme() ***
          },
        },
        '.btn-secondary': {
          backgroundColor: theme('colors.secondary.500'), // *** الوصول إلى secondary من theme() ***
          color: theme('colors.white'),
          fontWeight: theme('fontWeight.semibold'),
          paddingLeft: theme('spacing.4'),
          paddingRight: theme('spacing.4'),
          paddingTop: theme('spacing.2'),
          paddingBottom: theme('spacing.2'),
          borderRadius: theme('borderRadius.lg'),
          transitionProperty: theme('transitionProperty.colors'),
          transitionDuration: theme('transitionDuration.150'),
          transitionTimingFunction: theme('transitionTimingFunction.ease-out'),
          '&:hover': {
            backgroundColor: theme('colors.blue.700'), // هذا اللون blue.700 هو من Tailwind الافتراضي، لذلك colors.blue[700] صحيح أيضًا، لكن theme() أكثر اتساقًا
          },
        },
      })
    }),
  ],
}