import path from 'node:path';
import { createRequire } from 'node:module';
import type { NextConfig } from 'next';
import { VueLoaderPlugin } from 'vue-loader';

const require = createRequire(import.meta.url);

type WebpackRule = {
    loader?: string;
    use?: Array<string | { loader?: string }> | string | { loader?: string };
    rules?: WebpackRule[];
    oneOf?: WebpackRule[];
    resourceQuery?: unknown;
};

function loaderNames(rule: WebpackRule): string[] {
    const uses = Array.isArray(rule.use)
        ? rule.use
        : rule.use
          ? [rule.use]
          : [];

    return [rule.loader, ...uses.map((use) =>
        typeof use === 'string' ? use : use.loader,
    )].filter((value): value is string => Boolean(value));
}

function allowVueScopedStyles(rules: WebpackRule[]): void {
    for (const rule of rules) {
        if (loaderNames(rule).some((loader) => loader.includes('error-loader'))) {
            const existing = rule.resourceQuery;
            rule.resourceQuery = existing
                ? { and: [existing], not: [/vue&type=style/] }
                : { not: [/vue&type=style/] };
        }

        if (rule.rules) allowVueScopedStyles(rule.rules);
        if (rule.oneOf) allowVueScopedStyles(rule.oneOf);
    }
}

const nextConfig: NextConfig = {
    poweredByHeader: false,
    reactStrictMode: true,
    images: {
        remotePatterns: [
            { protocol: 'https', hostname: 'picsum.photos' },
            { protocol: 'https', hostname: 'images.unsplash.com' },
            { protocol: 'https', hostname: 'i.pravatar.cc' },
            { protocol: 'https', hostname: 'ui-avatars.com' },
            { protocol: 'https', hostname: '*.tile.openstreetmap.org' },
        ],
    },
    webpack(config) {
        allowVueScopedStyles(config.module.rules as WebpackRule[]);
        config.module.rules.unshift({
            resourceQuery: /vue&type=style/,
            use: [
                require.resolve('style-loader'),
                require.resolve('css-loader'),
                require.resolve('postcss-loader'),
            ],
        });
        config.module.rules.push({
            test: /\.ts$/,
            use: [
                {
                    loader: require.resolve('ts-loader'),
                    options: {
                        appendTsSuffixTo: [/\.vue$/],
                        transpileOnly: true,
                    },
                },
            ],
        });
        config.plugins.push(new VueLoaderPlugin());
        config.module.rules.unshift({
            test: /\.vue$/,
            use: [
                {
                    loader: require.resolve('vue-loader'),
                    options: {
                        enableTsInTemplate: true,
                        transformAssetUrls: {
                            base: null,
                            includeAbsolute: false,
                        },
                    },
                },
            ],
        });
        config.resolve.extensions.push('.vue');
        config.resolve.alias = {
            ...config.resolve.alias,
            '@': path.resolve(process.cwd(), 'resources/js'),
            'vue$': 'vue/dist/vue.esm-bundler.js',
        };

        return config;
    },
};

export default nextConfig;
