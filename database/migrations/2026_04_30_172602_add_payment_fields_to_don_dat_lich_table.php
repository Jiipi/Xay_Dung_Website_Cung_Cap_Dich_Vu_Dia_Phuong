<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('don_dat_lich', function (Blueprint $table) {
            $table->decimal('tien_coc', 15, 2)->default(0)->after('tong_tien')->comment('Số tiền cần đặt cọc');
            $table->decimal('phi_nen_tang', 15, 2)->default(0)->after('tien_coc')->comment('Phí thu nền tảng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_dat_lich', function (Blueprint $table) {
            $table->dropColumn(['tien_coc', 'phi_nen_tang']);
        });
    }
};
