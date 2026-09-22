<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('laporan_penjualan')) {
            Schema::create('laporan_penjualan', function (Blueprint $table) {
                $table->id('id_laporan');
                $table->unsignedBigInteger('id_pendaftaran')->nullable()->unique();
                $table->unsignedInteger('jumlah_terjual')->default(0);
                $table->decimal('total_pendapatan', 15, 2)->default(0);
                $table->decimal('total_modal', 15, 2)->default(0);
                $table->text('catatan_penjualan')->nullable();
                $table->timestamp('tanggal_laporan')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable();
            });

            return;
        }

        // Tambahkan kolom satu per satu agar kompatibel dengan tabel laporan_penjualan
        // versi lama yang mungkin sudah ada di database pengguna.
        if (! Schema::hasColumn('laporan_penjualan', 'id_pendaftaran')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->unsignedBigInteger('id_pendaftaran')->nullable()->after('id_laporan');
            });
        }

        if (! Schema::hasColumn('laporan_penjualan', 'jumlah_terjual')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->unsignedInteger('jumlah_terjual')->default(0);
            });
        }

        if (! Schema::hasColumn('laporan_penjualan', 'total_pendapatan')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->decimal('total_pendapatan', 15, 2)->default(0);
            });
        }

        if (! Schema::hasColumn('laporan_penjualan', 'total_modal')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->decimal('total_modal', 15, 2)->default(0);
            });
        }

        if (! Schema::hasColumn('laporan_penjualan', 'catatan_penjualan')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->text('catatan_penjualan')->nullable();
            });
        }

        if (! Schema::hasColumn('laporan_penjualan', 'tanggal_laporan')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->timestamp('tanggal_laporan')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasColumn('laporan_penjualan', 'updated_at')) {
            Schema::table('laporan_penjualan', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }

        // Foreign key sengaja tidak dipaksakan di migration repair ini karena tabel lama
        // dapat memiliki engine/index berbeda. Relasi Eloquent tetap bekerja melalui id_pendaftaran.
        // Validitas id_pendaftaran tetap dijaga oleh flow aplikasi.
    }

    public function down(): void
    {
        // Migration repair tidak menghapus kolom saat rollback agar data lama tidak ikut hilang.
    }
};
