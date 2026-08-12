import { defineConfig, globalIgnores } from 'eslint/config';
import nextVitals from 'eslint-config-next/core-web-vitals';
import nextTs from 'eslint-config-next/typescript';
import prettier from 'eslint-config-prettier/flat';

export default defineConfig([
    ...nextVitals,
    ...nextTs,
    prettier,
    {
        files: ['resources/js/**/*.ts'],
        rules: {
            // This tree contains Vue composables; React's hook/global rules do
            // not understand Vue lifecycle APIs and report false positives.
            'react-hooks/rules-of-hooks': 'off',
            'react-hooks/globals': 'off',
        },
    },
    globalIgnores([
        '.next/**',
        'node_modules/**',
        'vendor/**',
        'public/build/**',
        'bootstrap/cache/**',
        'storage/**',
        'fene/**',
        'resources/js/**/*.vue',
        'resources/js/app.ts',
        'resources/js/ssr.ts',
        'resources/js/actions/**',
        'resources/js/routes/**',
        'resources/js/wayfinder/**',
        'next-env.d.ts',
    ]),
]);
