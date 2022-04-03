const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: ["layouts/*.html","layouts/partials/*.html","layouts/_default/*.html","layouts/donate/*.html","../../content/*.md","../../content/*/*.md","../../content/*/*/*.md"],
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