<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pendaftaran_tenant')) {
            return;
        }

        Schema::create('pendaftaran_tenant', function (Blueprint $table) {
            $table->id('id_pendaftaran');
            $table->string('nik_pedagang', 16);
            $table->foreignId('id_event')->constrained('event_cfd', 'id_event')->cascadeOnDelete();
            $table->foreignId('id_lapak')->constrained('lapak_tenant', 'id_lapak')->cascadeOnDelete();
            $table->timestamp('tanggal_pendaftaran')->useCurrent();
            $table->string('status_pendaftaran', 30)->default('Menunggu Verifikasi');

            $table->foreign('nik_pedagang')->references('nik_pedagang')->on('pedagang')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_tenant');
    }
};
