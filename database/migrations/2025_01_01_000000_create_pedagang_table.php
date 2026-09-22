<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan meskipun tabel 'pedagang' sudah ada duluan
        // (misalnya dibuat lewat file .sql terpisah, bukan lewat migration ini).
        if (Schema::hasTable('pedagang')) {
            return;
        }

        Schema::create('pedagang', function (Blueprint $table) {
            $table->string('nik_pedagang', 16)->primary();
            $table->string('nama_pedagang', 100);
            $table->string('nama_usaha', 100);
            $table->string('sosial_media_usaha', 100)->nullable();
            $table->string('no_telepon', 20);
            $table->string('email', 100);
            $table->text('alamat');
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('status_verifikasi', 30)->default('Belum Verifikasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedagang');
    }
};
