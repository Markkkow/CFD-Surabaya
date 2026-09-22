<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pendaftaran_tenant')) {
            return;
        }

        Schema::table('pendaftaran_tenant', function (Blueprint $table) {
            if (! Schema::hasColumn('pendaftaran_tenant', 'nama_produk')) {
                $table->string('nama_produk', 150)->nullable()->after('id_lapak');
            }
            if (! Schema::hasColumn('pendaftaran_tenant', 'jenis_produk')) {
                $table->string('jenis_produk', 100)->nullable()->after('nama_produk');
            }
            if (! Schema::hasColumn('pendaftaran_tenant', 'jumlah_produk')) {
                $table->unsignedInteger('jumlah_produk')->nullable()->after('jenis_produk');
            }
            if (! Schema::hasColumn('pendaftaran_tenant', 'keterangan_tambahan')) {
                $table->text('keterangan_tambahan')->nullable()->after('jumlah_produk');
            }
            if (! Schema::hasColumn('pendaftaran_tenant', 'catatan_admin')) {
                $table->string('catatan_admin', 255)->nullable()->after('status_pendaftaran');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pendaftaran_tenant')) {
            return;
        }

        Schema::table('pendaftaran_tenant', function (Blueprint $table) {
            foreach (['nama_produk', 'jenis_produk', 'jumlah_produk', 'keterangan_tambahan', 'catatan_admin'] as $column) {
                if (Schema::hasColumn('pendaftaran_tenant', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
