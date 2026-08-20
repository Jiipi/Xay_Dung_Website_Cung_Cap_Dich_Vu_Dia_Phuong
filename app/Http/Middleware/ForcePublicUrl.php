<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * When the app is reached through a reverse proxy (Vercel custom domain → Render),
 * force generated absolute URLs (Vite/asset/route) to the public host the browser used.
 * Prevents cross-origin /build/*.js loads and CORS failures.
 */
class ForcePublicUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $forwardedHost = $request->headers->get('X-Forwarded-Host');
        $host = $forwardedHost
            ? trim(explode(',', $forwardedHost)[0])
            : $request->getHost();

        if (is_string($host) && $host !== '') {
            $forwardedProto = $request->headers->get('X-Forwarded-Proto');
            $scheme = $forwardedProto
                ? trim(explode(',', $forwardedProto)[0])
                : $request->getScheme();

            if (! in_array($scheme, ['http', 'https'], true)) {
                $scheme = 'https';
            }

            URL::forceRootUrl($scheme.'://'.$host);
        }

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
