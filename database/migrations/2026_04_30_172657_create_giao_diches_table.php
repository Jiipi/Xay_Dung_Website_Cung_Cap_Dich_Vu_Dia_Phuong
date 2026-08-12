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
        Schema::create('giao_dich', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('don_dat_lich_id')->nullable()->constrained('don_dat_lich')->onDelete('set null');
            $table->string('loai_giao_dich')->comment('nap_tien, rut_tien, thanh_toan_coc, thu_phi_nen_tang, hoan_tien');
            $table->decimal('so_tien', 15, 2);
            $table->string('phuong_thuc')->nullable()->comment('vnpay, momo, ngan_hang, vi_noi_bo');
            $table->string('ma_giao_dich_doi_tac')->nullable();
            $table->string('trang_thai')->default('cho_xu_ly')->comment('cho_xu_ly, thanh_cong, that_bai');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giao_dich');
    }
};
