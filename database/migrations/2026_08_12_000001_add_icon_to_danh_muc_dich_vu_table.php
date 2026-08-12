<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('danh_muc_dich_vu', function (Blueprint $table): void {
            $table->string('icon', 255)->nullable()->after('mo_ta');
        });
    }

    public function down(): void
    {
        Schema::table('danh_muc_dich_vu', function (Blueprint $table): void {
            $table->dropColumn('icon');
        });
    }
};
