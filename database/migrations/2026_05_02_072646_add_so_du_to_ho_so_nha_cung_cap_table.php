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
        Schema::table('ho_so_nha_cung_cap', function (Blueprint $table) {
            $table->decimal('so_du', 15, 2)->default(0)->after('diem_danh_gia')->comment('Số dư ví của nhà cung cấp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ho_so_nha_cung_cap', function (Blueprint $table) {
            $table->dropColumn('so_du');
        });
    }
};
