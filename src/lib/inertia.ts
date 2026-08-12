import 'server-only';

import type { Page } from '@inertiajs/core';
import { cookies, headers } from 'next/headers';
import { cache } from 'react';

export type InertiaPageResult =
    | { kind: 'page'; page: Page; status: number }
    | { kind: 'redirect'; location: string; status: number }
    | { kind: 'not-found'; status: 404 };

function backendOrigin(): URL {
    const value = process.env.LARAVEL_API_URL?.trim();

    if (!value) {
        throw new Error(
            'LARAVEL_API_URL is required. Point it to the separately hosted Laravel backend.',
        );
    }

    try {
        return new URL(value.endsWith('/') ? value : `${value}/`);
    } catch {
        throw new Error('LARAVEL_API_URL must be an absolute HTTP(S) URL.');
    }
}

function publicLocation(location: string, origin: URL): string {
    const target = new URL(location, origin);
    return `${target.pathname}${target.search}${target.hash}`;
}

export const getInertiaPage = cache(
    async (relativeUrl: string): Promise<InertiaPageResult> => {
        const requestHeaders = await headers();
        const cookieStore = await cookies();
        const origin = backendOrigin();
        const target = new URL(relativeUrl.replace(/^\//, ''), origin);
        const cookieHeader = cookieStore.toString();

        const response = await fetch(target, {
            method: 'GET',
            cache: 'no-store',
            redirect: 'manual',
            headers: {
                Accept: 'text/html, application/xhtml+xml',
                'X-Inertia': 'true',
                'X-Requested-With': 'XMLHttpRequest',
                ...(cookieHeader ? { Cookie: cookieHeader } : {}),
                'X-Forwarded-Host': requestHeaders.get('host') ?? '',
                'X-Forwarded-Proto':
                    requestHeaders.get('x-forwarded-proto') ?? 'https',
            },
            signal: AbortSignal.timeout(20_000),
        });

        if (response.status === 404) return { kind: 'not-found', status: 404 };

        if (response.status === 409) {
            const location = response.headers.get('x-inertia-location');
            if (location) {
                return {
                    kind: 'redirect',
                    location: publicLocation(location, origin),
                    status: response.status,
                };
            }
        }

        if (response.status >= 300 && response.status < 400) {
            const location = response.headers.get('location');
            if (!location) {
                throw new Error(
                    `Laravel returned ${response.status} without a Location header.`,
                );
            }

            return {
                kind: 'redirect',
                location: publicLocation(location, origin),
                status: response.status,
            };
        }

        const contentType = response.headers.get('content-type') ?? '';
        if (!contentType.includes('application/json')) {
            throw new Error(
                `Laravel did not return an Inertia JSON response (${response.status}, ${contentType || 'unknown content type'}).`,
            );
        }

        const page = (await response.json()) as Page;
        if (!page.component || !page.props || !page.url) {
            throw new Error('Laravel returned an invalid Inertia page payload.');
        }

        return { kind: 'page', page, status: response.status };
    },
);
