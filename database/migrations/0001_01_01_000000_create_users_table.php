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
        Schema::create('don_vi_hanh_chinh', function (Blueprint $table) {
            $table->id();
            $table->string('ten', 120);
            $table->string('cap_don_vi', 20);  // tinh | huyen | xa
            $table->foreignId('parent_id')->nullable()->constrained('don_vi_hanh_chinh')->nullOnDelete();
            $table->string('ma_hanh_chinh', 20)->nullable();
            $table->timestampsTz();
        });

        Schema::create('vai_tro_nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ten_vai_tro', 120);
            $table->string('mo_ta', 255)->nullable();
            $table->string('quyen')->nullable();
        });

        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('so_dien_thoai', 20)->nullable()->unique();
            $table->string('mat_khau_hash');
            $table->foreignId('vai_tro')->constrained('vai_tro_nguoi_dung')->restrictOnDelete();
            $table->string('trang_thai', 50)->default('hoat_dong')->index();
            $table->string('anh_dai_dien', 2048)->nullable();
            $table->string('dia_chi_chi_tiet', 500)->nullable();
            $table->foreignId('phuong_xa_id')->nullable()->constrained('don_vi_hanh_chinh')->nullOnDelete();
            $table->foreignId('quan_huyen_id')->nullable()->constrained('don_vi_hanh_chinh')->nullOnDelete();
            $table->foreignId('tinh_thanh_id')->nullable()->constrained('don_vi_hanh_chinh')->nullOnDelete();
            $table->timestampTz('email_da_xac_minh')->nullable();
            $table->timestampTz('lan_dang_nhap_cuoi')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->string('gioi_tinh', 10)->nullable();
            $table->decimal('toa_do_lat', 10, 7)->nullable();
            $table->decimal('toa_do_lng', 10, 7)->nullable();
            $table->timestampsTz();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestampTz('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('nguoi_dung');
        Schema::dropIfExists('vai_tro_nguoi_dung');
        Schema::dropIfExists('don_vi_hanh_chinh');
    }
};
