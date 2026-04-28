<?php

use App\Http\Middleware\EnsureUserIsProvider;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

$basePath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Local Cache Guard
|--------------------------------------------------------------------------
|
| Route/config optimize caches are convenient in production, but they often
| get stale during local feature work and can hide freshly added endpoints.
| In local/testing, we proactively remove those runtime cache files before
| the app boots so the current source of truth stays in routes/*.php + config/*.
|
*/
if (is_file($basePath.'/.env')) {
    $appEnv = env('APP_ENV');

    if (! is_string($appEnv) || $appEnv === '') {
        foreach (file($basePath.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if (str_starts_with($line, 'APP_ENV=')) {
                $appEnv = trim(substr($line, strlen('APP_ENV=')), "\"' \t");
                break;
            }
        }
    }

    if (in_array($appEnv, ['local', 'testing'], true)) {
        foreach (glob($basePath.'/bootstrap/cache/routes-*.php') ?: [] as $cachedRouteFile) {
            @unlink($cachedRouteFile);
        }

        foreach ([
            $basePath.'/bootstrap/cache/config.php',
            $basePath.'/bootstrap/cache/events.php',
        ] as $runtimeCacheFile) {
            if (is_file($runtimeCacheFile)) {
                @unlink($runtimeCacheFile);
            }
        }
    }
}

return Application::configure(basePath: $basePath)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);


        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, \Throwable $exception, Request $request) {
            if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403])) {
                return Inertia::render('errors/Error', ['status' => $response->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            } elseif ($response->getStatusCode() === 419) {
                return back()->with([
                    'message' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.',
                ]);
            }

            return $response;
        });
    })->create();
