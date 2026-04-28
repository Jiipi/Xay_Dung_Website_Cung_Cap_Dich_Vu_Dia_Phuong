<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminStatsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Provider\ProviderAvailabilityController;
use App\Http\Controllers\Provider\ProviderBookingController;
use App\Http\Controllers\Provider\ProviderDashboardController;
use App\Http\Controllers\Provider\ProviderProfileController;
use App\Http\Controllers\Provider\ProviderReviewController;
use App\Http\Controllers\Provider\ProviderServiceController;
use App\Http\Controllers\Service\ServiceController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// TEMP DEBUG - remove after fixing
Route::get('/debug-bindings', function () {
    $results = [];
    $interfaces = [
        \App\Repositories\Contracts\Service\ServiceRepositoryInterface::class,
        \App\Repositories\Contracts\Review\ReviewRepositoryInterface::class,
        \App\Repositories\Contracts\Booking\BookingRepositoryInterface::class,
        \App\Repositories\Contracts\Category\CategoryRepositoryInterface::class,
    ];
    foreach ($interfaces as $iface) {
        try {
            $instance = app()->make($iface);
            $results[$iface] = 'OK: ' . get_class($instance);
        } catch (\Throwable $e) {
            $results[$iface] = 'ERROR: ' . $e->getMessage();
        }
    }
    // Also try making the controller
    try {
        $ctrl = app()->make(\App\Http\Controllers\Provider\ProviderServiceController::class);
        $results['ProviderServiceController'] = 'OK';
    } catch (\Throwable $e) {
        $results['ProviderServiceController'] = 'ERROR: ' . $e->getMessage();
    }
    return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

Route::get('/', [HomeController::class, 'index'])->name('home');

// Public pages
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
Route::get('/ai-planner', [ServiceController::class, 'aiPlanner'])->name('ai-planner.index');
Route::inertia('/about', 'about/Index')->name('about.index');
Route::inertia('/contact', 'contact/Index')->name('contact.index');
Route::inertia('/policy', 'policy/Index')->name('policy.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        $role = $user->vaiTroNguoiDung?->ten_vai_tro;

        if ($role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'Nhà cung cấp') {
            return redirect()->route('provider.dashboard');
        }

        return redirect()->route('customer.dashboard');
    })->name('dashboard');
    // Customer
    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/customer/bookings', [CustomerController::class, 'bookings'])->name('customer.bookings.index');
    Route::get('/customer/favorites', [CustomerController::class, 'favorites'])->name('customer.favorites.index');
    Route::post('/customer/favorites/toggle', [CustomerController::class, 'toggleFavorite'])->name('customer.favorites.toggle');
    Route::post('/customer/ai-planner/generate', [ServiceController::class, 'generateAiPlan'])
        ->middleware('throttle:8,1')
        ->name('customer.ai-planner.generate');
    Route::post('/customer/ai-planner/chat', [ServiceController::class, 'chatAiPlanner'])
        ->middleware('throttle:18,1')
        ->name('customer.ai-planner.chat');
    Route::get('/customer/profile', [\App\Http\Controllers\Customer\CustomerProfileController::class, 'edit'])->name('customer.profile');
    Route::put('/customer/profile', [\App\Http\Controllers\Customer\CustomerProfileController::class, 'update'])->name('customer.profile.update');

    // Booking flow (real controllers)
    Route::post('/customer/bookings', [BookingController::class, 'store'])->name('customer.bookings.store');
    Route::get('/customer/bookings/success/{id}', [BookingController::class, 'success'])->name('customer.bookings.success');
    Route::get('/customer/bookings/{id}', [BookingController::class, 'show'])->name('customer.bookings.show');
    Route::post('/customer/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('customer.bookings.cancel');

    // Reviews
    Route::get('/customer/reviews/create', [ReviewController::class, 'create'])->name('customer.reviews.create');
    Route::post('/customer/reviews', [ReviewController::class, 'store'])->name('customer.reviews.store');

    // Notifications
    Route::get('/customer/notifications', [NotificationController::class, 'index'])->name('customer.notifications.index');
    Route::get('/customer/notifications/recent', [NotificationController::class, 'recent'])->name('customer.notifications.recent');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Provider (protected by role middleware)
    Route::middleware(\App\Http\Middleware\EnsureUserIsProvider::class)->prefix('provider')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('provider.dashboard');

        // Profile
        Route::get('/profile', [ProviderProfileController::class, 'edit'])->name('provider.profile');
        Route::post('/profile', [ProviderProfileController::class, 'update'])->name('provider.profile.update');

        // Services CRUD
        Route::get('/services', [ProviderServiceController::class, 'index'])->name('provider.services');
        Route::get('/services/create', [ProviderServiceController::class, 'create'])->name('provider.services.create');
        Route::post('/services', [ProviderServiceController::class, 'store'])->name('provider.services.store');
        Route::get('/services/{id}/edit', [ProviderServiceController::class, 'edit'])->name('provider.services.edit');
        Route::put('/services/{id}', [ProviderServiceController::class, 'update'])->name('provider.services.update');
        Route::delete('/services/{id}', [ProviderServiceController::class, 'destroy'])->name('provider.services.destroy');
        Route::post('/services/{id}/toggle-status', [ProviderServiceController::class, 'toggleStatus'])->name('provider.services.toggle');

        // Bookings
        Route::get('/bookings', [ProviderBookingController::class, 'index'])->name('provider.bookings');
        Route::get('/bookings/{id}', [ProviderBookingController::class, 'show'])->name('provider.bookings.show');
        Route::post('/bookings/{id}/confirm', [ProviderBookingController::class, 'confirm'])->name('provider.bookings.confirm');
        Route::post('/bookings/{id}/reject', [ProviderBookingController::class, 'reject'])->name('provider.bookings.reject');
        Route::post('/bookings/{id}/complete', [ProviderBookingController::class, 'complete'])->name('provider.bookings.complete');

        // Availability
        Route::get('/availability', [ProviderAvailabilityController::class, 'index'])->name('provider.availability');
        Route::put('/availability', [ProviderAvailabilityController::class, 'update'])->name('provider.availability.update');

        // Reviews
        Route::get('/reviews', [ProviderReviewController::class, 'index'])->name('provider.reviews.index');
        Route::post('/reviews/{id}/reply', [ProviderReviewController::class, 'reply'])->name('provider.reviews.reply');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Provider\ProviderNotificationController::class, 'index'])->name('provider.notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Provider\ProviderNotificationController::class, 'markRead'])->name('provider.notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Provider\ProviderNotificationController::class, 'markAllRead'])->name('provider.notifications.readAll');
    });

    // Admin (protected by role middleware)
    Route::middleware(\App\Http\Middleware\EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
        Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle');

        // Service approval
        Route::get('/services', [AdminServiceController::class, 'index'])->name('admin.services');
        Route::post('/services/{id}/approve', [AdminServiceController::class, 'approve'])->name('admin.services.approve');
        Route::post('/services/{id}/reject', [AdminServiceController::class, 'reject'])->name('admin.services.reject');

        // Bookings
        Route::get('/bookings', [\App\Http\Controllers\Admin\AdminBookingController::class, 'index'])->name('admin.bookings');
        Route::post('/bookings/{id}/force-confirm', [\App\Http\Controllers\Admin\AdminBookingController::class, 'forceConfirm'])->name('admin.bookings.force-confirm');
        Route::post('/bookings/{id}/force-complete', [\App\Http\Controllers\Admin\AdminBookingController::class, 'forceComplete'])->name('admin.bookings.force-complete');
        Route::post('/bookings/{id}/force-reject', [\App\Http\Controllers\Admin\AdminBookingController::class, 'forceReject'])->name('admin.bookings.force-reject');

        // Stats
        Route::get('/stats', [AdminStatsController::class, 'index'])->name('admin.stats');

        // Reviews
        Route::get('/reviews', [\App\Http\Controllers\Admin\AdminReviewController::class, 'index'])->name('admin.reviews');
        Route::delete('/reviews/{id}', [\App\Http\Controllers\Admin\AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    });
});

require __DIR__.'/settings.php';
