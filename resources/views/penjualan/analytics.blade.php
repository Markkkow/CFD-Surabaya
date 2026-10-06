@extends('layouts.app')

@section('title', 'Analytics Penjualan Saya - CFD Surabaya')

@push('styles')
<style>
    .analytics-hero{background:linear-gradient(125deg,#173b2d,#1a7a4c 65%,#38a16f);color:#fff;border-radius:1.6rem;padding:2rem;overflow:hidden;position:relative}.analytics-hero:after{content:"";position:absolute;width:280px;height:280px;border-radius:50%;right:-100px;top:-130px;background:rgba(255,255,255,.08)}
    .metric-card{border:1px solid #e6ede9;border-radius:1.2rem;background:#fff;height:100%}.metric-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:var(--cfd-green-light);color:var(--cfd-green);font-size:1.2rem}.metric-label{color:#718078;font-size:.82rem}.metric-value{font-family:'Poppins',sans-serif;font-size:1.45rem;font-weight:800}.chart-card{border:0;border-radius:1.3rem}.insight{border-left:4px solid var(--cfd-green);background:#f4faf7;border-radius:.8rem;padding:1rem 1.1rem}.table thead th{white-space:nowrap;font-size:.82rem;color:#66746e}.status-profit{background:#eaf8f0;color:#157347}.status-loss{background:#fff0f0;color:#b02a37}.indicator-card{border:1px solid #e6ede9;border-radius:1.15rem;background:#fff;height:100%;padding:1rem}.indicator-title{font-size:.78rem;color:#718078;text-transform:uppercase;letter-spacing:.02em}.indicator-value{font-family:'Poppins',sans-serif;font-size:1.2rem;font-weight:800}.indicator-help{font-size:.76rem;color:#7a8680}.signal{display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;padding:.25rem .55rem;font-size:.72rem;font-weight:700}.signal-positive{background:#eaf8f0;color:#157347}.signal-warning{background:#fff7e6;color:#946200}.signal-negative{background:#fff0f0;color:#b02a37}.signal-neutral{background:#eef2f0;color:#5d6963}
    .analytics-section-head{display:flex;justify-content:space-between;align-items:flex-end;gap:1rem}.insight-panel{border:1px solid #e6ede9;border-radius:1.25rem;background:#fff;padding:1.35rem}.insight-panel-icon{width:42px;height:42px;border-radius:12px;background:#edf7f1;color:var(--cfd-green);display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:1rem}.insight-panel-label,.mini-analysis-label{font-size:.76rem;text-transform:uppercase;letter-spacing:.04em;color:#7a8680;font-weight:700}.insight-main{font-family:'Poppins',sans-serif;font-size:1.25rem;font-weight:800;margin-top:.3rem;line-height:1.25}.insight-main-help,.mini-analysis-help{font-size:.8rem;color:#7a8680;margin-top:.25rem}.insight-divider{height:1px;background:#edf1ef;margin:1rem 0}.insight-row{display:flex;justify-content:space-between;align-items:center;gap:1rem;font-size:.82rem;padding:.38rem 0}.insight-row span{color:#718078}.insight-row strong{text-align:right;max-width:58%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.mini-analysis-card{border:1px solid #e6ede9;border-radius:1.15rem;background:#fff;padding:1.15rem 1.25rem}.mini-analysis-value{font-size:1.05rem;font-weight:800;margin-top:.35rem}
    @media(max-width:767.98px){.analytics-section-head{align-items:flex-start}.analytics-section-head .badge{display:none}.insight-panel{padding:1.15rem}.insight-main{font-size:1.1rem}}

</style>
@endpush

@section('content')
<div class="analytics-hero shadow-soft mb-4">
    <div class="position-relative" style="z-index:1">
        <span class="badge rounded-pill bg-light text-success mb-3"><i class="bi bi-graph-up-arrow me-1"></i>Analytics Pribadi</span>
        <h1 class="h2 fw-bold mb-2">Performa Penjualan Saya</h1>
        <p class="mb-0 opacity-75">Ringkasan seluruh laporan penjualan Anda untuk melihat omzet, modal, laba/rugi, dan tingkat produk terjual.</p>
    </div>
</div>

@if($laporan->isEmpty())
    <div class="card border-0 shadow-sm rounded-4"><div class="card-body text-center py-5"><i class="bi bi-bar-chart fs-1 text-success"></i><h4 class="fw-bold mt-3">Belum ada data analytics</h4><p class="text-muted">Isi laporan penjualan setelah event selesai agar analytics dapat dihitung.</p><a href="{{ route('penjualan.laporan') }}" class="btn btn-success">Buka Laporan Penjualan</a></div></div>
@else
@php
    $revenueMin = $insights['events']->min('revenue');
    $revenueMax = $insights['events']->max('revenue');
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-cash-stack"></i></div><div class="metric-label">Total omzet</div><div class="metric-value">Rp{{ number_format($summary['omzet'],0,',','.') }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-wallet2"></i></div><div class="metric-label">Total modal</div><div class="metric-value">Rp{{ number_format($summary['modal'],0,',','.') }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-activity"></i></div><div class="metric-label">Laba / rugi</div><div class="metric-value {{ $summary['laba'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $summary['laba'] < 0 ? '-' : '' }}Rp{{ number_format(abs($summary['laba']),0,',','.') }}</div><small class="text-muted">Margin {{ number_format($summary['margin'],1,',','.') }}%</small></div></div>
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-box-seam"></i></div><div class="metric-label">Produk terjual</div><div class="metric-value">{{ number_format($summary['terjual']) }} / {{ number_format($summary['stok']) }}</div><small class="text-muted">Sell-through {{ number_format($summary['sell_through'],1,',','.') }}%</small></div></div>
</div>

@php
    $latest = $perEvent->last();
    $previous = $perEvent->count() > 1 ? $perEvent->get($perEvent->count() - 2) : null;
    $omzetChange = $previous && $previous['omzet'] > 0
        ? round((($latest['omzet'] - $previous['omzet']) / $previous['omzet']) * 100, 1)
        : null;
    $profitChange = $previous && $previous['laba'] != 0
        ? round((($latest['laba'] - $previous['laba']) / abs($previous['laba'])) * 100, 1)
        : null;

    if ($summary['laba'] < 0) {
        $performanceLabel = 'Rugi';
        $performanceClass = 'signal-negative';
    } elseif ($summary['sell_through'] >= 80 && $summary['margin'] >= 20) {
        $performanceLabel = 'Penjualan kuat';
        $performanceClass = 'signal-positive';
    } elseif ($summary['sell_through'] < 50) {
        $performanceLabel = 'Banyak stok tersisa';
        $performanceClass = 'signal-warning';
    } else {
        $performanceLabel = 'Perlu dipantau';
        $performanceClass = 'signal-neutral';
    }
@endphp

<div class="analytics-section-head mb-3">
    <div>
        <h5 class="fw-bold mb-1">Insight Utama</h5>
        <small class="text-muted">Ringkasan temuan paling penting dari data penjualan Anda.</small>
    </div>
    <span class="badge rounded-pill bg-light text-dark">Data Science</span>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="insight-panel shadow-sm h-100">
            <div class="insight-panel-icon"><i class="bi bi-box-seam"></i></div>
            <div class="insight-panel-label">Produk</div>
            <div class="insight-main">{{ $insights['best_product_sold']['name'] ?? '-' }}</div>
            <div class="insight-main-help">{{ number_format($insights['best_product_sold']['sold'] ?? 0) }} unit terjual</div>
            <div class="insight-divider"></div>
            <div class="insight-row"><span>Omzet tertinggi</span><strong>{{ $insights['best_product_revenue']['name'] ?? '-' }}</strong></div>
            <div class="insight-row"><span>Nilai omzet</span><strong>Rp{{ number_format($insights['best_product_revenue']['revenue'] ?? 0,0,',','.') }}</strong></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="insight-panel shadow-sm h-100">
            <div class="insight-panel-icon"><i class="bi bi-calendar-event"></i></div>
            <div class="insight-panel-label">Event</div>
            <div class="insight-main">{{ $insights['best_event_sold']['name'] ?? '-' }}</div>
            <div class="insight-main-help">{{ number_format($insights['best_event_sold']['sold'] ?? 0) }} unit terjual</div>
            <div class="insight-divider"></div>
            <div class="insight-row"><span>Omzet tertinggi</span><strong>{{ $insights['best_event_revenue']['name'] ?? '-' }}</strong></div>
            <div class="insight-row"><span>Nilai omzet</span><strong>Rp{{ number_format($insights['best_event_revenue']['revenue'] ?? 0,0,',','.') }}</strong></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="insight-panel shadow-sm h-100">
            <div class="insight-panel-icon"><i class="bi bi-archive"></i></div>
            <div class="insight-panel-label">Stok & Produk</div>
            <div class="insight-main">{{ number_format($summary['sell_through'],1,',','.') }}%</div>
            <div class="insight-main-help">tingkat produk terjual</div>
            <div class="insight-divider"></div>
            <div class="insight-row"><span>Paling banyak tersisa</span><strong>{{ $insights['most_leftover_product']['name'] ?? '-' }}</strong></div>
            <div class="insight-row"><span>Sisa stok</span><strong>{{ number_format($insights['most_leftover_product']['remaining'] ?? 0) }} unit</strong></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="mini-analysis-card shadow-sm h-100">
            <div class="mini-analysis-label">Jenis produk terlaris</div>
            <div class="mini-analysis-value">{{ $insights['best_type_sold']['name'] ?? '-' }}</div>
            <div class="mini-analysis-help">{{ number_format($insights['best_type_sold']['sold'] ?? 0) }} unit terjual berdasarkan jenis produk.</div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mini-analysis-card shadow-sm h-100">
            <div class="mini-analysis-label">Status data penjualan</div>
            <div class="d-flex align-items-center gap-2 mt-2"><span class="signal {{ $performanceClass }}">{{ $performanceLabel }}</span><span class="mini-analysis-help">{{ $summary['laporan'] }} event dianalisis</span></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-4">
        <h5 class="fw-bold mb-1">Statistik Penjualan</h5><small class="text-muted">Mengukur pusat data dan variasi unit terjual serta omzet.</small>
        <div class="table-responsive mt-3"><table class="table table-sm align-middle mb-0"><thead><tr><th>Ukuran</th><th>Unit Terjual</th><th>Omzet</th></tr></thead><tbody>
            <tr><td>Mean</td><td>{{ number_format($insights['statistics']['sold_mean'],2,',','.') }}</td><td>Rp{{ number_format($insights['statistics']['revenue_mean'],0,',','.') }}</td></tr>
            <tr><td>Median</td><td>{{ number_format($insights['statistics']['sold_median'],2,',','.') }}</td><td>Rp{{ number_format($insights['statistics']['revenue_median'],0,',','.') }}</td></tr>
            <tr><td>Standar deviasi</td><td>{{ number_format($insights['statistics']['sold_stddev'],2,',','.') }}</td><td>Rp{{ number_format($insights['statistics']['revenue_stddev'],0,',','.') }}</td></tr>
            <tr><td>Min – Max</td><td>{{ number_format($insights['statistics']['sold_min']) }} – {{ number_format($insights['statistics']['sold_max']) }}</td><td>Rp{{ number_format($revenueMin,0,',','.') }} – Rp{{ number_format($revenueMax,0,',','.') }}</td></tr>
        </tbody></table></div>
    </div></div></div>
    <div class="col-lg-5"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-4">
        <h5 class="fw-bold mb-1">Hubungan Antar Variabel</h5><small class="text-muted">Korelasi Pearson dari data laporan yang tersedia.</small>
        <div class="mt-3">
        @foreach($insights['correlations'] as $corr)
            @php $r = $corr['r']; $absR = $r === null ? 0 : abs($r); $strength = $r === null ? 'Tidak dapat dihitung' : ($absR >= .7 ? 'kuat' : ($absR >= .4 ? 'sedang' : 'lemah')); @endphp
            <div class="border-bottom py-2"><div class="d-flex justify-content-between gap-2"><span class="small">{{ $corr['label'] }}</span><strong>{{ $r === null ? '-' : number_format($r,3,',','.') }}</strong></div><small class="text-muted">{{ $strength }} · n={{ $corr['n'] }}</small></div>
        @endforeach
        </div>
    </div></div></div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4"><div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Deteksi Anomali Penjualan</h5><small class="text-muted">Event ditandai jika nilai unit terjual atau omzet memiliki |z-score| ≥ 2.</small></div><span class="signal {{ $insights['anomalies']->isNotEmpty() ? 'signal-warning' : 'signal-positive' }}">{{ $insights['anomalies']->count() }} anomali</span></div>
    @if($insights['anomalies']->isEmpty())
        <div class="alert alert-light border mb-0"><strong>Tidak ada anomali terdeteksi.</strong> Dengan data saat ini, tidak ada event yang cukup jauh dari pola rata-rata.</div>
    @else
        <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Event</th><th>Unit Terjual</th><th>z Unit</th><th>Omzet</th><th>z Omzet</th><th>Interpretasi</th></tr></thead><tbody>
        @foreach($insights['anomalies'] as $a)
            <tr><td><strong>{{ $a['name'] }}</strong><div class="small text-muted">{{ $a['date'] ? \Carbon\Carbon::parse($a['date'])->format('d/m/Y') : '-' }}</div></td><td>{{ number_format($a['sold']) }}</td><td>{{ number_format($a['z_sold'],2,',','.') }}</td><td>Rp{{ number_format($a['revenue'],0,',','.') }}</td><td>{{ number_format($a['z_revenue'],2,',','.') }}</td><td><span class="signal signal-warning">Di luar pola umum</span></td></tr>
        @endforeach
        </tbody></table></div>
    @endif
</div></div>

<div class="row g-3 mb-4">
    <div class="col-lg-6"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-4"><h6 class="fw-bold mb-3">Produk berdasarkan jumlah terjual</h6><div style="height:260px"><canvas id="topProductsChart"></canvas></div></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body p-4"><h6 class="fw-bold mb-3">Event berdasarkan jumlah terjual</h6><div style="height:260px"><canvas id="topEventsChart"></canvas></div></div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8"><div class="card chart-card shadow-sm h-100"><div class="card-body p-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="fw-bold mb-1">Omzet, Modal & Laba per Event</h5><small class="text-muted">Bandingkan hasil keuangan setiap event.</small></div></div><div style="height:320px"><canvas id="financeChart"></canvas></div></div></div></div>
    <div class="col-lg-4"><div class="card chart-card shadow-sm h-100"><div class="card-body p-4"><h5 class="fw-bold mb-1">Produk Terjual</h5><small class="text-muted">Terjual dibanding sisa stok.</small><div style="height:280px" class="mt-3"><canvas id="productChart"></canvas></div></div></div></div>
</div>

@php
    $best = $perEvent->sortByDesc('omzet')->first();
    $bestSell = $perEvent->sortByDesc('sell_through')->first();
@endphp
<div class="insight mb-4">
    <div class="fw-bold mb-1"><i class="bi bi-lightbulb-fill me-1 text-success"></i>Ringkasan analisa</div>
    <div class="text-muted small">
        Dari {{ $summary['laporan'] }} event yang sudah dilaporkan, Anda menjual <strong class="text-dark">{{ $summary['terjual'] }} dari {{ $summary['stok'] }} produk</strong> ({{ number_format($summary['sell_through'],1,',','.') }}%).
        @if($best) Omzet tertinggi tercatat pada <strong class="text-dark">{{ $best['event'] }}</strong> sebesar <strong class="text-dark">Rp{{ number_format($best['omzet'],0,',','.') }}</strong>. @endif
        @if($bestSell) Tingkat produk terjual tertinggi ada pada <strong class="text-dark">{{ $bestSell['event'] }}</strong> sebesar <strong class="text-dark">{{ number_format($bestSell['sell_through'],1,',','.') }}%</strong>. @endif
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white p-4 border-0"><h5 class="fw-bold mb-1">Detail Per Event</h5><small class="text-muted">Angka dihitung otomatis dari laporan yang Anda kirim.</small></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="ps-4">Event</th><th>Produk</th><th>Terjual / Stok</th><th>Sell-through</th><th>Omzet</th><th>Modal</th><th>ROI</th><th>Laba/Unit</th><th class="pe-4">Laba/Rugi</th></tr></thead><tbody>
    @foreach($perEvent as $row)
        <tr><td class="ps-4"><strong>{{ $row['event'] }}</strong><div class="small text-muted">{{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}</div></td><td>{{ $row['produk'] }}<div class="small text-muted">{{ $row['jenis'] }}</div></td><td>{{ $row['terjual'] }} / {{ $row['stok'] }}</td><td><strong>{{ number_format($row['sell_through'],1,',','.') }}%</strong><div class="progress mt-1" style="height:5px"><div class="progress-bar bg-success" style="width:{{ min(100,$row['sell_through']) }}%"></div></div></td><td>Rp{{ number_format($row['omzet'],0,',','.') }}</td><td>Rp{{ number_format($row['modal'],0,',','.') }}</td><td><span class="{{ $row['roi'] >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">{{ number_format($row['roi'],1,',','.') }}%</span></td><td>Rp{{ number_format(abs($row['laba_per_unit']),0,',','.') }}</td><td class="pe-4"><span class="badge rounded-pill {{ $row['laba'] >= 0 ? 'status-profit' : 'status-loss' }}">{{ $row['laba'] < 0 ? '-' : '' }}Rp{{ number_format(abs($row['laba']),0,',','.') }}</span></td></tr>
    @endforeach
    </tbody></table></div>
</div>
@endif
@endsection

@if($laporan->isNotEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const eventData = @json($perEvent);
const insightProducts = @json($insights['products']);
const insightEvents = @json($insights['events']);
new Chart(document.getElementById('topProductsChart'), {type:'bar',data:{labels:insightProducts.map(x=>x.name),datasets:[{label:'Unit terjual',data:insightProducts.map(x=>x.sold)}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true}}}});
new Chart(document.getElementById('topEventsChart'), {type:'bar',data:{labels:insightEvents.map(x=>x.name),datasets:[{label:'Unit terjual',data:insightEvents.map(x=>x.sold)}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
new Chart(document.getElementById('financeChart'), {type:'bar',data:{labels:eventData.map(x=>x.event),datasets:[{label:'Omzet',data:eventData.map(x=>x.omzet)},{label:'Modal',data:eventData.map(x=>x.modal)},{label:'Laba/Rugi',data:eventData.map(x=>x.laba)}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'Rp'+new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v)}}}}});
new Chart(document.getElementById('productChart'), {type:'doughnut',data:{labels:['Terjual','Sisa'],datasets:[{data:[{{ $summary['terjual'] }},{{ max(0,$summary['stok']-$summary['terjual']) }}]}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}});
</script>
@endpush
@endif
