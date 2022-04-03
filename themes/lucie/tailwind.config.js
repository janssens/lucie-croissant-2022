const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: ['./layouts/**/*.html','./../../content/**/*.md','./../../public/**/*.html'],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['"nunito_sans"', ...defaultTheme.fontFamily.sans],
        'serif': ['"libre_baskerville"', ...defaultTheme.fontFamily.serif],
      },
      colors: {
        'union-blue': '#3F2682',
        'eelv-green': '#409682'
      },
    },
  },
  plugins: [],
}