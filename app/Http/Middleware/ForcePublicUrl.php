<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keep generated absolute URLs on the public browser origin when the app is
 * reached through a reverse proxy (Vercel custom domain → Render).
 *
 * Vercel external rewrites often set Host to the upstream (*.onrender.com) and
 * may omit X-Forwarded-Host, so we prefer APP_URL as the public root and only
 * override when a real forwarded host is present.
 */
class ForcePublicUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = config('app.url');
        if (is_string($appUrl) && $appUrl !== '') {
            URL::forceRootUrl(rtrim($appUrl, '/'));
        }

        $forwardedHost = $request->headers->get('X-Forwarded-Host');
        if (is_string($forwardedHost) && $forwardedHost !== '') {
            $host = trim(explode(',', $forwardedHost)[0]);

            if ($host !== '' && ! $this->isInternalHost($host)) {
                $forwardedProto = $request->headers->get('X-Forwarded-Proto');
                $scheme = is_string($forwardedProto) && $forwardedProto !== ''
                    ? trim(explode(',', $forwardedProto)[0])
                    : $request->getScheme();

                if (! in_array($scheme, ['http', 'https'], true)) {
                    $scheme = 'https';
                }

                URL::forceRootUrl($scheme.'://'.$host);
            }
        }

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        return $next($request);
    }

    private function isInternalHost(string $host): bool
    {
        $host = strtolower($host);

        return str_ends_with($host, '.onrender.com')
            || str_ends_with($host, '.vercel.app')
            || $host === 'localhost'
            || str_starts_with($host, '127.0.0.1');
    }
}
