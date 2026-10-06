<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inbox_pedagang')) {
            return;
        }

        Schema::create('inbox_pedagang', function (Blueprint $table) {
            $table->id('id_inbox');

            $table->string('nik_pedagang', 16)
                ->collation('utf8mb4_0900_ai_ci');

            $table->string('tipe', 30);
            $table->string('judul', 150);
            $table->text('pesan');

            // Harus sama dengan pendaftaran_tenant.id_pendaftaran
            $table->integer('id_pendaftaran')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('nik_pedagang')
                ->references('nik_pedagang')
                ->on('pedagang')
                ->cascadeOnDelete();

            $table->foreign('id_pendaftaran')
                ->references('id_pendaftaran')
                ->on('pendaftaran_tenant')
                ->nullOnDelete();

            $table->index('nik_pedagang');
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbox_pedagang');
    }
};