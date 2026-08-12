<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('yeu_cau_rut_tien', function (Blueprint $table) {
            $table
                ->foreignId('giao_dich_id')
                ->nullable()
                ->unique()
                ->constrained('giao_dich')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('yeu_cau_rut_tien', function (Blueprint $table) {
            $table->dropConstrainedForeignId('giao_dich_id');
        });
    }
};
