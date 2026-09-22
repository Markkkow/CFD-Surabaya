<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kalau tabel 'lapak_tenant' sudah ada duluan (skema lama dari .sql
        // bawaan tugas), JANGAN dibuat ulang di sini. Kolom zonasi untuk
        // kasus itu ditangani oleh migration
        // 2025_01_02_000000_add_zoning_columns_to_lapak_tenant_table.php
        if (Schema::hasTable('lapak_tenant')) {
            return;
        }

        Schema::create('lapak_tenant', function (Blueprint $table) {
            $table->id('id_lapak');
            $table->foreignId('id_event')->constrained('event_cfd', 'id_event')->cascadeOnDelete();

            // Zonasi ala "bioskop": setiap kategori dagangan punya area sendiri,
            // di dalam area itu lapak disusun per baris (A, B, C, ...) & kolom (nomor kursi).
            $table->string('kategori_lapak', 50);
            $table->string('baris', 5);
            $table->unsignedInteger('kolom');
            $table->string('nomor_lapak', 10);

            $table->string('lokasi_lapak', 150)->nullable();
            $table->string('ukuran_lapak', 30);
            $table->string('status_lapak', 20)->default('Tersedia'); // Tersedia | Dipesan | Perbaikan

            $table->unique(['id_event', 'nomor_lapak']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lapak_tenant');
    }
};
