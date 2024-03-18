/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin');
const colors = require('tailwindcss/colors');

module.exports = {
    darkMode: 'class',
    content: [
        './*.php',
        './templates/**/*.twig', 
        './src/**/*.js'
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'), 
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/container-queries'),
        plugin(function ({ addUtilities }) {
            const utilFormSwitch = {
                ".form-switch": {
                    border: "transparent",
                    "background-color": colors.gray[300],
                    "background-image": "url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e\")",
                    "background-position": "left center",
                    "background-repeat": "no-repeat",
                    "background-size": "contain !important",
                    "vertical-align": "top",
                    "&:checked": {
                        border: "transparent",
                        "background-color": "currentColor",
                        "background-image":
                        "url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e\")",
                        "background-position": "right center",
                    },
                    "&:disabled, &:disabled + label": {
                        opacity: ".5",
                        cursor: "not-allowed",
                    },
                },
            };
            addUtilities(utilFormSwitch);
        }),
    ],
}

