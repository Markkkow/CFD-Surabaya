<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id('id_admin');
                $table->string('nama_admin', 100);
                $table->string('username', 50)->unique();
                $table->string('password');
            });
        }

        // Buat akun admin default kalau belum ada, supaya bisa langsung login
        // tanpa perlu jalankan seeder terpisah.
        $sudahAda = DB::table('admins')->where('username', 'admin')->exists();

        if (! $sudahAda) {
            DB::table('admins')->insert([
                'nama_admin' => 'Administrator CFD',
                'username' => 'admin',
                'password' => Hash::make('037545'),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
