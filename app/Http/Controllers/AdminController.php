<?php

namespace App\Http\Controllers;

use App\Models\EventCfd;
use App\Models\InboxPedagang;
use App\Models\LapakTenant;
use App\Models\PendaftaranTenant;
use App\Models\Perizinan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public const ZONA_LIST = ['Kuliner', 'Fashion', 'Kerajinan', 'Jasa & Lainnya'];

    public function index()
    {
        $events = EventCfd::withCount('lapak')->orderByDesc('tanggal_event')->get();
        $activeEvents = EventCfd::aktifBerjalan()->orderBy('tanggal_event')->orderBy('waktu_mulai')->get();

        $rekapBaris = LapakTenant::selectRaw(
                'MIN(id_lapak) as id_lapak, id_event, kategori_lapak, baris, COUNT(*) as total, MAX(kolom) as jumlah_kolom, MAX(ukuran_lapak) as ukuran_lapak, MAX(lokasi_lapak) as lokasi_lapak'
            )
            ->groupBy('id_event', 'kategori_lapak', 'baris')
            ->orderBy('kategori_lapak')
            ->orderBy('baris')
            ->get()
            ->groupBy('id_event');

        $rekapZona = LapakTenant::selectRaw('id_event, kategori_lapak, count(*) as total')
            ->groupBy('id_event', 'kategori_lapak')
            ->get()
            ->groupBy('id_event');

        return view('admin.index', [
            'events' => $events,
            'activeEvents' => $activeEvents,
            'rekapZona' => $rekapZona,
            'rekapBaris' => $rekapBaris,
            'zonaList' => self::ZONA_LIST,
        ]);
    }

    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'nama_event' => 'required|string|max:150',
            'tanggal_event' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'lokasi' => 'required|string|max:150',
            'tanggal_buka_pendaftaran' => 'required|date|before_or_equal:tanggal_event',
            'status_event' => 'required|in:Aktif,Nonaktif,Selesai',
        ]);

        EventCfd::create($data);

        return back()->with('success', 'Event baru berhasil ditambahkan. Sekarang tambahkan lapaknya per zona di bawah.');
    }

    public function toggleEventStatus(Request $request, EventCfd $event)
    {
        $request->validate(['status_event' => 'required|in:Aktif,Nonaktif,Selesai']);
        $event->update(['status_event' => $request->status_event]);

        return back()->with('success', "Status event \"{$event->nama_event}\" diubah menjadi {$request->status_event}.");
    }

    public function destroyEvent(EventCfd $event)
    {
        $nama = $event->nama_event;
        $event->delete(); // lapak & pendaftaran terkait ikut terhapus (cascade)

        return back()->with('success', "Event \"{$nama}\" beserta seluruh lapaknya berhasil dihapus.");
    }

    public function storeLapakBaris(Request $request)
    {
        $data = $request->validate([
            'id_event' => 'required|integer|exists:event_cfd,id_event',
            'kategori_lapak' => 'required|in:' . implode(',', self::ZONA_LIST),
            'baris' => 'required|string|max:5|alpha',
            'jumlah_kolom' => 'required|integer|min:1|max:26',
            'ukuran_lapak' => 'required|string|max:30',
            'lokasi_lapak' => 'nullable|string|max:150',
        ]);

        $baris = strtoupper($data['baris']);
        $dibuat = 0;
        $dilewati = 0;

        for ($kolom = 1; $kolom <= $data['jumlah_kolom']; $kolom++) {
            $nomorLapak = $baris . $kolom;

            $sudahAda = LapakTenant::where('id_event', $data['id_event'])
                ->where('nomor_lapak', $nomorLapak)
                ->exists();

            if ($sudahAda) {

                $dilewati++;
                continue;
            }

            LapakTenant::create([
                'id_event' => $data['id_event'],
                'kategori_lapak' => $data['kategori_lapak'],
                'baris' => $baris,
                'kolom' => $kolom,
                'nomor_lapak' => $nomorLapak,
                'lokasi_lapak' => $data['lokasi_lapak'] ?? null,
                'ukuran_lapak' => $data['ukuran_lapak'],
                'status_lapak' => 'Tersedia',
            ]);
            $dibuat++;
        }

        $pesan = "{$dibuat} lapak berhasil ditambahkan ke Zona {$data['kategori_lapak']} baris {$baris}.";
        if ($dilewati > 0) {
            $pesan .= " ({$dilewati} nomor dilewati karena sudah ada.)";
        }

        return back()->with('success', $pesan);
    }

    public function destroyLapak(LapakTenant $lapak)
    {
        $info = "{$lapak->kategori_lapak} - {$lapak->nomor_lapak}";
        $lapak->delete();

        return back()->with('success', "Lapak {$info} berhasil dihapus.");
    }

    public function updateLapakBaris(Request $request, LapakTenant $lapak)
    {
        $data = $request->validate([
            'jumlah_kolom' => 'required|integer|min:1|max:26',
            'ukuran_lapak' => 'required|string|max:30',
            'lokasi_lapak' => 'nullable|string|max:150',
        ]);

        $groupQuery = LapakTenant::where('id_event', $lapak->id_event)
            ->where('kategori_lapak', $lapak->kategori_lapak)
            ->where('baris', $lapak->baris);

        $currentMax = (int) ($groupQuery->max('kolom') ?? 0);
        $newMax = (int) $data['jumlah_kolom'];

        if ($newMax < $currentMax) {
            $lapakYangAkanDihapus = (clone $groupQuery)
                ->where('kolom', '>', $newMax)
                ->get();

            foreach ($lapakYangAkanDihapus as $lapakHapus) {
                if ($lapakHapus->status_lapak !== 'Tersedia') {
                    return back()->withErrors([
                        'lapak' => "Tidak dapat mengurangi jumlah lapak sampai {$newMax}. Lapak {$lapakHapus->nomor_lapak} sedang {$lapakHapus->status_lapak}.",
                    ]);
                }

                if (PendaftaranTenant::where('id_lapak', $lapakHapus->id_lapak)->exists()) {
                    return back()->withErrors([
                        'lapak' => "Tidak dapat mengurangi jumlah lapak karena {$lapakHapus->nomor_lapak} sudah memiliki riwayat pendaftaran.",
                    ]);
                }
            }

            (clone $groupQuery)
                ->where('kolom', '>', $newMax)
                ->delete();
        }

        if ($newMax > $currentMax) {
            for ($kolom = $currentMax + 1; $kolom <= $newMax; $kolom++) {
                $nomorLapak = $lapak->baris . $kolom;

                if (LapakTenant::where('id_event', $lapak->id_event)
                    ->where('nomor_lapak', $nomorLapak)
                    ->exists()) {
                    continue;
                }

                LapakTenant::create([
                    'id_event' => $lapak->id_event,
                    'kategori_lapak' => $lapak->kategori_lapak,
                    'baris' => $lapak->baris,
                    'kolom' => $kolom,
                    'nomor_lapak' => $nomorLapak,
                    'lokasi_lapak' => $data['lokasi_lapak'] ?? null,
                    'ukuran_lapak' => $data['ukuran_lapak'],
                    'status_lapak' => 'Tersedia',
                ]);
            }
        }

        // Samakan informasi ukuran/lokasi pada seluruh lapak dalam baris tersebut.
        (clone $groupQuery)->update([
            'ukuran_lapak' => $data['ukuran_lapak'],
            'lokasi_lapak' => $data['lokasi_lapak'] ?? null,
        ]);

        return back()->with(
            'success',
            "Zona {$lapak->kategori_lapak}, baris {$lapak->baris} berhasil diperbarui menjadi {$newMax} lapak."
        );
    }

    /** Daftar pendaftaran yang menunggu verifikasi, lengkap dengan data produk & pedagang. */
    public function verifikasi()
    {
        $menunggu = PendaftaranTenant::with(['pedagang', 'event', 'lapak', 'produk'])
            ->where('status_pendaftaran', 'Menunggu Verifikasi')
            ->orderBy('tanggal_pendaftaran')
            ->get();

        $riwayat = PendaftaranTenant::with(['pedagang', 'event', 'lapak', 'produk'])
            ->whereIn('status_pendaftaran', ['Terverifikasi', 'Ditolak'])
            ->orderByDesc('id_pendaftaran')
            ->limit(30)
            ->get();

        foreach ($menunggu->concat($riwayat) as $p) {
            $p->setRelation('perizinan', Perizinan::where('nik_pedagang', $p->nik_pedagang)
                ->where('id_event', $p->id_event)
                ->first());
        }

        return view('admin.verifikasi', compact('menunggu', 'riwayat'));
    }

    public function terimaPendaftaran(PendaftaranTenant $pendaftaran)
    {
        if ($pendaftaran->status_pendaftaran !== 'Menunggu Verifikasi') {
            return back()->withErrors(['pendaftaran' => 'Pendaftaran ini sudah diproses sebelumnya.']);
        }

        // Setelah pendaftaran disetujui, sistem otomatis mencatat perizinan.
        // Pedagang tidak perlu mengajukan dokumen perizinan secara terpisah.
        $perizinan = Perizinan::updateOrCreate(
            [
                'nik_pedagang' => $pendaftaran->nik_pedagang,
                'id_event' => $pendaftaran->id_event,
            ],
            [
                'jenis_perizinan' => 'Izin Berjualan CFD',
                'tanggal_pengajuan' => now()->toDateString(),
                'tanggal_berlaku' => $pendaftaran->event->tanggal_event,
                'status_perizinan' => 'Terverifikasi',
                'dokumen_perizinan' => '',
            ]
        );

        $pendaftaran->update([
            'status_pendaftaran' => 'Terverifikasi',
            'catatan_admin' => null,
        ]);

        InboxPedagang::create([
            'nik_pedagang' => $pendaftaran->nik_pedagang,
            'tipe' => 'diterima',
            'judul' => 'Verifikasi Diterima',
            'pesan' => "Selamat! Pendaftaran produk \"{$pendaftaran->nama_produk}\" telah berhasil diverifikasi oleh admin.",
            'id_pendaftaran' => $pendaftaran->id_pendaftaran,
        ]);

        return back()->with('success', "Pendaftaran {$pendaftaran->nama_produk} milik {$pendaftaran->pedagang->nama_pedagang} berhasil diverifikasi.");
    }

    public function tolakPendaftaran(Request $request, PendaftaranTenant $pendaftaran)
    {
        if ($pendaftaran->status_pendaftaran !== 'Menunggu Verifikasi') {
            return back()->withErrors(['pendaftaran' => 'Pendaftaran ini sudah diproses sebelumnya.']);
        }

        $request->validate([
            'catatan_admin' => 'nullable|string|max:255',
        ]);

        $catatan = trim((string) $request->catatan_admin);
        if ($catatan === '') {
            $catatan = 'Tidak ada alasan penolakan yang diberikan oleh admin.';
        }

        $pendaftaran->update([
            'status_pendaftaran' => 'Ditolak',
            'catatan_admin' => $catatan,
        ]);

        $perizinan = Perizinan::where('nik_pedagang', $pendaftaran->nik_pedagang)
            ->where('id_event', $pendaftaran->id_event)
            ->first();
        if ($perizinan) {
            $perizinan->update(['status_perizinan' => 'Ditolak']);
        }


        if ($pendaftaran->lapak) {
            $pendaftaran->lapak->update(['status_lapak' => 'Tersedia']);
        }

        InboxPedagang::create([
            'nik_pedagang' => $pendaftaran->nik_pedagang,
            'tipe' => 'ditolak',
            'judul' => 'Verifikasi Ditolak',
            'pesan' => "Pendaftaran produk \"{$pendaftaran->nama_produk}\" ditolak oleh admin.\n\nAlasan penolakan:\n{$catatan}",
            'id_pendaftaran' => $pendaftaran->id_pendaftaran,
        ]);

        return back()->with('success', "Pendaftaran {$pendaftaran->nama_produk} milik {$pendaftaran->pedagang->nama_pedagang} ditolak. Lapak dikembalikan menjadi tersedia.");
    }
}
