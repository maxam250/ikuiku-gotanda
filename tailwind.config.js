const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './public/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': '0.6rem',
            },
            gridTemplateColumns: {
                '13': 'repeat(13, minmax(0, 1fr))',
                '14': 'repeat(14, minmax(0, 1fr))',
                '15': 'repeat(15, minmax(0, 1fr))',
                '16': 'repeat(16, minmax(0, 1fr))',
            },
            width: {
                'costume-mobile': 'calc(33.333% - 12px)',
                'costume-desktop': '240px',
                'option-mobile': 'calc(33.333% - 12px)',
                'option-desktop': '240px',
            },
            minWidth: {
                'costume': '120px',
                'option': '120px',
            }
        },
    },

    plugins: [forms],

    // JITモードでarbitrary valuesを有効にする
    safelist: [
        'w-[calc(33.333%-12px)]',
        'sm:w-[calc(33.333%-12px)]',
        'lg:w-60',
        'grid-cols-3',
        'lg:grid-cols-4',
        'grid-cols-7',
    ],
};
