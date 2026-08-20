#!/bin/bash
set -euo pipefail

cd /var/www

echo "==> Bootstrapping Dalat Services..."

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is empty. Set APP_KEY in Render Environment."
    echo "       Local: php artisan key:generate --show"
    echo "       Value MUST look like: base64:xxxxxxxx (Laravel format)."
    echo "       Render 'generateValue' secrets are NOT valid Laravel APP_KEY values."
    exit 1
fi

# Laravel refuses / mis-encrypts cookies when APP_KEY is not base64:<32-bytes>.
# Render dashboard "generate" buttons often create a raw random string → every
# web route 500s with an empty csrf-token while /up still returns 200.
case "${APP_KEY}" in
    base64:*)
        key_body="${APP_KEY#base64:}"
        # rough length check: 32 raw bytes → 44 base64 chars (with padding)
        if [ "${#key_body}" -lt 40 ]; then
            echo "ERROR: APP_KEY looks too short after base64: prefix (${#key_body} chars)."
            echo "       Run locally: php artisan key:generate --show"
            exit 1
        fi
        ;;
    *)
        echo "ERROR: APP_KEY must start with 'base64:' (Laravel format)."
        echo "       Current prefix is not base64:. Render generateValue is NOT enough."
        echo "       Run locally: php artisan key:generate --show"
        echo "       Paste the full value into Render → Environment → APP_KEY"
        exit 1
        ;;
esac

if [ -z "${DB_URL:-}${DATABASE_URL:-}${DB_HOST:-}" ]; then
    echo "ERROR: No database configuration found (DB_URL / DATABASE_URL / DB_HOST)."
    exit 1
fi

# Default to file sessions on free tier unless the operator overrides.
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"

# Fail fast if the database is unreachable — homepage/services still query DB.
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

# Ensure runtime dirs are writable for file-based sessions/cache/logs.
# php-fpm runs as www-data — root-only perms cause empty CSRF + 500 on every page.
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

echo "==> Caching configuration..."
# Drop stale caches before rebuild so APP_URL / SESSION_DRIVER fixes always apply.
php artisan config:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan config:cache
php artisan view:cache
# route:cache fails when routes contain closures (dashboard redirect) — skip safely
php artisan route:cache 2>/dev/null || echo "Skipping route:cache (closures present or cache failed)"

# Prove session driver works AS www-data (same user as php-fpm).
echo "==> Verifying session driver as www-data (${SESSION_DRIVER})..."
su -s /bin/sh www-data -c 'php -r "
require \"vendor/autoload.php\";
\$app = require \"bootstrap/app.php\";
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
try {
    // Prove APP_KEY can encrypt (invalid keys fail here).
    \$enc = \$app->make(\"encrypter\");
    \$enc->encryptString(\"boot-check\");
    \$store = \$app->make(\"session\")->driver();
    \$store->start();
    \$token = \$store->token();
    if (! is_string(\$token) || \$token === \"\") {
        throw new RuntimeException(\"Session started but CSRF token is empty\");
    }
    echo \"Session OK (driver=\".config(\"session.driver\").\", token length \".strlen(\$token).\")\n\";
} catch (Throwable \$e) {
    fwrite(STDERR, \"ERROR: Session/APP_KEY failed under www-data: \".\$e->getMessage().PHP_EOL);
    exit(1);
}
"'

echo "==> Boot config: APP_URL=${APP_URL:-<empty>} APP_ENV=${APP_ENV:-<empty>} SESSION_DRIVER=${SESSION_DRIVER} CACHE_STORE=${CACHE_STORE}"
echo "==> APP_KEY format: OK (base64:...)"

echo "==> Starting Supervisord (Nginx + PHP-FPM)..."
exec "$@"
