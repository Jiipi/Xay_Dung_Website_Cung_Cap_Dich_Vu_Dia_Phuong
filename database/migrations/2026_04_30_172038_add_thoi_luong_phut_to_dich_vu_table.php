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
        Schema::table('dich_vu', function (Blueprint $table) {
            $table->integer('thoi_luong_phut')->default(120)->after('don_vi_gia')->comment('Thời lượng dịch vụ (phút)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dich_vu', function (Blueprint $table) {
            $table->dropColumn('thoi_luong_phut');
        });
    }
};
