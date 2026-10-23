import forms from '@tailwindcss/forms';

/** Warna & tipografi mengikuti Style Guide pada makalah. */
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#007DFC',
                    50: '#E8F3FF',
                    100: '#B9DCFF',
                    600: '#0068D6',
                    700: '#0055AD',
                },
                ink: '#121212',
                grey: '#888689',
                neutral: '#F6F6F6',
            },
            fontFamily: {
                sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                marker: ['"Permanent Marker"', 'cursive'],
            },
            boxShadow: {
                card: '0 4px 20px rgba(18, 18, 18, 0.06)',
                nav: '0 -4px 20px rgba(18, 18, 18, 0.06)',
            },
        },
    },
    plugins: [forms],
};
