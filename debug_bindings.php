<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check if bindings exist
echo "=== Checking Container Bindings ===" . PHP_EOL;

$bindings = [
    \App\Repositories\Contracts\Category\CategoryRepositoryInterface::class,
    \App\Repositories\Contracts\Booking\BookingRepositoryInterface::class,
    \App\Repositories\Contracts\Review\ReviewRepositoryInterface::class,
    \App\Repositories\Contracts\Service\ServiceRepositoryInterface::class,
];

foreach ($bindings as $interface) {
    $bound = $app->bound($interface);
    echo ($bound ? '✓' : '✗') . " {$interface}: " . ($bound ? 'BOUND' : 'NOT BOUND') . PHP_EOL;
}

echo PHP_EOL . "=== Trying to resolve ===" . PHP_EOL;

foreach ($bindings as $interface) {
    try {
        $instance = $app->make($interface);
        echo "✓ " . get_class($instance) . PHP_EOL;
    } catch (\Throwable $e) {
        echo "✗ ERROR: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "=== Registered Providers ===" . PHP_EOL;
$providers = $app->getLoadedProviders();
foreach ($providers as $provider => $loaded) {
    if (str_contains($provider, 'App\\')) {
        echo ($loaded ? '✓' : '✗') . " {$provider}" . PHP_EOL;
    }
}
