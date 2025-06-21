/** @type {import('tailwindcss').Config} */
import plugin from 'tailwindcss/plugin'

export default {
  content: [
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Cairo', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [
    plugin(function ({ addComponents }) {
      addComponents({
        '.icon-fix': {
          display: 'inline-block',
          verticalAlign: 'middle',
        },
      })
    }),
  ],
}
