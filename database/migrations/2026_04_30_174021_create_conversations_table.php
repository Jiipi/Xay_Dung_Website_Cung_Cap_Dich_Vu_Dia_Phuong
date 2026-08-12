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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khach_hang_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('nha_cung_cap_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('don_dat_lich_id')->nullable()->constrained('don_dat_lich')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
