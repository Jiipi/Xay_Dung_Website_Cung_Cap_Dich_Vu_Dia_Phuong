<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('don_dat_lich', function (Blueprint $table) {
            $table->string('huy_boi', 20)->nullable()->after('trang_thai_don');
            $table->text('ly_do_huy')->nullable()->after('huy_boi');
        });
    }

    public function down(): void
    {
        Schema::table('don_dat_lich', function (Blueprint $table) {
            $table->dropColumn(['huy_boi', 'ly_do_huy']);
        });
    }
};
