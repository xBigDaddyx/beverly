import preset from './vendor/filament/filament/tailwind.config.preset'
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
const colors = require('tailwindcss/colors')

export default {
    presets: [
        require("./vendor/wireui/wireui/tailwind.config.js"),


    ],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        "./vendor/wireui/wireui/src/*.php",
        "./vendor/wireui/wireui/ts/**/*.ts",
        "./vendor/wireui/wireui/src/WireUi/**/*.php",
        "./vendor/wireui/wireui/src/Components/**/*.php",
    ],
    daisyui: {
        themes: [
            {
                beverly: {
                    primary: '#f9b218',
                    secondary: '#306e6d',
                    accent: '#c8e0ca',
                    neutral: '#272727',
                    'base-100': '#ffffff',
                },
            }
        ],
        darkTheme: "dark",
        base: true,
        styled: true,
        utils: true,
        themeRoot: ":root"
    },
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#FFFFFF',
                    100: '#FAFDF9',
                    200: '#DEF2DB',
                    300: '#C2E7BE',
                    400: '#A6DCA0',
                    500: '#8AD182',
                    600: '#64C259',
                    700: '#48A63D',
                    800: '#367D2E',
                    900: '#24541F',
                    950: '#1B3F17'
                },
                secondary: {
                    50: '#69E0E0',
                    100: '#59DCDC',
                    200: '#37D5D5',
                    300: '#27BCBC',
                    400: '#209A9A',
                    500: '#197878',
                    600: '#125656',
                    700: '#0B3535',
                    800: '#041313',
                    900: '#000000',
                    950: '#000000'
                },
                positive: {
                    '50': '#f4f9f5',
                    '100': '#e6f2e7',
                    '200': '#c8e0ca',
                    '300': '#a8cdab',
                    '400': '#7aae7f',
                    '500': '#57905d',
                    '600': '#447549',
                    '700': '#385d3c',
                    '800': '#304b33',
                    '900': '#283f2b',
                    '950': '#122114',
                },
                negative: colors.red,
                warning: colors.amber,
                info: colors.blue
            },

        },
    },

    plugins: [require('daisyui'), typography],
    darkMode: ['class'],
}
