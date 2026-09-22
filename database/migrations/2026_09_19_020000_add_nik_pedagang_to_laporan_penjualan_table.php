<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('laporan_penjualan') && !Schema::hasColumn('laporan_penjualan', 'nik_pedagang')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->string('nik_pedagang', 16)->nullable()->after('id_laporan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('laporan_penjualan') && Schema::hasColumn('laporan_penjualan', 'nik_pedagang')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->dropColumn('nik_pedagang');
            });
        }
    }
};
