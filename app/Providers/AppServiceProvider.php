<?php

namespace App\Providers;

use App\Repositories\Contracts\Category\CategoryRepositoryInterface;
use App\Repositories\Eloquent\Category\EloquentCategoryRepository;
use App\Repositories\Contracts\Booking\BookingRepositoryInterface;
use App\Repositories\Eloquent\Booking\EloquentBookingRepository;
use App\Repositories\Contracts\Review\ReviewRepositoryInterface;
use App\Repositories\Eloquent\Review\EloquentReviewRepository;
use App\Repositories\Contracts\Service\ServiceRepositoryInterface;
use App\Repositories\Eloquent\Service\EloquentServiceRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Normalize APP_KEY before the encrypter is resolved. Render operators often
        // paste the 32-byte payload without the required "base64:" prefix; entrypoint
        // also fixes this for the process env, but config:cache / FPM workers need a
        // belt-and-suspenders path inside PHP itself.
        $rawKey = $_ENV['APP_KEY'] ?? $_SERVER['APP_KEY'] ?? null;
        if (! is_string($rawKey) || $rawKey === '') {
            $fromEnv = getenv('APP_KEY');
            $rawKey = is_string($fromEnv) ? $fromEnv : null;
        }
        if (is_string($rawKey)) {
            $key = trim($rawKey, " \t\n\r\0\x0B\"'");
            if ($key !== '' && ! str_starts_with($key, 'base64:')) {
                if (preg_match('/^[A-Za-z0-9+\/]+=*$/', $key) === 1 && strlen($key) >= 43 && strlen($key) <= 48) {
                    $key = 'base64:'.$key;
                    $_ENV['APP_KEY'] = $key;
                    $_SERVER['APP_KEY'] = $key;
                    putenv('APP_KEY='.$key);
                }
            }
        }

        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, EloquentBookingRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, EloquentReviewRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, EloquentServiceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // config:cache may have baked a payload-only APP_KEY before entrypoint
        // normalization; repair the runtime config so the encrypter can boot.
        $cachedKey = config('app.key');
        if (is_string($cachedKey) && $cachedKey !== '' && ! str_starts_with($cachedKey, 'base64:')) {
            if (preg_match('/^[A-Za-z0-9+\/]+=*$/', $cachedKey) === 1 && strlen($cachedKey) >= 43 && strlen($cachedKey) <= 48) {
                config(['app.key' => 'base64:'.$cachedKey]);
            }
        }

        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // Public request host is finalized in ForcePublicUrl middleware (proxy-aware).
        // Keep scheme HTTPS in production for console/queue generated URLs.
        if (app()->isProduction()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Root-relative Vite URLs (/build/...) so custom-domain pages never
        // pull JS/CSS from a different origin (avoids CORS when APP_URL or
        // X-Forwarded-Host is wrong behind Vercel → Render).
        Vite::createAssetPathsUsing(fn (string $path, $secure = null): string => '/'.ltrim($path, '/'));

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
