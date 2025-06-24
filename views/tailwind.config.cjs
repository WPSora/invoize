// @ts-check
import { join } from 'path';
import { skeleton } from '@skeletonlabs/tw-plugin';

/** @type {import('tailwindcss').Config}*/
const config = {
    content: [
        "./src/**/*.{html,js,svelte,ts}",
        join(require.resolve(
            '@skeletonlabs/skeleton'),
            '../**/*.{html,js,svelte,ts}'
        )
    ],
    // important: "#wp-invoice-app",
    darkMode: "class",
    theme: {
        container: {
            center: true,
            padding: "2rem",
            screens: {
                "2xl": "1400px"
            }
        },
        extend: {
            screens: {
                print: { raw: 'print' },
            },
            colors: {
                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",
                primary: {
                    DEFAULT: "hsl(var(--primary))",
                    foreground: "hsl(var(--primary-foreground))",
                    50: "#CFB2FF",
                    100: "#BF99FF",
                    200: "#AF80FF",
                    300: "#9F66FF",
                    400: "#904DFF",
                    500: "#8033FF",
                    600: "#701AFF",
                    700: "#6000FF",
                    800: "#5600E5",
                    900: "#4D00CC",
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary))",
                    foreground: "hsl(var(--secondary-foreground))"
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive))",
                    foreground: "hsl(var(--destructive-foreground))"
                },
                muted: {
                    DEFAULT: "hsl(var(--muted))",
                    foreground: "hsl(var(--muted-foreground))"
                },
                accent: {
                    DEFAULT: "hsl(var(--accent))",
                    foreground: "hsl(var(--accent-foreground))"
                },
                popover: {
                    DEFAULT: "hsl(var(--popover))",
                    foreground: "hsl(var(--popover-foreground))"
                },
                card: {
                    DEFAULT: "hsl(var(--card))",
                    foreground: "hsl(var(--card-foreground))"
                }
            },
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)"
            },
            // fontFamily: {
            //     sans: ["Inter", ...fontFamily.sans]
            // }
        }
    },

    plugins: [
        require("tailwindcss-animate"),
        skeleton({
            themes: { preset: ["skeleton", "modern", "crimson", "gold-nouveau", "wintry"] }
        })
    ],
};

module.exports = config;
