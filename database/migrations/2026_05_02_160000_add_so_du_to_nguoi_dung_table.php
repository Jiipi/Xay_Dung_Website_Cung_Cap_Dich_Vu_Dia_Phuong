<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->decimal('so_du', 15, 2)->default(0)->after('toa_do_lng')->comment('Số dư ví của người dùng');
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('so_du');
        });
    }
};
