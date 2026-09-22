<?php

namespace App\Http\Controllers;

use App\Models\EventCfd;
use App\Models\LapakTenant;
use App\Models\PendaftaranTenant;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** Daftar zona yang dikenali sistem (harus sama persis dengan key di lapak/index.blade.php) */
    public const ZONA_LIST = ['Kuliner', 'Fashion', 'Kerajinan', 'Jasa & Lainnya'];

    public function index()
    {
        $events = EventCfd::withCount('lapak')->orderByDesc('tanggal_event')->get();
        $activeEvents = EventCfd::aktifBerjalan()->orderBy('tanggal_event')->orderBy('waktu_mulai')->get();

        // Rekap jumlah lapak per zona untuk tiap event, ditampilkan sebagai info kecil.
        $rekapZona = LapakTenant::selectRaw('id_event, kategori_lapak, count(*) as total')
            ->groupBy('id_event', 'kategori_lapak')
            ->get()
            ->groupBy('id_event');

        return view('admin.index', [
            'events' => $events,
            'activeEvents' => $activeEvents,
            'rekapZona' => $rekapZona,
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
                // Lapak dengan nomor ini sudah ada (mungkin ditambah sebelumnya) —
                // dilewati saja supaya status/pemesanan yang sudah ada tidak ter-reset.
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

    /** Daftar pendaftaran yang menunggu verifikasi, lengkap dengan data produk & pedagang. */
    public function verifikasi()
    {
        $menunggu = PendaftaranTenant::with(['pedagang', 'event', 'lapak'])
            ->where('status_pendaftaran', 'Menunggu Verifikasi')
            ->orderBy('tanggal_pendaftaran')
            ->get();

        $riwayat = PendaftaranTenant::with(['pedagang', 'event', 'lapak'])
            ->whereIn('status_pendaftaran', ['Terverifikasi', 'Ditolak'])
            ->orderByDesc('id_pendaftaran')
            ->limit(30)
            ->get();

        return view('admin.verifikasi', compact('menunggu', 'riwayat'));
    }

    public function terimaPendaftaran(PendaftaranTenant $pendaftaran)
    {
        if ($pendaftaran->status_pendaftaran !== 'Menunggu Verifikasi') {
            return back()->withErrors(['pendaftaran' => 'Pendaftaran ini sudah diproses sebelumnya.']);
        }

        $pendaftaran->update([
            'status_pendaftaran' => 'Terverifikasi',
            'catatan_admin' => null,
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

        $pendaftaran->update([
            'status_pendaftaran' => 'Ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        // Lapak dikembalikan menjadi "Tersedia" lagi supaya bisa dipilih pedagang lain
        // (atau dicoba ulang oleh pedagang yang sama dengan lapak lain).
        if ($pendaftaran->lapak) {
            $pendaftaran->lapak->update(['status_lapak' => 'Tersedia']);
        }

        return back()->with('success', "Pendaftaran {$pendaftaran->nama_produk} milik {$pendaftaran->pedagang->nama_pedagang} ditolak. Lapak dikembalikan menjadi tersedia.");
    }
}
