import * as fs from "node:fs";
import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

const tsFiles = fs
    .readdirSync("resources/ts")
    .filter((f) => f.endsWith(".ts") || f.endsWith(".tsx"))
    .map((f) => `resources/ts/${f}`);
const cssFiles = fs
    .readdirSync("resources/css")
    .filter((f) => f.endsWith(".css"))
    .map((f) => `resources/css/${f}`);

export default defineConfig({
    server: {
        cors: true,
    },
    plugins: [
        react(),
        tailwindcss(),
        laravel({
            input: [...tsFiles, ...cssFiles],
            refresh: true,
        }),
    ],
});
