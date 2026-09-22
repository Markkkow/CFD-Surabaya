<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pedagang')) {
            return;
        }

        Schema::table('pedagang', function (Blueprint $table) {
            if (! Schema::hasColumn('pedagang', 'ktp_pedagang')) {
                $table->string('ktp_pedagang', 255)->nullable()->after('alamat');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pedagang')) {
            return;
        }

        Schema::table('pedagang', function (Blueprint $table) {
            if (Schema::hasColumn('pedagang', 'ktp_pedagang')) {
                $table->dropColumn('ktp_pedagang');
            }
        });
    }
};
