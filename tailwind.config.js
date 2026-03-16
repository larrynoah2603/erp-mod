/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Modules/**/resources/views/**/*.blade.php',
    './app/Modules/**/Http/Livewire/**/*.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
