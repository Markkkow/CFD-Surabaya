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
            if (! Schema::hasColumn('pendaftaran_tenant', 'foto_produk')) {
                $table->string('foto_produk', 255)->nullable()->after('keterangan_tambahan');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pendaftaran_tenant')) {
            return;
        }

        Schema::table('pendaftaran_tenant', function (Blueprint $table) {
            if (Schema::hasColumn('pendaftaran_tenant', 'foto_produk')) {
                $table->dropColumn('foto_produk');
            }
        });
    }
};
