export function getSiteUrl(): URL {
    const configuredUrl = process.env.NEXT_PUBLIC_SITE_URL?.trim();

    if (!configuredUrl) {
        throw new Error(
            'NEXT_PUBLIC_SITE_URL is required (for example, https://example.com).',
        );
    }

    const url = new URL(configuredUrl);
    if (!['http:', 'https:'].includes(url.protocol)) {
        throw new Error('NEXT_PUBLIC_SITE_URL must use HTTP or HTTPS.');
    }

    return url;
}
