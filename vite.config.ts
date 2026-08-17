import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig(async () => {
    const plugins: any[] = [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ];

    // Wayfinder automatically runs `php artisan wayfinder:generate` during build.
    // Enable only if ENABLE_WAYFINDER=true or in PHP-enabled dev environment.
    if (process.env.ENABLE_WAYFINDER === 'true') {
        try {
            const { wayfinder } = await import('@laravel/vite-plugin-wayfinder');
            plugins.push(wayfinder({ formVariants: true }));
        } catch {
            // Ignore if missing
        }
    }

    return {
        plugins,
        server: {
            host: 'localhost',
            cors: true,
        },
    };
});
