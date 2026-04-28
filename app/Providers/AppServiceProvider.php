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
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

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
