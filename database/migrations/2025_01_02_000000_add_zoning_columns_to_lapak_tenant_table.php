<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration ini KHUSUS untuk kasus tabel 'lapak_tenant' sudah ada duluan
     * (misal dibuat dari file .sql bawaan tugas UTS) sehingga tidak sempat
     * dibuat lewat migration create_lapak_tenant_table di atas.
     * Di sini kita tambahkan kolom zonasi kalau belum ada, lalu bagikan
     * data lapak yang sudah ada ke 4 zona secara otomatis.
     */
    public function up(): void
    {
        if (! Schema::hasTable('lapak_tenant')) {
            return;
        }

        Schema::table('lapak_tenant', function (Blueprint $table) {
            if (! Schema::hasColumn('lapak_tenant', 'kategori_lapak')) {
                $table->string('kategori_lapak', 50)->nullable()->after('id_event');
            }
            if (! Schema::hasColumn('lapak_tenant', 'baris')) {
                $table->string('baris', 5)->nullable()->after('kategori_lapak');
            }
            if (! Schema::hasColumn('lapak_tenant', 'kolom')) {
                $table->unsignedInteger('kolom')->nullable()->after('baris');
            }
        });

        $this->backfillZoning();
    }

    public function down(): void
    {
        if (! Schema::hasTable('lapak_tenant')) {
            return;
        }

        Schema::table('lapak_tenant', function (Blueprint $table) {
            foreach (['kategori_lapak', 'baris', 'kolom'] as $column) {
                if (Schema::hasColumn('lapak_tenant', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Bagi rata lapak yang sudah ada (dan belum punya kategori_lapak) ke 4
     * zona secara bergiliran, lalu susun baris/kolom otomatis per zona per
     * event supaya denah "kursi bioskop" tetap terbentuk walau data lapak
     * sudah lebih dulu diisi manual (mis. lewat SQL/phpMyAdmin).
     */
    private function backfillZoning(): void
    {
        $zonas = ['Kuliner', 'Fashion', 'Kerajinan', 'Jasa & Lainnya'];
        $kursiPerBaris = 10;

        $rows = DB::table('lapak_tenant')
            ->whereNull('kategori_lapak')
            ->orderBy('id_event')
            ->orderBy('id_lapak')
            ->get();

        $posisiPerZona = []; // key: "id_event|zona" => posisi urut (0-based) di zona itu

        foreach ($rows as $index => $row) {
            $zona = $zonas[$index % count($zonas)];
            $key = $row->id_event . '|' . $zona;
            $posisiPerZona[$key] = ($posisiPerZona[$key] ?? -1) + 1;
            $posisi = $posisiPerZona[$key];

            $barisIndex = intdiv($posisi, $kursiPerBaris); // 0, 1, 2, ...
            $kolom = ($posisi % $kursiPerBaris) + 1;
            $baris = chr(65 + ($barisIndex % 26)); // 0 -> A, 1 -> B, dst

            DB::table('lapak_tenant')->where('id_lapak', $row->id_lapak)->update([
                'kategori_lapak' => $zona,
                'baris' => $baris,
                'kolom' => $kolom,
                // Nomor lapak asli dipertahankan kalau sudah ada isinya,
                // supaya data lama tidak berubah tampilannya.
                'nomor_lapak' => $row->nomor_lapak ?: ($baris . $kolom),
            ]);
        }
    }
};
