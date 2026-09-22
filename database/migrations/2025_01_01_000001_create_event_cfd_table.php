<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_cfd')) {
            return;
        }

        Schema::create('event_cfd', function (Blueprint $table) {
            $table->id('id_event');
            $table->string('nama_event', 150);
            $table->date('tanggal_event');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('lokasi', 150);
            $table->string('status_event', 30)->default('Aktif');
            $table->date('tanggal_buka_pendaftaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_cfd');
    }
};
