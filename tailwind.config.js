import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Fredoka', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#1E3A8F',
                secondary: '#10B981',
                tertiary: '#F56E0B',
            },
        },
    },
    plugins: [],
};
