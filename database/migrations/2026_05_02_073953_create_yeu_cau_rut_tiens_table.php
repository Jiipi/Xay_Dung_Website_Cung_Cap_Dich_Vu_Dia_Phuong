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
        Schema::create('yeu_cau_rut_tien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->decimal('so_tien', 15, 2);
            $table->string('trang_thai')->default('cho_xu_ly')->comment('cho_xu_ly, da_duyet, tu_choi');
            $table->text('ghi_chu')->nullable();
            $table->text('admin_ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_rut_tien');
    }
};
