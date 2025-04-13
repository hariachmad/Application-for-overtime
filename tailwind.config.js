/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: 'jit',
  content: ["./admin/**/*.{html,js,php}",
    "./**/*.php",
    "./src/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

