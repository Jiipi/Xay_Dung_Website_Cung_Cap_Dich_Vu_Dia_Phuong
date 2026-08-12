import type { MetadataRoute } from 'next';
import { getSiteUrl } from '@next/lib/site-url';

const publicPaths = [
    '/',
    '/services',
    '/categories',
    '/search',
    '/ai-planner',
    '/about',
    '/contact',
    '/policy',
];

async function servicePaths(): Promise<string[]> {
    const backend = process.env.LARAVEL_API_URL?.trim();
    if (!backend) return [];

    try {
        const response = await fetch(new URL('/services', backend), {
            cache: 'no-store',
            headers: {
                Accept: 'application/json',
                'X-Inertia': 'true',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: AbortSignal.timeout(10_000),
        });
        if (!response.ok) return [];

        const payload = (await response.json()) as {
            props?: { services?: Array<{ id?: number | string }> };
        };

        return (payload.props?.services ?? [])
            .filter((service) => service.id !== undefined)
            .map((service) => `/services/${service.id}`);
    } catch {
        return [];
    }
}

export const dynamic = 'force-dynamic';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
    const siteUrl = getSiteUrl();
    const paths = [...publicPaths, ...(await servicePaths())];

    return paths.map((path) => ({
        url: new URL(path, siteUrl).toString(),
        changeFrequency: path === '/' ? 'daily' : 'weekly',
        priority: path === '/' ? 1 : path.startsWith('/services/') ? 0.8 : 0.7,
    }));
}
