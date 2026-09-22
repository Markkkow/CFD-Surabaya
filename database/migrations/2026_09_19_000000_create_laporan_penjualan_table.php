<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('laporan_penjualan')) {
            // Tabel versi lama akan dilengkapi oleh migration repair berikutnya.
            return;
        }

        Schema::create('laporan_penjualan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('id_pendaftaran')->unique()
                ->constrained('pendaftaran_tenant', 'id_pendaftaran')->cascadeOnDelete();
            $table->unsignedInteger('jumlah_terjual');
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->decimal('total_modal', 15, 2)->default(0);
            $table->text('catatan_penjualan')->nullable();
            $table->timestamp('tanggal_laporan')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_penjualan');
    }
};
