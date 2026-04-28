<?php
if (function_exists('opcache_get_status')) {
    $s = opcache_get_status(false);
    echo 'opcache_enabled: ' . ($s['opcache_enabled'] ? 'yes' : 'no') . PHP_EOL;
    echo 'cached_scripts: ' . ($s['opcache_statistics']['num_cached_scripts'] ?? 0) . PHP_EOL;
} else {
    echo 'opcache not available' . PHP_EOL;
}

echo 'opcache.enable: ' . ini_get('opcache.enable') . PHP_EOL;
echo 'opcache.enable_cli: ' . ini_get('opcache.enable_cli') . PHP_EOL;
echo 'opcache.revalidate_freq: ' . ini_get('opcache.revalidate_freq') . PHP_EOL;
echo 'opcache.validate_timestamps: ' . ini_get('opcache.validate_timestamps') . PHP_EOL;
