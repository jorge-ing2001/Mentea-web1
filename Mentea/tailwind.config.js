import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        { pattern: /bg-(yellow|blue|green|red|gray|amber|indigo|emerald|teal|cyan)-(50|100|200|500|600|700|800)/ },
        { pattern: /text-(yellow|blue|green|red|gray|amber|indigo|emerald|teal|cyan)-(600|700|800)/ },
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                mentea: {
                    50:  '#F0FDFA',
                    100: '#CCFBF1',
                    200: '#A7F3D0',
                    300: '#5EEAD4',
                    400: '#2DD4BF',
                    500: '#14B8A6',
                    600: '#0F766E',
                    700: '#0D9488',
                    800: '#115E59',
                    900: '#053B50',
                    950: '#042F3A',
                    navy: '#053B50',
                    green: '#10B981',
                    teal: '#2DD4BF',
                    mint: '#A7F3D0',
                },
            },
            boxShadow: {
                soft: '0 4px 20px -4px rgba(15, 118, 110, 0.15)',
                card: '0 1px 3px rgba(5, 59, 80, 0.06)',
            },
            backgroundImage: {
                'mentea-gradient': 'linear-gradient(135deg, #053B50 0%, #0F766E 45%, #10B981 100%)',
                'mentea-soft': 'linear-gradient(180deg, #F0FDFA 0%, #F8FAFC 40%, #FFFFFF 100%)',
            },
        },
    },
    plugins: [forms],
};
