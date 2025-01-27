import {defineConfig} from "vite";
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import * as fs from "node:fs";

const tsFiles = fs
    .readdirSync("resources/ts")
    .filter((f) => f.endsWith(".ts"))
    .map((f) => `resources/ts/${f}`);
const cssFiles = fs
    .readdirSync("resources/css")
    .filter((f) => f.endsWith(".css"))
    .map((f) => `resources/css/${f}`);

export default defineConfig({
    plugins: [
        react(),
        tailwindcss(),
        laravel({
            input: [
                ...tsFiles, ...cssFiles,
            ],
            refresh: true
        })
    ]
});
