#!/bin/bash
set -e

cd /var/www

# Run database migrations on startup
echo "==> Running database migrations..."
php artisan migrate --force --isolated || echo "Migration warning: could not run migrations immediately"

# Storage link
php artisan storage:link || true

# Production caches
echo "==> Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting Supervisord (Nginx + PHP-FPM)..."
exec "$@"
