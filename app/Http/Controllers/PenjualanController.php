<?php

namespace App\Http\Controllers;

use App\Models\EventCfd;
use App\Models\LaporanPenjualan;
use App\Models\Produk;
use App\Models\PendaftaranTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    public function laporan()
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;
        $eventSelesaiIds = EventCfd::sudahSelesai()->pluck('id_event');

        $pendaftaran = PendaftaranTenant::with(['event', 'lapak', 'produk', 'laporanPenjualan.detailPenjualan.produk'])
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

        $produk = $pendaftaran->produk;
        if (! $produk) {
            return back()->withErrors(['laporan' => 'Produk untuk pendaftaran ini belum tersedia di tabel produk.']);
        }

        $data = $request->validate([
            'jumlah_terjual' => 'required|integer|min:0|max:' . (int) $produk->stok_produk,
            'total_modal' => 'required|numeric|min:0|max:999999999999.99',
            'catatan_penjualan' => 'nullable|string|max:1000',
        ]);

        $totalPendapatan = (int) $data['jumlah_terjual'] * (int) $produk->harga;

        $laporan = LaporanPenjualan::updateOrCreate(
            ['id_pendaftaran' => $pendaftaran->id_pendaftaran],
            [
                'nik_pedagang' => $pendaftaran->nik_pedagang,
                'id_event' => $pendaftaran->id_event,
                'total_omzet' => $totalPendapatan,
                'jumlah_terjual' => $data['jumlah_terjual'],
                'total_pendapatan' => $totalPendapatan,
                'total_modal' => $data['total_modal'],
                'catatan_penjualan' => $data['catatan_penjualan'] ?? null,
                'tanggal_laporan' => $pendaftaran->laporanPenjualan?->tanggal_laporan ?? now(),
                'updated_at' => now(),
            ]
        );

        $laporan->detailPenjualan()->updateOrCreate(
            ['id_produk' => $produk->id_produk],
            [
                'jumlah_item_terjual' => $data['jumlah_terjual'],
                'harga_satuan' => $produk->harga,
                'subtotal' => $totalPendapatan,
            ]
        );

        return redirect()->route('penjualan.laporan')
            ->with('success', 'Laporan penjualan berhasil disimpan. Detail penjualan tercatat dan data langsung masuk ke analytics Anda.');
    }

    public function analytics()
    {
        $nik = Auth::guard('pedagang')->user()->nik_pedagang;

        $laporan = LaporanPenjualan::with(['pendaftaran.event', 'pendaftaran.lapak', 'pendaftaran.produk', 'detailPenjualan.produk'])
            ->whereHas('pendaftaran', fn ($q) => $q->where('nik_pedagang', $nik))
            ->get()
            ->sortBy(fn ($item) => optional($item->pendaftaran->event)->tanggal_event)
            ->values();

        $summary = $this->buildSummary($laporan);
        $insights = $this->buildInsights($laporan);

        $perEvent = $laporan->map(function ($item) {
            $p = $item->pendaftaran;
            $stok = (int) ($p->produk?->stok_produk ?? $p->jumlah_produk ?? 0);
            $terjual = (int) $item->jumlah_terjual;
            $omzet = (float) $item->total_pendapatan;
            $modal = (float) $item->total_modal;
            $laba = $omzet - $modal;

            return [
                'event' => $p->event?->nama_event ?? 'Event',
                'tanggal' => $p->event?->tanggal_event,
                'produk' => $p->produk?->nama_produk ?? $p->nama_produk,
                'jenis' => $p->produk?->kategori_produk ?? $p->jenis_produk,
                'stok' => $stok,
                'terjual' => $terjual,
                'sisa' => max(0, $stok - $terjual),
                'sell_through' => $stok > 0 ? round(($terjual / $stok) * 100, 1) : 0,
                'omzet' => $omzet,
                'modal' => $modal,
                'laba' => $laba,
                'margin' => $omzet > 0 ? round(($laba / $omzet) * 100, 1) : 0,
                'roi' => $modal > 0 ? round(($laba / $modal) * 100, 1) : 0,
                'omzet_per_unit' => $terjual > 0 ? round($omzet / $terjual, 0) : 0,
                'laba_per_unit' => $terjual > 0 ? round($laba / $terjual, 0) : 0,
                'modal_per_unit' => $terjual > 0 ? round($modal / $terjual, 0) : 0,
                'sisa_persen' => $stok > 0 ? round((max(0, $stok - $terjual) / $stok) * 100, 1) : 0,
            ];
        });

        return view('penjualan.analytics', compact('laporan', 'summary', 'perEvent', 'insights'));
    }

    private function buildInsights($laporan): array
    {
        $productGroups = $laporan->groupBy(fn ($item) => trim((string) ($item->pendaftaran->produk?->nama_produk ?? $item->pendaftaran->nama_produk ?? 'Produk tidak diketahui')));
        $typeGroups = $laporan->groupBy(fn ($item) => trim((string) ($item->pendaftaran->produk?->kategori_produk ?? $item->pendaftaran->jenis_produk ?? 'Jenis tidak diketahui')));
        $eventGroups = $laporan->groupBy(fn ($item) => (int) ($item->id_event ?? 0));

        $products = $productGroups->map(function ($items, $name) {
            $stock = $items->sum(fn ($i) => (int) ($i->pendaftaran->produk?->stok_produk ?? $i->pendaftaran->jumlah_produk ?? 0));
            $sold = $items->sum(fn ($i) => (int) $i->jumlah_terjual);
            $revenue = $items->sum(fn ($i) => (float) $i->total_pendapatan);
            return ['name' => $name, 'sold' => $sold, 'stock' => $stock, 'remaining' => max(0, $stock - $sold), 'revenue' => $revenue, 'sell_through' => $stock > 0 ? round($sold / $stock * 100, 1) : 0];
        })->values();

        $types = $typeGroups->map(function ($items, $name) {
            $sold = $items->sum(fn ($i) => (int) $i->jumlah_terjual);
            $revenue = $items->sum(fn ($i) => (float) $i->total_pendapatan);
            return ['name' => $name, 'sold' => $sold, 'revenue' => $revenue, 'reports' => $items->count()];
        })->values();

        $events = $eventGroups->map(function ($items) {
            $event = $items->first()->pendaftaran->event;
            $sold = $items->sum(fn ($i) => (int) $i->jumlah_terjual);
            $stock = $items->sum(fn ($i) => (int) ($i->pendaftaran->produk?->stok_produk ?? $i->pendaftaran->jumlah_produk ?? 0));
            $revenue = $items->sum(fn ($i) => (float) $i->total_pendapatan);
            $modal = $items->sum(fn ($i) => (float) $i->total_modal);
            return [
                'name' => $event?->nama_event ?? 'Event',
                'date' => $event?->tanggal_event,
                'sold' => $sold,
                'stock' => $stock,
                'revenue' => $revenue,
                'modal' => $modal,
                'sell_through' => $stock > 0 ? round($sold / $stock * 100, 1) : 0,
            ];
        })->values();

        $soldValues = $events->pluck('sold')->map(fn ($v) => (float) $v)->values();
        $revenueValues = $events->pluck('revenue')->map(fn ($v) => (float) $v)->values();
        $meanSold = $this->mean($soldValues);
        $stdSold = $this->stddev($soldValues);
        $meanRevenue = $this->mean($revenueValues);
        $stdRevenue = $this->stddev($revenueValues);

        $anomalies = $events->map(function ($event) use ($meanSold, $stdSold, $meanRevenue, $stdRevenue) {
            $zSold = $stdSold > 0 ? (($event['sold'] - $meanSold) / $stdSold) : 0;
            $zRevenue = $stdRevenue > 0 ? (($event['revenue'] - $meanRevenue) / $stdRevenue) : 0;
            $score = max(abs($zSold), abs($zRevenue));
            return array_merge($event, [
                'z_sold' => round($zSold, 2),
                'z_revenue' => round($zRevenue, 2),
                'anomaly_score' => round($score, 2),
                'is_anomaly' => $score >= 2,
            ]);
        })->filter(fn ($event) => $event['is_anomaly'])->values();

        $pairs = [
            'Stok awal vs unit terjual' => [$laporan->map(fn ($i) => (float) ($i->pendaftaran->produk?->stok_produk ?? $i->pendaftaran->jumlah_produk ?? 0)), $laporan->map(fn ($i) => (float) $i->jumlah_terjual)],
            'Modal vs omzet' => [$laporan->map(fn ($i) => (float) $i->total_modal), $laporan->map(fn ($i) => (float) $i->total_pendapatan)],
            'Unit terjual vs omzet' => [$laporan->map(fn ($i) => (float) $i->jumlah_terjual), $laporan->map(fn ($i) => (float) $i->total_pendapatan)],
        ];
        $correlations = collect($pairs)->map(function ($pair, $label) {
            return ['label' => $label, 'r' => $this->pearson($pair[0], $pair[1]), 'n' => $pair[0]->count()];
        })->values();

        return [
            'products' => $products,
            'types' => $types,
            'events' => $events,
            'best_product_sold' => $products->sortByDesc('sold')->first(),
            'best_product_revenue' => $products->sortByDesc('revenue')->first(),
            'most_leftover_product' => $products->sortByDesc('remaining')->first(),
            'best_type_sold' => $types->sortByDesc('sold')->first(),
            'best_event_sold' => $events->sortByDesc('sold')->first(),
            'best_event_revenue' => $events->sortByDesc('revenue')->first(),
            'statistics' => [
                'sold_mean' => round($meanSold, 2),
                'sold_median' => round($this->median($soldValues), 2),
                'sold_stddev' => round($stdSold, 2),
                'sold_min' => $soldValues->min(),
                'sold_max' => $soldValues->max(),
                'revenue_mean' => round($meanRevenue, 2),
                'revenue_median' => round($this->median($revenueValues), 2),
                'revenue_stddev' => round($stdRevenue, 2),
            ],
            'correlations' => $correlations,
            'anomalies' => $anomalies,
        ];
    }

    private function mean($values): float
    {
        return $values->count() ? $values->avg() : 0.0;
    }

    private function median($values): float
    {
        $sorted = $values->sort()->values();
        $n = $sorted->count();
        if ($n === 0) return 0.0;
        $mid = intdiv($n, 2);
        return $n % 2 ? (float) $sorted[$mid] : ((float) $sorted[$mid - 1] + (float) $sorted[$mid]) / 2;
    }

    private function stddev($values): float
    {
        $n = $values->count();
        if ($n < 2) return 0.0;
        $mean = $values->avg();
        $variance = $values->reduce(fn ($carry, $value) => $carry + (($value - $mean) ** 2), 0) / ($n - 1);
        return sqrt($variance);
    }

    private function pearson($x, $y): ?float
    {
        $pairs = $x->values()->zip($y->values())->filter(fn ($pair) => is_numeric($pair[0]) && is_numeric($pair[1]));
        $n = $pairs->count();
        if ($n < 2) return null;
        $mx = $pairs->avg(fn ($p) => $p[0]);
        $my = $pairs->avg(fn ($p) => $p[1]);
        $num = 0.0; $dx = 0.0; $dy = 0.0;
        foreach ($pairs as $p) {
            $a = $p[0] - $mx; $b = $p[1] - $my;
            $num += $a * $b; $dx += $a * $a; $dy += $b * $b;
        }
        if ($dx <= 0 || $dy <= 0) return null;
        return round($num / sqrt($dx * $dy), 3);
    }

    private function buildSummary($laporan): array
    {
        $stok = $laporan->sum(fn ($item) => (int) ($item->pendaftaran->produk?->stok_produk ?? $item->pendaftaran->jumlah_produk ?? 0));
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
            'roi' => $modal > 0 ? round(($laba / $modal) * 100, 1) : 0,
            'omzet_per_unit' => $terjual > 0 ? round($omzet / $terjual, 0) : 0,
            'laba_per_unit' => $terjual > 0 ? round($laba / $terjual, 0) : 0,
            'modal_per_unit' => $terjual > 0 ? round($modal / $terjual, 0) : 0,
            'sisa' => max(0, $stok - $terjual),
            'sisa_persen' => $stok > 0 ? round((max(0, $stok - $terjual) / $stok) * 100, 1) : 0,
        ];
    }
}
