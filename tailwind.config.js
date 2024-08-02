import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import colors from 'tailwindcss/colors';
import defaultTheme from 'tailwindcss/defaultTheme';
import preset from './vendor/filament/support/tailwind.config.preset';

export default {
    presets: [preset],
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        '../mailcoach-ui/resources/**/*.blade.php',
        '../mailcoach-packages/packages/*/resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './src/Livewire/**/*.php',
    ],
    theme: {
        fontFamily: {
            'sans': ["Inter", "'Inter Fallback'", ...defaultTheme.fontFamily.sans],
            'roboto': ['"Roboto"', ...defaultTheme.fontFamily.sans],
            'title': ['"Inter Tight"', '"Inter Tight Fallback"', ...defaultTheme.fontFamily.sans],
            'mono': [...defaultTheme.fontFamily.mono],
        },
        fontSize: {
            '2xs': '0.625rem', // 10px
            xs: '0.75rem', // 12px
            '14': '14px', // 14px
            sm: '0.938rem', // 15px
            base: '1rem', // 16px
            lg: '1.125rem', // 18px
            xl: '1.313rem', // 21px
            '2xl': '1.5rem', // 24px
            '3xl': '1.75rem', // 28px
            '3.5xl': '2rem', // 32px
            '4xl': '3rem', // 48px
            '5xl': '3.5rem', // 56px
            '6xl': '4rem', // 64px
            '7xl': '6rem', // 96px
        },
        colors: {
            transparent: 'transparent',
            current: 'currentColor',

            'navy': '#142D6F',
            'navy-dark': '#131C2E',
            'navy-bleak': '#242F4E',
            'navy-bleak-light': '#6B7588',
            'navy-bleak-extra-light': '#7281A9',

            'blue': '#648BEF',
            'blue-dark': '#3461D6',

            'sky': '#A3BDFF',
            'sky-light': '#C4D5FF',
            'sky-extra-light': '#E1EAFF',

            'sand-dark': '#7F7A6C',
            'sand-bleak': '#D7D5D1',
            'sand': '#E1DCCC',
            'sand-light': '#EBE8DD',
            'sand-extra-light': '#F6F4EF',

            snow: '#E9E9E9',

            green: '#0FBA9E',
            'green-dark': '#157E6D',
            'green-light': '#BAE9E1',
            'green-extra-light': '#E5F5F2',

            'red-dark': '#993A37',
            red: '#ED5E58',
            'red-light': '#F9D5D3',
            'red-extra-light': '#FFF0EF',

            'orange-dark': '#996B37',
            'orange': '#D68A34',
            'orange-bright': '#EDA758',
            'orange-light': '#F9E7D3',
            'orange-extra-light': '#FFF8EF',

            black: colors.black,
            white: colors.white,
            indigo: colors.indigo,
            teal: colors.teal,
            yellow: colors.amber,
            danger: colors.rose,
            primary: colors.blue,
            success: colors.emerald,
            warning: colors.amber,
        },
        extend: {
            spacing: {
                '4.5': '1.125rem',
                30: '7.5rem',
            },
            boxShadow: {
                focus: '0 2px 2px #e5e3e1',
                input: '0px 1px 1px 0px #BCB7A840',
                card: '0px 0.9975153207778931px 2.194533586502075px 0px #534C3B1A;',
                buttons: '0px 0px 31px 0px #7F7A6C1A;',
                'profile': '0px 0.9975153207778931px 2.194533586502075px 0px #534C3B14, 0px 3.3504464626312256px 7.3709821701049805px 0px #534C3B0C, 0px 15px 33px 0px #534C3B08;',
                'dropdown': '0px 0.9975153207778931px 2.194533586502075px 0px rgba(83, 76, 59, 0.08), 0px 3.3504464626312256px 7.3709821701049805px 0px rgba(83, 76, 59, 0.05),0px 15px 33px 0px rgba(83, 76, 59, 0.03)',
            },
            gridTemplateColumns: {
                auto: 'auto',
                'auto-1fr': 'auto 1fr',
                '1fr-auto': '1fr auto',
            },
            inset: {
                full: '100%',
            },
            height: {
                '2px': '2px',
                13: '3.25rem',
            },
            minHeight: {
                4: '1rem',
                6: '1.5rem',
                8: '2rem',
                9: '2.25rem',
                10: '2.5rem',
                13: '3.25rem',
            },
            minWidth: {
                4: '1rem',
                6: '1.5rem',
                8: '2rem',
                10: '2.5rem',
                32: '8rem',
            },
            maxWidth: {
                layout: '1680px',
            },
            backgroundSize: {
                'size-200': '200% 200%',
            },
            backgroundPosition: {
                'pos-0': '0% 0%',
                'pos-100': '100% 100%',
            },
            keyframes: {
                scale: {
                    '0%, 100%': { transform: 'scale(1)' },
                    '25%': { transform: 'scale(1.05)' },
                    '50%': { transform: 'scale(1)' },
                    '75%': { transform: 'scale(1.05)' },
                }
            },
            animation: {
                scale: 'scale 300ms ease-in-out',
            }
        },
    },
    corePlugins: {
        ringWidth: false,
    },
    plugins: [forms, typography],
};
