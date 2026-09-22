<?php

namespace App\Http\Controllers;

use App\Models\EventCfd;
use App\Models\LaporanPenjualan;
use App\Models\PendaftaranTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    public function laporan()
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;
        $eventSelesaiIds = EventCfd::sudahSelesai()->pluck('id_event');

        $pendaftaran = PendaftaranTenant::with(['event', 'lapak', 'laporanPenjualan'])
            ->where('nik_pedagang', $nik)
            ->where('status_pendaftaran', 'Terverifikasi')
            ->whereIn('id_event', $eventSelesaiIds)
            ->orderByDesc('id_event')
            ->get();

        $belumLapor = $pendaftaran->whereNull('laporanPenjualan');
        $sudahLapor = $pendaftaran->whereNotNull('laporanPenjualan');

        return view('penjualan.laporan', compact('belumLapor', 'sudahLapor'));
    }

    public function simpanLaporan(Request $request, PendaftaranTenant $pendaftaran)
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        if ($pendaftaran->nik_pedagang !== $nik || $pendaftaran->status_pendaftaran !== 'Terverifikasi') {
            abort(403);
        }

        $eventSudahSelesai = EventCfd::sudahSelesai()
            ->where('id_event', $pendaftaran->id_event)
            ->exists();

        if (! $eventSudahSelesai) {
            return back()->withErrors(['laporan' => 'Laporan penjualan baru dapat diisi setelah waktu event selesai.']);
        }

        $data = $request->validate([
            'jumlah_terjual' => 'required|integer|min:0|max:' . (int) $pendaftaran->jumlah_produk,
            'total_pendapatan' => 'required|numeric|min:0|max:999999999999.99',
            'total_modal' => 'required|numeric|min:0|max:999999999999.99',
            'catatan_penjualan' => 'nullable|string|max:1000',
        ]);

        LaporanPenjualan::updateOrCreate(
            ['id_pendaftaran' => $pendaftaran->id_pendaftaran],
            [
                'nik_pedagang' => $pendaftaran->nik_pedagang,
                'id_event' => $pendaftaran->id_event,
                'total_omzet' => $data['total_pendapatan'],
                ...$data,
                'tanggal_laporan' => $pendaftaran->laporanPenjualan?->tanggal_laporan ?? now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('penjualan.laporan')
            ->with('success', 'Laporan penjualan berhasil disimpan. Data langsung masuk ke analytics Anda.');
    }

    public function analytics()
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        $laporan = LaporanPenjualan::with(['pendaftaran.event', 'pendaftaran.lapak'])
            ->whereHas('pendaftaran', fn ($q) => $q->where('nik_pedagang', $nik))
            ->get()
            ->sortBy(fn ($item) => optional($item->pendaftaran->event)->tanggal_event)
            ->values();

        $summary = $this->buildSummary($laporan);

        $perEvent = $laporan->map(function ($item) {
            $p = $item->pendaftaran;
            $stok = (int) ($p->jumlah_produk ?? 0);
            $terjual = (int) $item->jumlah_terjual;
            $omzet = (float) $item->total_pendapatan;
            $modal = (float) $item->total_modal;
            $laba = $omzet - $modal;

            return [
                'event' => $p->event?->nama_event ?? 'Event',
                'tanggal' => $p->event?->tanggal_event,
                'produk' => $p->nama_produk,
                'jenis' => $p->jenis_produk,
                'stok' => $stok,
                'terjual' => $terjual,
                'sisa' => max(0, $stok - $terjual),
                'sell_through' => $stok > 0 ? round(($terjual / $stok) * 100, 1) : 0,
                'omzet' => $omzet,
                'modal' => $modal,
                'laba' => $laba,
                'margin' => $omzet > 0 ? round(($laba / $omzet) * 100, 1) : 0,
            ];
        });

        return view('penjualan.analytics', compact('laporan', 'summary', 'perEvent'));
    }

    private function buildSummary($laporan): array
    {
        $stok = $laporan->sum(fn ($item) => (int) ($item->pendaftaran->jumlah_produk ?? 0));
        $terjual = $laporan->sum('jumlah_terjual');
        $omzet = $laporan->sum(fn ($item) => (float) $item->total_pendapatan);
        $modal = $laporan->sum(fn ($item) => (float) $item->total_modal);
        $laba = $omzet - $modal;

        return [
            'laporan' => $laporan->count(),
            'stok' => $stok,
            'terjual' => $terjual,
            'sell_through' => $stok > 0 ? round(($terjual / $stok) * 100, 1) : 0,
            'omzet' => $omzet,
            'modal' => $modal,
            'laba' => $laba,
            'margin' => $omzet > 0 ? round(($laba / $omzet) * 100, 1) : 0,
        ];
    }
}
