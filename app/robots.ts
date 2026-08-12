import type { MetadataRoute } from 'next';
import { getSiteUrl } from '@next/lib/site-url';

export default function robots(): MetadataRoute.Robots {
    const siteUrl = getSiteUrl();

    return {
        rules: {
            userAgent: '*',
            allow: '/',
            disallow: [
                '/admin/',
                '/customer/',
                '/provider/',
                '/settings/',
                '/chat',
                '/notifications',
                '/dashboard',
            ],
        },
        sitemap: new URL('/sitemap.xml', siteUrl).toString(),
    };
}
