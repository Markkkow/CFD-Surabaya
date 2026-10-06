<?php

namespace Database\Seeders;

use App\Models\EventCfd;
use App\Models\LapakTenant;
use App\Models\LaporanPenjualan;
use App\Models\Pedagang;
use App\Models\Produk;
use App\Models\DetailPenjualan;
use App\Models\PendaftaranTenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyPedagangSeeder extends Seeder
{

    public function run(): void
    {
        DB::transaction(function () {
            $event = EventCfd::find(4);

            if (! $event) {
                $event = EventCfd::first();
            }

            if (! $event) {
                $event = EventCfd::create([
                    'nama_event' => 'CFD Demo 50 Pedagang',
                    'tanggal_event' => '2026-09-26',
                    'waktu_mulai' => '07:00:00',
                    'waktu_selesai' => '11:00:00',
                    'lokasi' => 'Jalan Darmo, Surabaya',
                    'status_event' => 'Aktif',
                    'tanggal_buka_pendaftaran' => '2026-09-20',
                ]);
            }

            $lapaks = LapakTenant::where('id_event', $event->id_event)
                ->where('status_lapak', 'Tersedia')
                ->orderBy('id_lapak')
                ->get();

            if ($lapaks->count() < 50) {
                $kurang = 50 - $lapaks->count();

                for ($i = 1; $i <= $kurang; $i++) {
                    $nextNumber = LapakTenant::where('id_event', $event->id_event)->count() + 1;
                    $nomor = 'D' . $nextNumber;

                    $lapaks->push(LapakTenant::create([
                        'id_event' => $event->id_event,
                        'kategori_lapak' => 'Kuliner',
                        'baris' => 'D',
                        'kolom' => $nextNumber,
                        'nomor_lapak' => $nomor,
                        'lokasi_lapak' => 'Area Demo CFD',
                        'ukuran_lapak' => '2x2 m',
                        'status_lapak' => 'Tersedia',
                    ]));
                }
            }

            $namaDepan = [
                'Andi', 'Bima', 'Citra', 'Dinda', 'Eko', 'Fajar', 'Gita', 'Hana',
                'Indra', 'Jihan', 'Kevin', 'Laras', 'Maya', 'Nadia', 'Oscar', 'Putri',
                'Raka', 'Salsa', 'Tio', 'Vina', 'Wahyu', 'Yuni', 'Zaki', 'Alya',
                'Bagas', 'Cindy', 'Daffa', 'Elsa', 'Farhan', 'Galih', 'Hendra', 'Intan',
                'Joko', 'Karin', 'Lukman', 'Mila', 'Naufal', 'Olivia', 'Rian', 'Sinta',
                'Tegar', 'Ulfa', 'Vito', 'Wulan', 'Yoga', 'Zahra', 'Arman', 'Bella',
                'Dion', 'Niken',
            ];

            $usaha = [
                ['nama' => 'Kopi Sudut Kota', 'produk' => 'Es Kopi Susu', 'jenis' => 'Minuman'],
                ['nama' => 'Rasa Nusantara', 'produk' => 'Nasi Ayam Sambal', 'jenis' => 'Makanan Berat'],
                ['nama' => 'Sweet Corner', 'produk' => 'Brownies Mini', 'jenis' => 'Makanan Ringan'],
                ['nama' => 'Sate Senja', 'produk' => 'Sate Ayam', 'jenis' => 'Makanan Berat'],
                ['nama' => 'Jajan Pasar Kita', 'produk' => 'Kue Tradisional', 'jenis' => 'Makanan Ringan'],
                ['nama' => 'Fresh Bowl', 'produk' => 'Salad Buah', 'jenis' => 'Makanan'],
                ['nama' => 'Teh Taman', 'produk' => 'Teh Lemon', 'jenis' => 'Minuman'],
                ['nama' => 'Burger Lokal', 'produk' => 'Burger Beef', 'jenis' => 'Makanan Berat'],
                ['nama' => 'Craft Corner', 'produk' => 'Gantungan Kunci', 'jenis' => 'Kerajinan'],
                ['nama' => 'Fashion Jalanan', 'produk' => 'Kaos Lokal', 'jenis' => 'Fashion'],
            ];

            $harga = [12000, 15000, 18000, 20000, 22000, 25000, 28000, 30000, 35000, 40000];
            $passwordHash = Hash::make('password');

            for ($i = 1; $i <= 50; $i++) {
                $index = $i - 1;
                $kode = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                $nama = $namaDepan[$index] . ' ' . ['Pratama', 'Saputra', 'Lestari', 'Ramadhan', 'Permata'][$index % 5];
                $usahaData = $usaha[$index % count($usaha)];

                // 16 digit dan unik.
                $nik = '357800000000' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
                $username = 'demo' . $kode;

                $pedagang = Pedagang::updateOrCreate(
                    ['username' => $username],
                    [
                        'nik_pedagang' => $nik,
                        'nama_pedagang' => $nama,
                        'nama_usaha' => $usahaData['nama'] . ' ' . $kode,
                        'sosial_media_usaha' => '@' . strtolower(str_replace(' ', '', $usahaData['nama'])) . $kode,
                        'no_telepon' => '08123000' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                        'email' => 'pedagang' . $kode . '@cfd-demo.test',
                        'alamat' => 'Jl. Darmo No. ' . (10 + $i) . ', Surabaya',
                        'ktp_pedagang' => null,
                        'password' => $passwordHash,
                        'status_verifikasi' => 'Terverifikasi',
                    ]
                );

                $pendaftaran = PendaftaranTenant::where('nik_pedagang', $pedagang->nik_pedagang)
                    ->where('id_event', $event->id_event)
                    ->first();

                $stokProduk = 200 + (($i * 17) % 301);
                $produk = Produk::firstOrCreate(
                    [
                        'nik_pedagang' => $pedagang->nik_pedagang,
                        'nama_produk' => $usahaData['produk'],
                    ],
                    [
                        'kategori_produk' => $usahaData['jenis'],
                        'harga' => $harga[$index % count($harga)],
                        'stok_produk' => $stokProduk,
                        'deskripsi_produk' => 'Data demo untuk pengujian dashboard penjualan.',
                        'foto_produk' => null,
                    ]
                );

                if (! $pendaftaran) {
                    $lapak = $lapaks[$index];

                    $pendaftaran = PendaftaranTenant::create([
                        'nik_pedagang' => $pedagang->nik_pedagang,
                        'id_event' => $event->id_event,
                        'id_lapak' => $lapak->id_lapak,
                        'id_produk' => $produk->id_produk,
                        'nama_produk' => $produk->nama_produk,
                        'jenis_produk' => $produk->kategori_produk,
                        'jumlah_produk' => $produk->stok_produk,
                        'keterangan_tambahan' => $produk->deskripsi_produk,
                        'foto_produk' => $produk->foto_produk,
                        'tanggal_pendaftaran' => '2026-09-20',
                        'status_pendaftaran' => 'Terverifikasi',
                        'catatan_admin' => null,
                        'tanggal_verifikasi' => '2026-09-21',
                    ]);

                    $lapak->update(['status_lapak' => 'Dipesan']);
                } elseif (! $pendaftaran->id_produk) {
                    $pendaftaran->update(['id_produk' => $produk->id_produk]);
                }

                $qty = 35 + (($i * 17) % 116);
                $hargaSatuan = (int) $produk->harga;
                $omzet = $qty * $hargaSatuan;
                $rasioModal = 0.45 + (($index % 6) * 0.04);
                $modal = (int) (round(($omzet * $rasioModal) / 1000) * 1000);

                $laporan = LaporanPenjualan::updateOrCreate(
                    ['id_pendaftaran' => $pendaftaran->id_pendaftaran],
                    [
                        'nik_pedagang' => $pedagang->nik_pedagang,
                        'id_event' => $event->id_event,
                        'tanggal_laporan' => $event->tanggal_event,
                        'total_omzet' => $omzet,
                        'jumlah_terjual' => $qty,
                        'total_pendapatan' => $omzet,
                        'total_modal' => $modal,
                        'catatan_penjualan' => 'Dummy data penjualan ' . $kode . ' — omzet dan hasil penjualan sudah diisi.',
                        'updated_at' => now(),
                    ]
                );

                DetailPenjualan::updateOrCreate(
                    ['id_laporan' => $laporan->id_laporan, 'id_produk' => $produk->id_produk],
                    [
                        'jumlah_item_terjual' => $qty,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $omzet,
                    ]
                );
            }
        });

        $this->command?->info('50 pedagang demo + pendaftaran + laporan penjualan berhasil dibuat.');
        $this->command?->info('Username: demo01 s/d demo50 | Password: password');
    }
}
