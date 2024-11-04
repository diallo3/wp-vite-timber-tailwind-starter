/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin');
const colors = require('tailwindcss/colors');
const { addDynamicIconSelectors } = require('@iconify/tailwind');

module.exports = {
    darkMode: 'class',
    content: [
        './*.php',
        './templates/**/*.twig', 
        './src/**/*.js'
    ],
    theme: {
        extend: {
            colors: {
                'wordpress-blue': '#0d99d5',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'), 
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/container-queries'),
        require("tailwindcss-fluid-type")({
            settings: {
              // ...
              prefix: "fluid-",
            },
        }),
        addDynamicIconSelectors(),
    ],
}

