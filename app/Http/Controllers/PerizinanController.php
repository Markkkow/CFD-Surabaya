<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranTenant;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerizinanController extends Controller
{
    public const JENIS_PERIZINAN = [
        'Surat Izin Berjualan CFD',
        'Surat Keterangan Usaha',
        'NIB / Legalitas Usaha',
        'Dokumen Pendukung Lainnya',
    ];

    public function create()
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        $pendaftaran = PendaftaranTenant::with(['event', 'lapak', 'produk'])
            ->where('nik_pedagang', $nik)
            ->where('status_pendaftaran', 'Menunggu Perizinan')
            ->latest('id_pendaftaran')
            ->first();

        if (! $pendaftaran) {
            return redirect()->route('lapak.index');
        }

        $perizinan = Perizinan::where('nik_pedagang', $nik)
            ->where('id_event', $pendaftaran->id_event)
            ->first();

        return view('perizinan.create', [
            'pendaftaran' => $pendaftaran,
            'perizinan' => $perizinan,
            'jenisPerizinan' => self::JENIS_PERIZINAN,
        ]);
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        $pendaftaran = PendaftaranTenant::where('id_pendaftaran', $request->id_pendaftaran)
            ->where('nik_pedagang', $nik)
            ->where('status_pendaftaran', 'Menunggu Perizinan')
            ->firstOrFail();

        $data = $request->validate([
            'id_pendaftaran' => 'required|integer',
            'jenis_perizinan' => 'required|in:' . implode(',', self::JENIS_PERIZINAN),
            'tanggal_berlaku' => 'required|date',
            'dokumen_perizinan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $existing = Perizinan::where('nik_pedagang', $nik)
            ->where('id_event', $pendaftaran->id_event)
            ->first();

        if ($existing?->dokumen_perizinan) {
            Storage::disk('public')->delete($existing->dokumen_perizinan);
        }

        $path = $request->file('dokumen_perizinan')->store('perizinan', 'public');

        Perizinan::updateOrCreate(
            [
                'nik_pedagang' => $nik,
                'id_event' => $pendaftaran->id_event,
            ],
            [
                'jenis_perizinan' => $data['jenis_perizinan'],
                'tanggal_pengajuan' => now()->toDateString(),
                'tanggal_berlaku' => $data['tanggal_berlaku'],
                'status_perizinan' => 'Menunggu Verifikasi',
                'dokumen_perizinan' => $path,
            ]
        );

        $pendaftaran->update(['status_pendaftaran' => 'Menunggu Verifikasi']);

        return redirect()->route('home')->with('success', 'Dokumen perizinan berhasil diajukan. Pendaftaran menunggu verifikasi admin.');
    }
}
