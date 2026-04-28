<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::whereHas('vaiTroNguoiDung', function($q) {
    $q->where('ten_vai_tro', 'Nhà cung cấp');
})->first(['id', 'email', 'ho_ten']);

echo json_encode($user, JSON_UNESCAPED_UNICODE) . PHP_EOL;
