#!/bin/bash

# Navigate to app directory
cd /var/www

# Ensure correct permissions
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Install PHP dependencies if vendor doesn't exist
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Install Node dependencies and build assets if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "Installing Node dependencies and building assets..."
    npm ci
    npm run build
fi

# Set up .env file if it doesn't exist
if [ ! -f ".env" ]; then
    if [ -f ".env.docker" ]; then
        cp .env.docker .env
    elif [ -f ".env.example" ]; then
        cp .env.example .env
    fi
    php artisan key:generate
fi

# Note: We're not automatically running migrations here to prevent 
# overwriting data if this container restarts. Run it manually:
# docker compose exec app php artisan migrate

if [ "${APP_ENV}" = "production" ]; then
    echo "Caching Laravel config/routes/views for production..."
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    echo "Laravel production caching complete."
else
    echo "Clearing Laravel optimize caches for local/testing..."
    php artisan optimize:clear 2>/dev/null || true
    echo "Laravel local/testing caches cleared."
fi

# Execute CMD
exec "$@"
