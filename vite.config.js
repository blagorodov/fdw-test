import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    css: {
        lightningcss: {
            // legacy DataTables CSS uses IE `*property` hacks
            errorRecovery: true,
        },
    },
    build: {
        rollupOptions: {
            output: {
                // Без хеша в имени — стабильные пути в build/assets (удобнее diff/деплой).
                // После релиза может понадобиться сброс кэша браузера/CDN для JS/CSS.
                entryFileNames: (chunkInfo) => {
                    const id = chunkInfo.facadeModuleId ?? '';
                    const m = id.match(/([^/]+)\.(js|css)$/);
                    if (m) {
                        return `assets/${m[1]}.${m[2]}`;
                    }

                    return 'assets/[name].js';
                },
                // Отдельная папка, чтобы имена чанков не пересекались с entry.
                chunkFileNames: 'assets/chunks/[name].js',
                assetFileNames: 'assets/[name][extname]',
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/vote.js',
                'resources/js/stat.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
