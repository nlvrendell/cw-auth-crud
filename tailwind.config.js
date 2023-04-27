const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
    important: true,
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
        colors: {
            primary: {
                50: "#C9F9EE",
                100: "#B2F7E7",
                200: "#85F1D8",
                300: "#57ECCA",
                400: "#29E7BB",
                500: "#16C79E",
                600: "#11997A",
                700: "#0C6B55",
                800: "#073D31",
                900: "#020F0C",
            },
        },
    },

    plugins: [require("@tailwindcss/forms")],
};
