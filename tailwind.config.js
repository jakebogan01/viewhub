const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.svelte',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                jost: ["Jost", "sans-serif"],
            },
            fontSize: {
                13: "0.8125rem",
                15: "0.9375rem",
            },
            lineHeight: {
                19: "1.1875rem",
                22: "1.375rem",
            },
            screens: {
                1440: "1440px",
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
};
