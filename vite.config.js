import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    build: {
        sourcemap: true,

        rollupOptions: {
            output: {
                entryFileNames: `assets/[name].js`,
                chunkFileNames: `assets/[name].js`,
                assetFileNames: `assets/[name].[ext]`
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/editors/codemirror/codemirror.js',
                'resources/js/editors/markdown/markdown.js',
            ],
            refresh: true,
            publicDirectory: 'resources',
            buildDirectory: 'dist',
        }),
    ],
})
