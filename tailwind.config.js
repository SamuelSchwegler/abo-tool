/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
      "./resources/*.{js,vue}",
      "./resources/**/*.{js,vue,blade.php}",
  ],
  theme: {
    extend: {
        fontFamily: {
            sans: ['Nunito', ...defaultTheme.fontFamily.sans],
        }
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
