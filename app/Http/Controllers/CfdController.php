<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;
use App\Models\Pedagang;
use App\Models\LapakTenant;
use App\Models\EventCfd;
use App\Models\PendaftaranTenant;

class CfdController extends Controller
{
    /** Pilihan jenis produk yang bisa dipilih pedagang (biar tidak perlu ngetik manual). */
    public const JENIS_PRODUK_LIST = [
        'Makanan Berat',
        'Makanan Ringan / Snack',
        'Minuman',
        'Pakaian',
        'Aksesoris',
        'Kerajinan Tangan',
        'Jasa',
        'Lainnya',
    ];

    public function home()
    {
        $events = EventCfd::aktifBerjalan()->orderBy('tanggal_event')->orderBy('waktu_mulai')->get();
        $pendingSalesReportsCount = 0;

        if (Auth::guard('pedagang')->check()) {
            $nik = Auth::guard('pedagang')->user()->nik_pedagang;
            $eventSelesaiIds = EventCfd::sudahSelesai()->pluck('id_event');

            $pendingSalesReportsCount = PendaftaranTenant::where('nik_pedagang', $nik)
                ->where('status_pendaftaran', 'Terverifikasi')
                ->whereIn('id_event', $eventSelesaiIds)
                ->whereDoesntHave('laporanPenjualan')
                ->count();
        }

        return view('home', compact('events', 'pendingSalesReportsCount'));
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Login gabungan: satu form untuk Pedagang maupun Admin.
     * Dicek dulu ke tabel pedagang, kalau tidak cocok baru dicek ke tabel admin.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $pedagang = Pedagang::where('username', $request->username)->first();
        if ($pedagang && Hash::check($request->password, $pedagang->password)) {
            // Pastikan hanya satu jenis akun yang aktif dalam satu session.
            // Ini mencegah session admin dan pedagang bertumpuk ketika pengguna berganti akun.
            Auth::guard('admin')->logout();
            Auth::guard('pedagang')->login($pedagang);
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', 'Login berhasil! Selamat datang, ' . $pedagang->nama_pedagang . '.');
        }

        $admin = Admin::where('username', $request->username)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            // Saat masuk sebagai admin, keluarkan akun pedagang yang mungkin masih aktif.
            Auth::guard('pedagang')->logout();
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();

            return redirect()->route('admin.index')->with('success', 'Login admin berhasil! Selamat datang, ' . $admin->nama_admin . '.');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nik_pedagang' => 'required|string|max:16|unique:pedagang',
            'nama_pedagang' => 'required|string|max:100',
            'nama_usaha' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'alamat' => 'required|string',
            'ktp_pedagang' => 'required|image|max:2048',
            'username' => 'required|string|max:50|unique:pedagang',
            'password' => 'required|string|min:6'
        ]);

        $pathKtp = $request->file('ktp_pedagang')->store('ktp', 'public');

        Pedagang::create([
            'nik_pedagang' => $request->nik_pedagang,
            'nama_pedagang' => $request->nama_pedagang,
            'nama_usaha' => $request->nama_usaha,
            'no_telepon' => $request->no_telepon,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'ktp_pedagang' => $pathKtp,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status_verifikasi' => 'Belum Verifikasi'
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan login.');
    }

    public function pilihLapak()
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        // Kalau pedagang ini masih punya pendaftaran yang belum dilengkapi data
        // produknya, arahkan dulu ke sana sebelum bisa memilih lapak lagi.
        $belumLengkap = PendaftaranTenant::where('nik_pedagang', $nik)
            ->where('status_pendaftaran', 'Menunggu Data Produk')
            ->first();

        if ($belumLengkap) {
            return redirect()->route('produk.lengkapi')
                ->with('info', 'Lengkapi dulu data produk untuk lapak yang baru saja Anda pilih.');
        }

        $events = EventCfd::aktifBerjalan()->orderBy('tanggal_event')->orderBy('waktu_mulai')->get();

        // Registrasi AKTIF milik pedagang ini (bukan yang Ditolak) per event —
        // dipakai untuk menegakkan aturan "1 pedagang hanya boleh 1 lapak per event".
        $registrasiSaya = PendaftaranTenant::with('lapak')
            ->where('nik_pedagang', $nik)
            ->whereIn('id_event', $events->pluck('id_event'))
            ->whereIn('status_pendaftaran', PendaftaranTenant::STATUS_AKTIF)
            ->get()
            ->keyBy('id_event');

        // Ambil SEMUA lapak (bukan cuma yang tersedia) supaya lapak yang sudah
        // "Dipesan" tetap tampil di denah, seperti kursi bioskop yang sudah terisi.
        $semuaLapak = LapakTenant::whereIn('id_event', $events->pluck('id_event'))
            ->orderBy('kategori_lapak')
            ->orderBy('baris')
            ->orderBy('kolom')
            ->get();

        // Susun menjadi struktur bertingkat: [id_event][kategori][baris][] = lapak
        // agar Blade tinggal me-render denah per zona per baris, tanpa parsing.
        $lapakByEvent = [];
        foreach ($semuaLapak as $l) {
            $lapakByEvent[$l->id_event][$l->kategori_lapak][$l->baris][] = $l;
        }

        return view('lapak.index', compact('events', 'lapakByEvent', 'registrasiSaya'));
    }

    public function daftarLapak(Request $request)
    {
        $request->validate([
            'id_event' => 'required|integer',
            'id_lapak' => 'required|integer'
        ]);

        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        return DB::transaction(function () use ($request, $nik) {
            // Kunci baris lapak ini supaya tidak ada 2 request bersamaan yang
            // sama-sama lolos cek "Tersedia" (mencegah race condition).
            $eventMasihAktif = EventCfd::aktifBerjalan()
                ->where('id_event', $request->id_event)
                ->exists();

            if (! $eventMasihAktif) {
                return back()->withErrors(['id_event' => 'Event ini sudah selesai atau tidak lagi aktif. Silakan pilih event aktif lainnya.']);
            }

            $lapak = LapakTenant::where('id_lapak', $request->id_lapak)
                ->where('id_event', $request->id_event)
                ->lockForUpdate()
                ->first();

            if (! $lapak || $lapak->status_lapak !== 'Tersedia') {
                return back()->withErrors(['id_lapak' => 'Lapak yang dipilih sudah tidak tersedia. Silakan pilih lapak lain.']);
            }

            // ATURAN KERAS: 1 pedagang hanya boleh punya 1 lapak per event.
            $sudahPunya = PendaftaranTenant::where('nik_pedagang', $nik)
                ->where('id_event', $request->id_event)
                ->whereIn('status_pendaftaran', PendaftaranTenant::STATUS_AKTIF)
                ->exists();

            if ($sudahPunya) {
                return back()->withErrors(['id_lapak' => 'Anda sudah memilih lapak lain pada event ini. Satu pedagang hanya boleh memiliki 1 lapak untuk 1 event yang sama.']);
            }

            $pendaftaran = PendaftaranTenant::create([
                'nik_pedagang' => $nik,
                'id_event' => $request->id_event,
                'id_lapak' => $request->id_lapak,
                'tanggal_pendaftaran' => now(),
                'status_pendaftaran' => 'Menunggu Data Produk',
            ]);

            $lapak->update(['status_lapak' => 'Dipesan']);

            return redirect()->route('produk.lengkapi')
                ->with('success', "Lapak nomor {$lapak->nomor_lapak} berhasil dipilih. Lengkapi data produk Anda untuk melanjutkan.");
        });
    }

    /** Tampilkan form pengisian data produk untuk pendaftaran yang baru dipilih. */
    public function lengkapiProduk()
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        $pendaftaran = PendaftaranTenant::with(['lapak', 'event'])
            ->where('nik_pedagang', $nik)
            ->where('status_pendaftaran', 'Menunggu Data Produk')
            ->latest('id_pendaftaran')
            ->first();

        if (! $pendaftaran) {
            return redirect()->route('lapak.index');
        }

        return view('produk.lengkapi', [
            'pendaftaran' => $pendaftaran,
            'jenisProdukList' => self::JENIS_PRODUK_LIST,
        ]);
    }

    /** Simpan data produk lalu majukan status ke "Menunggu Verifikasi". */
    public function simpanProduk(Request $request)
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        $request->validate([
            'id_pendaftaran' => 'required|integer',
            'nama_produk' => 'required|string|max:150',
            'jenis_produk' => 'required|in:' . implode(',', self::JENIS_PRODUK_LIST),
            'jumlah_produk' => 'required|integer|min:1',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'foto_produk' => 'nullable|image|max:2048',
        ]);

        $pendaftaran = PendaftaranTenant::where('id_pendaftaran', $request->id_pendaftaran)
            ->where('nik_pedagang', $nik)
            ->where('status_pendaftaran', 'Menunggu Data Produk')
            ->first();

        if (! $pendaftaran) {
            return redirect()->route('lapak.index')->withErrors(['id_pendaftaran' => 'Pendaftaran tidak ditemukan atau sudah diproses.']);
        }

        $dataUpdate = [
            'nama_produk' => $request->nama_produk,
            'jenis_produk' => $request->jenis_produk,
            'jumlah_produk' => $request->jumlah_produk,
            'keterangan_tambahan' => $request->keterangan_tambahan,
            'status_pendaftaran' => 'Menunggu Verifikasi',
        ];

        if ($request->hasFile('foto_produk')) {
            // Hapus foto lama kalau ada, lalu simpan yang baru ke storage/app/public/produk
            if ($pendaftaran->foto_produk) {
                Storage::disk('public')->delete($pendaftaran->foto_produk);
            }
            $dataUpdate['foto_produk'] = $request->file('foto_produk')->store('produk', 'public');
        }

        $pendaftaran->update($dataUpdate);

        return redirect()->route('home')->with('success', 'Data produk berhasil disimpan. Pendaftaran Anda kini menunggu verifikasi admin.');
    }

    public function logout(Request $request)
    {
        // Bersihkan kedua guard agar tidak ada akun lama yang tertinggal di session.
        Auth::guard('admin')->logout();
        Auth::guard('pedagang')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil logout.');
    }
}
