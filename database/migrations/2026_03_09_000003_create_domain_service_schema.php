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
        // ho_so_nha_cung_cap: 1-1 with nguoi_dung, id IS the FK
        Schema::create('ho_so_nha_cung_cap', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')->references('id')->on('nguoi_dung')->cascadeOnDelete();
            $table->string('ten_thuong_hieu', 120);
            $table->text('gioi_thieu')->nullable();
            $table->smallInteger('nam_kinh_nghiem')->default(0);
            $table->string('website', 2048)->nullable();
            $table->string('facebook', 2048)->nullable();
            $table->string('giay_phep_kinh_doanh', 2048)->nullable();
            $table->string('stk_ngan_hang', 20)->nullable();
            $table->string('ten_ngan_hang', 50)->nullable();
            $table->string('ten_chu_tk', 50)->nullable();
            $table->decimal('ty_le_hoa_hong', 5, 2)->default(0);
            $table->decimal('diem_danh_gia', 3, 2)->default(0);
            $table->timestampsTz();
        });

        // danh_muc_dich_vu: self-referencing hierarchy
        Schema::create('danh_muc_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('danh_muc_dich_vu')->cascadeOnDelete();
            $table->string('ten_danh_muc');
            $table->string('slug')->unique();
            $table->text('mo_ta')->nullable();
            $table->string('anh_dai_dien', 2048)->nullable();
            $table->integer('thu_tu_hien_thi')->default(0);
            $table->string('trang_thai', 50)->default('hoat_dong')->index();
            $table->timestampsTz();

            $table->index('parent_id');
        });

        // dich_vu: main service listing
        Schema::create('dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nha_cung_cap_id')->constrained('nguoi_dung')->restrictOnDelete();
            $table->foreignId('danh_muc_id')->constrained('danh_muc_dich_vu')->restrictOnDelete();
            $table->string('ten_dich_vu');
            $table->string('slug');
            $table->text('mo_ta_chi_tiet')->nullable();
            $table->decimal('gia_tu', 12, 2)->nullable();
            $table->decimal('gia_den', 12, 2)->nullable();
            $table->string('don_vi_gia', 50)->nullable();
            $table->string('dia_chi_hien_thi', 500)->nullable();
            $table->foreignId('tinh_thanh_id')->nullable()->constrained('don_vi_hanh_chinh')->nullOnDelete();
            $table->foreignId('quan_huyen_id')->nullable()->constrained('don_vi_hanh_chinh')->nullOnDelete();
            $table->decimal('toa_do_lat', 10, 7)->nullable();
            $table->decimal('toa_do_lng', 10, 7)->nullable();
            $table->jsonb('danh_sach_anh')->nullable();
            $table->jsonb('the_tu_khoa')->nullable();
            $table->jsonb('khu_vuc_phuc_vu')->nullable();
            $table->jsonb('thuoc_tinh')->nullable();
            $table->jsonb('lich_lam_viec')->nullable();
            $table->integer('do_uu_tien')->default(0);
            $table->string('trang_thai_duyet', 50)->default('cho_duyet')->index();
            $table->string('trang_thai_hoat_dong', 50)->default('hoat_dong')->index();
            $table->timestampsTz();

            $table->unique(['nha_cung_cap_id', 'slug']);
            $table->index('quan_huyen_id');
        });

        // don_dat_lich: booking + payment merged
        Schema::create('don_dat_lich', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don', 50)->unique();
            $table->foreignId('khach_hang_id')->constrained('nguoi_dung')->restrictOnDelete();
            $table->foreignId('nha_cung_cap_id')->constrained('nguoi_dung')->restrictOnDelete();
            $table->foreignId('dich_vu_id')->constrained('dich_vu')->restrictOnDelete();
            $table->timestampTz('thoi_gian_thuc_hien')->nullable();
            $table->decimal('so_luong', 10, 2)->default(1);
            $table->string('don_vi', 50)->nullable();
            $table->string('dia_diem_thuc_hien', 500)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->string('ma_khuyen_mai', 100)->nullable();
            $table->decimal('tam_tinh', 12, 2)->default(0);
            $table->decimal('phi_dich_vu', 12, 2)->default(0);
            $table->decimal('giam_gia', 12, 2)->default(0);
            $table->decimal('tong_tien', 12, 2)->default(0);
            $table->string('trang_thai_don', 50)->default('cho_xac_nhan')->index();
            $table->string('phuong_thuc_thanh_toan', 50)->nullable();
            $table->string('trang_thai_thanh_toan', 50)->default('cho_thanh_toan')->index();
            $table->string('ma_giao_dich_doi_tac', 255)->nullable();
            $table->timestampsTz();

            $table->index(['khach_hang_id', 'trang_thai_don']);
            $table->index(['nha_cung_cap_id', 'trang_thai_don']);
        });

        // danh_gia: reviews with provider reply merged
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_dat_lich_id')->unique()->constrained('don_dat_lich')->cascadeOnDelete();
            $table->foreignId('nha_cung_cap_id')->constrained('nguoi_dung')->cascadeOnDelete();
            $table->foreignId('khach_hang_id')->constrained('nguoi_dung')->cascadeOnDelete();
            $table->smallInteger('so_sao');
            $table->text('noi_dung')->nullable();
            $table->boolean('an_danh')->default(false);
            $table->text('phan_hoi_tu_ncc')->nullable();
            $table->timestampTz('ngay_phan_hoi')->nullable();
            $table->timestampsTz();

            $table->index(['nha_cung_cap_id', 'so_sao']);
        });

        // thong_bao: in-app notifications
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->string('loai_thong_bao', 100)->index();
            $table->boolean('da_doc')->default(false)->index();
            $table->timestampTz('created_at')->useCurrent();
        });

        // yeu_thich: user favorites
        Schema::create('yeu_thich', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->cascadeOnDelete();
            $table->foreignId('dich_vu_id')->constrained('dich_vu')->cascadeOnDelete();
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['nguoi_dung_id', 'dich_vu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_thich');
        Schema::dropIfExists('thong_bao');
        Schema::dropIfExists('danh_gia');
        Schema::dropIfExists('don_dat_lich');
        Schema::dropIfExists('dich_vu');
        Schema::dropIfExists('danh_muc_dich_vu');
        Schema::dropIfExists('ho_so_nha_cung_cap');
    }
};
