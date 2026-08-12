import type { NextRequest } from 'next/server';
import { NextResponse } from 'next/server';

function shouldForward(request: NextRequest): boolean {
    if (!['GET', 'HEAD'].includes(request.method)) return true;

    const pathname = request.nextUrl.pathname;
    return (
        request.headers.get('x-inertia') === 'true' ||
        request.headers.get('x-requested-with') === 'XMLHttpRequest' ||
        pathname.startsWith('/storage/') ||
        pathname === '/broadcasting/auth' ||
        pathname === '/up'
    );
}

export function proxy(request: NextRequest) {
    if (!shouldForward(request)) return NextResponse.next();

    const configuredOrigin = process.env.LARAVEL_API_URL?.trim();
    if (!configuredOrigin) {
        return NextResponse.json(
            { message: 'LARAVEL_API_URL is not configured.' },
            { status: 503 },
        );
    }

    let target: URL;
    try {
        target = new URL(
            `${request.nextUrl.pathname}${request.nextUrl.search}`,
            configuredOrigin.endsWith('/')
                ? configuredOrigin
                : `${configuredOrigin}/`,
        );
    } catch {
        return NextResponse.json(
            { message: 'LARAVEL_API_URL is invalid.' },
            { status: 503 },
        );
    }

    const requestHeaders = new Headers(request.headers);
    requestHeaders.set('x-forwarded-host', request.nextUrl.host);
    requestHeaders.set('x-forwarded-proto', request.nextUrl.protocol.slice(0, -1));
    requestHeaders.set('x-dalat-next-proxy', '1');

    return NextResponse.rewrite(target, {
        request: { headers: requestHeaders },
    });
}

export const config = {
    matcher: [
        '/((?!_next/static|_next/image|favicon.ico|favicon.png|apple-touch-icon.png|robots.txt|sitemap.xml).*)',
    ],
};
