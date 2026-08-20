#!/bin/bash
set -euo pipefail

cd /var/www

echo "==> Bootstrapping Dalat Services..."

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is empty. Set APP_KEY in Render Environment (php artisan key:generate --show)."
    exit 1
fi

if [ -z "${DB_URL:-}${DATABASE_URL:-}${DB_HOST:-}" ]; then
    echo "ERROR: No database configuration found (DB_URL / DATABASE_URL / DB_HOST)."
    exit 1
fi

# Fail fast if the database is unreachable — web routes need sessions/cache tables.
echo "==> Checking database connection..."
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Database OK\n";
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: Database connection failed: ".$e->getMessage().PHP_EOL);
    exit(1);
}
'

echo "==> Running database migrations..."
php artisan migrate --force --isolated

# Storage link (ignore if already linked)
php artisan storage:link 2>/dev/null || true

# Ensure runtime dirs are writable for file-based fallbacks/logs
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

echo "==> Caching configuration..."
php artisan config:cache
php artisan view:cache
# route:cache fails when routes contain closures (dashboard redirect) — skip safely
php artisan route:cache 2>/dev/null || echo "Skipping route:cache (closures present or cache failed)"

echo "==> Starting Supervisord (Nginx + PHP-FPM)..."
exec "$@"
