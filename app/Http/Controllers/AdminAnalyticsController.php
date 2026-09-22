<?php

namespace App\Http\Controllers;

use App\Models\LaporanPenjualan;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        $laporan = LaporanPenjualan::with(['pendaftaran.pedagang', 'pendaftaran.event', 'pendaftaran.lapak'])
            ->get();

        $stok = $laporan->sum(fn ($item) => (int) ($item->pendaftaran->jumlah_produk ?? 0));
        $terjual = $laporan->sum('jumlah_terjual');
        $omzet = $laporan->sum(fn ($item) => (float) $item->total_pendapatan);
        $modal = $laporan->sum(fn ($item) => (float) $item->total_modal);
        $laba = $omzet - $modal;

        $summary = [
            'pedagang' => $laporan->pluck('pendaftaran.nik_pedagang')->filter()->unique()->count(),
            'laporan' => $laporan->count(),
            'stok' => $stok,
            'terjual' => $terjual,
            'sell_through' => $stok > 0 ? round(($terjual / $stok) * 100, 1) : 0,
            'omzet' => $omzet,
            'modal' => $modal,
            'laba' => $laba,
            'margin' => $omzet > 0 ? round(($laba / $omzet) * 100, 1) : 0,
        ];

        $perEvent = $laporan->groupBy(fn ($item) => $item->pendaftaran->id_event)
            ->map(function ($items) {
                $first = $items->first();
                $stok = $items->sum(fn ($i) => (int) ($i->pendaftaran->jumlah_produk ?? 0));
                $terjual = $items->sum('jumlah_terjual');
                $omzet = $items->sum(fn ($i) => (float) $i->total_pendapatan);
                $modal = $items->sum(fn ($i) => (float) $i->total_modal);
                $laba = $omzet - $modal;

                return [
                    'event' => $first->pendaftaran->event?->nama_event ?? 'Event',
                    'tanggal' => $first->pendaftaran->event?->tanggal_event,
                    'pedagang' => $items->pluck('pendaftaran.nik_pedagang')->unique()->count(),
                    'stok' => $stok,
                    'terjual' => $terjual,
                    'sell_through' => $stok > 0 ? round(($terjual / $stok) * 100, 1) : 0,
                    'omzet' => $omzet,
                    'modal' => $modal,
                    'laba' => $laba,
                    'margin' => $omzet > 0 ? round(($laba / $omzet) * 100, 1) : 0,
                ];
            })->sortBy('tanggal')->values();

        $perKategori = $laporan->groupBy(fn ($item) => $item->pendaftaran->jenis_produk ?: 'Tidak dikategorikan')
            ->map(function ($items, $kategori) {
                $stok = $items->sum(fn ($i) => (int) ($i->pendaftaran->jumlah_produk ?? 0));
                $terjual = $items->sum('jumlah_terjual');
                $omzet = $items->sum(fn ($i) => (float) $i->total_pendapatan);
                return [
                    'kategori' => $kategori,
                    'stok' => $stok,
                    'terjual' => $terjual,
                    'sell_through' => $stok > 0 ? round(($terjual / $stok) * 100, 1) : 0,
                    'omzet' => $omzet,
                ];
            })->sortByDesc('omzet')->values();

        $perPedagang = $laporan->groupBy(fn ($item) => $item->pendaftaran->nik_pedagang)
            ->map(function ($items) {
                $first = $items->first();
                $omzet = $items->sum(fn ($i) => (float) $i->total_pendapatan);
                $modal = $items->sum(fn ($i) => (float) $i->total_modal);
                $stok = $items->sum(fn ($i) => (int) ($i->pendaftaran->jumlah_produk ?? 0));
                $terjual = $items->sum('jumlah_terjual');
                return [
                    'nama' => $first->pendaftaran->pedagang?->nama_pedagang ?? 'Pedagang',
                    'usaha' => $first->pendaftaran->pedagang?->nama_usaha ?? '-',
                    'event' => $items->count(),
                    'terjual' => $terjual,
                    'sell_through' => $stok > 0 ? round(($terjual / $stok) * 100, 1) : 0,
                    'omzet' => $omzet,
                    'modal' => $modal,
                    'laba' => $omzet - $modal,
                ];
            })->sortByDesc('omzet')->values();

        return view('admin.analytics', compact('laporan', 'summary', 'perEvent', 'perKategori', 'perPedagang'));
    }
}
