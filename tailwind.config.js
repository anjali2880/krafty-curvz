/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./storage/framework/views/*.php",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['Cal Sans', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                primary: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                },
                accent: {
                    50: '#fdf4ff',
                    100: '#fae8ff',
                    200: '#f5d0fe',
                    300: '#f0abfc',
                    400: '#e879f9',
                    500: '#d946ef',
                    600: '#c026d3',
                    700: '#a21caf',
                    800: '#86198f',
                    900: '#701a75',
                },
                resin: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
                ocean: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#06b6d4',
                    600: '#0891b2',
                    700: '#0e7490',
                    800: '#155e75',
                    900: '#164e63',
                },
                sunset: {
                    50: '#fff7ed',
                    100: '#fed7aa',
                    200: '#fdba74',
                    300: '#fb923c',
                    400: '#f97316',
                    500: '#ea580c',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                neutral: {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#e5e5e5',
                    300: '#d4d4d4',
                    400: '#a3a3a3',
                    500: '#737373',
                    600: '#525252',
                    700: '#404040',
                    800: '#262626',
                    900: '#171717',
                }
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'slide-down': 'slideDown 0.5s ease-out',
                'scale-in': 'scaleIn 0.3s ease-out',
                'bounce-soft': 'bounceSoft 2s infinite',
                'float': 'float 3s ease-in-out infinite',
                'resin-flow': 'resinFlow 8s ease-in-out infinite',
                'resin-pour': 'resinPour 6s ease-out infinite',
                'resin-swirl': 'resinSwirl 10s linear infinite',
                'liquid-wave': 'liquidWave 4s ease-in-out infinite',
                'color-shift': 'colorShift 5s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.9)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                bounceSoft: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                resinFlow: {
                    '0%': { 
                        transform: 'translateY(-100px) rotate(0deg) scale(0.8)',
                        opacity: '0.3'
                    },
                    '25%': { 
                        transform: 'translateY(0px) rotate(90deg) scale(1)',
                        opacity: '0.6'
                    },
                    '50%': { 
                        transform: 'translateY(50px) rotate(180deg) scale(1.1)',
                        opacity: '0.8'
                    },
                    '75%': { 
                        transform: 'translateY(100px) rotate(270deg) scale(1)',
                        opacity: '0.6'
                    },
                    '100%': { 
                        transform: 'translateY(200px) rotate(360deg) scale(0.8)',
                        opacity: '0.3'
                    },
                },
                resinPour: {
                    '0%': { 
                        transform: 'translateX(-50px) translateY(-50px) scale(0)',
                        opacity: '0'
                    },
                    '30%': { 
                        transform: 'translateX(0px) translateY(0px) scale(1.2)',
                        opacity: '0.7'
                    },
                    '60%': { 
                        transform: 'translateX(25px) translateY(25px) scale(0.8)',
                        opacity: '0.4'
                    },
                    '100%': { 
                        transform: 'translateX(50px) translateY(50px) scale(0)',
                        opacity: '0'
                    },
                },
                resinSwirl: {
                    '0%': { 
                        transform: 'rotate(0deg) translateX(100px) rotate(0deg)',
                        opacity: '0.5'
                    },
                    '100%': { 
                        transform: 'rotate(360deg) translateX(100px) rotate(-360deg)',
                        opacity: '0.5'
                    },
                },
                liquidWave: {
                    '0%, 100%': { 
                        transform: 'translateY(0px) scaleY(1)',
                        borderRadius: '60% 40% 30% 70% / 60% 30% 70% 40%'
                    },
                    '50%': { 
                        transform: 'translateY(-20px) scaleY(0.8)',
                        borderRadius: '30% 60% 70% 40% / 50% 60% 30% 60%'
                    },
                },
                colorShift: {
                    '0%, 100%': { 
                        background: 'linear-gradient(45deg, #f97316, #d946ef, #06b6d4)',
                    },
                    '33%': { 
                        background: 'linear-gradient(45deg, #d946ef, #06b6d4, #f97316)',
                    },
                    '66%': { 
                        background: 'linear-gradient(45deg, #06b6d4, #f97316, #d946ef)',
                    },
                },
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                'large': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 4px 25px -5px rgba(0, 0, 0, 0.1)',
                'glow': '0 0 20px rgba(245, 158, 11, 0.3)',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            backdropBlur: {
                xs: '2px',
            },
        },
    },
    plugins: [],
}
