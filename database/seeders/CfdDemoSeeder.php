<?php

namespace Database\Seeders;

use App\Models\EventCfd;
use App\Models\LapakTenant;
use App\Models\Pedagang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CfdDemoSeeder extends Seeder
{
    /**
     * Seed 1 event CFD lengkap dengan denah lapak ala "kursi bioskop":
     * setiap kategori dagangan (zona) punya area sendiri, dan di dalam
     * zona itu lapak disusun per baris & kolom.
     */
    public function run(): void
    {
        $event = EventCfd::updateOrCreate(
            ['nama_event' => 'CFD Jalan Tunjungan - Minggu Pagi'],
            [
                'tanggal_event' => now()->next('Sunday')->toDateString(),
                'waktu_mulai' => '06:00:00',
                'waktu_selesai' => '10:00:00',
                'lokasi' => 'Jalan Tunjungan, Surabaya',
                'status_event' => 'Aktif',
                'tanggal_buka_pendaftaran' => now()->toDateString(),
            ]
        );

        // Definisi zona: kategori => [baris => jumlah_kolom]
        $zonas = [
            'Kuliner' => [
                'ukuran' => '2x2 m',
                'lokasi' => 'Sisi Utara, dekat Taman Apsari',
                'baris' => ['A' => 10, 'B' => 10],
            ],
            'Fashion' => [
                'ukuran' => '2x1.5 m',
                'lokasi' => 'Sisi Tengah, depan Gedung Siola',
                'baris' => ['C' => 8, 'D' => 8],
            ],
            'Kerajinan' => [
                'ukuran' => '1.5x1.5 m',
                'lokasi' => 'Sisi Selatan, dekat air mancur',
                'baris' => ['E' => 6],
            ],
            'Jasa & Lainnya' => [
                'ukuran' => '2x2 m',
                'lokasi' => 'Ujung Jalan, dekat pos petugas',
                'baris' => ['F' => 6],
            ],
        ];

        // Pola lapak yang sudah "Dipesan" agar tampilan demo terlihat hidup,
        // seperti kursi bioskop yang sebagian sudah terisi.
        $sudahDipesan = ['A2', 'A3', 'A7', 'B5', 'C1', 'C2', 'D6', 'E3', 'F1'];

        foreach ($zonas as $kategori => $data) {
            foreach ($data['baris'] as $baris => $jumlahKolom) {
                for ($kolom = 1; $kolom <= $jumlahKolom; $kolom++) {
                    $nomorLapak = $baris . $kolom;

                    LapakTenant::updateOrCreate(
                        [
                            'id_event' => $event->id_event,
                            'nomor_lapak' => $nomorLapak,
                        ],
                        [
                            'kategori_lapak' => $kategori,
                            'baris' => $baris,
                            'kolom' => $kolom,
                            'lokasi_lapak' => $data['lokasi'],
                            'ukuran_lapak' => $data['ukuran'],
                            'status_lapak' => in_array($nomorLapak, $sudahDipesan) ? 'Dipesan' : 'Tersedia',
                        ]
                    );
                }
            }
        }

        // Akun pedagang contoh untuk uji coba login.
        Pedagang::updateOrCreate(
            ['username' => 'pedagang1'],
            [
                'nik_pedagang' => '3578012345670001',
                'nama_pedagang' => 'Sari Wulandari',
                'nama_usaha' => 'Warung Kopi Bu Sari',
                'no_telepon' => '081234567890',
                'email' => 'sari@example.com',
                'alamat' => 'Jl. Kenjeran No. 12, Surabaya',
                'password' => Hash::make('password'),
                'status_verifikasi' => 'Terverifikasi',
            ]
        );
    }
}
