<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('produk')) {
            Schema::create('produk', function (Blueprint $table) {
                $table->id('id_produk');
                $table->string('nik_pedagang', 16);
                $table->string('nama_produk', 100);
                $table->string('kategori_produk', 50);
                $table->unsignedBigInteger('harga')->default(0);
                $table->unsignedInteger('stok_produk')->default(0);
                $table->text('deskripsi_produk')->nullable();
                $table->string('foto_produk', 255)->nullable();
                $table->foreign('nik_pedagang')->references('nik_pedagang')->on('pedagang')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('pendaftaran_tenant') && ! Schema::hasColumn('pendaftaran_tenant', 'id_produk')) {
            Schema::table('pendaftaran_tenant', function (Blueprint $table) {
                $table->unsignedBigInteger('id_produk')->nullable()->after('id_lapak');
                $table->foreign('id_produk')->references('id_produk')->on('produk')->nullOnDelete();
            });
        }

        // Migrasikan data produk lama dari pendaftaran ke tabel PRODUK.
        if (Schema::hasTable('pendaftaran_tenant') && Schema::hasTable('produk')) {
            $rows = DB::table('pendaftaran_tenant')
                ->whereNull('id_produk')
                ->whereNotNull('nama_produk')
                ->get();

            foreach ($rows as $row) {
                $idProduk = DB::table('produk')->insertGetId([
                    'nik_pedagang' => $row->nik_pedagang,
                    'nama_produk' => $row->nama_produk,
                    'kategori_produk' => $row->jenis_produk ?: 'Lainnya',
                    'harga' => 0,
                    'stok_produk' => (int) ($row->jumlah_produk ?? 0),
                    'deskripsi_produk' => $row->keterangan_tambahan,
                    'foto_produk' => $row->foto_produk,
                ]);

                DB::table('pendaftaran_tenant')
                    ->where('id_pendaftaran', $row->id_pendaftaran)
                    ->update(['id_produk' => $idProduk]);
            }
        }

        if (! Schema::hasTable('perizinan')) {
            Schema::create('perizinan', function (Blueprint $table) {
                $table->id('id_perizinan');
                $table->string('nik_pedagang', 16);
                $table->unsignedBigInteger('id_event');
                $table->string('jenis_perizinan', 100);
                $table->date('tanggal_pengajuan');
                $table->date('tanggal_berlaku');
                $table->string('status_perizinan', 50)->default('Menunggu Verifikasi');
                $table->string('dokumen_perizinan', 255);
                $table->foreign('nik_pedagang')->references('nik_pedagang')->on('pedagang')->cascadeOnDelete();
                $table->foreign('id_event')->references('id_event')->on('event_cfd')->cascadeOnDelete();
                $table->unique(['nik_pedagang', 'id_event']);
            });
        }

        if (! Schema::hasTable('detail_penjualan')) {
            Schema::create('detail_penjualan', function (Blueprint $table) {
                $table->id('id_detail_penjualan');
                $table->unsignedBigInteger('id_laporan');
                $table->unsignedBigInteger('id_produk');
                $table->unsignedInteger('jumlah_item_terjual')->default(0);
                $table->unsignedBigInteger('harga_satuan')->default(0);
                $table->unsignedBigInteger('subtotal')->default(0);
                $table->foreign('id_laporan')->references('id_laporan')->on('laporan_penjualan')->cascadeOnDelete();
                $table->foreign('id_produk')->references('id_produk')->cascadeOnDelete();
                $table->unique(['id_laporan', 'id_produk']);
            });
        }

        // Backfill detail untuk laporan lama agar laporan lama tetap terbaca.
        if (Schema::hasTable('laporan_penjualan') && Schema::hasTable('detail_penjualan') && Schema::hasTable('pendaftaran_tenant')) {
            $reports = DB::table('laporan_penjualan as l')
                ->join('pendaftaran_tenant as p', 'p.id_pendaftaran', '=', 'l.id_pendaftaran')
                ->whereNotNull('p.id_produk')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('detail_penjualan as d')
                        ->whereColumn('d.id_laporan', 'l.id_laporan');
                })
                ->select('l.id_laporan', 'l.jumlah_terjual', 'l.total_pendapatan', 'p.id_produk')
                ->get();

            foreach ($reports as $report) {
                $qty = (int) $report->jumlah_terjual;
                $revenue = (float) $report->total_pendapatan;
                $unitPrice = $qty > 0 ? (int) round($revenue / $qty) : 0;

                DB::table('detail_penjualan')->insert([
                    'id_laporan' => $report->id_laporan,
                    'id_produk' => $report->id_produk,
                    'jumlah_item_terjual' => $qty,
                    'harga_satuan' => $unitPrice,
                    'subtotal' => (int) round($revenue),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
        Schema::dropIfExists('perizinan');

        if (Schema::hasTable('pendaftaran_tenant') && Schema::hasColumn('pendaftaran_tenant', 'id_produk')) {
            Schema::table('pendaftaran_tenant', function (Blueprint $table) {
                $table->dropForeign(['id_produk']);
                $table->dropColumn('id_produk');
            });
        }

        Schema::dropIfExists('produk');
    }
};
