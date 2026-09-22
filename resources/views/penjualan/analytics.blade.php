@extends('layouts.app')

@section('title', 'Analytics Penjualan Saya - CFD Surabaya')

@push('styles')
<style>
    .analytics-hero{background:linear-gradient(125deg,#173b2d,#1a7a4c 65%,#38a16f);color:#fff;border-radius:1.6rem;padding:2rem;overflow:hidden;position:relative}.analytics-hero:after{content:"";position:absolute;width:280px;height:280px;border-radius:50%;right:-100px;top:-130px;background:rgba(255,255,255,.08)}
    .metric-card{border:1px solid #e6ede9;border-radius:1.2rem;background:#fff;height:100%}.metric-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:var(--cfd-green-light);color:var(--cfd-green);font-size:1.2rem}.metric-label{color:#718078;font-size:.82rem}.metric-value{font-family:'Poppins',sans-serif;font-size:1.45rem;font-weight:800}.chart-card{border:0;border-radius:1.3rem}.insight{border-left:4px solid var(--cfd-green);background:#f4faf7;border-radius:.8rem;padding:1rem 1.1rem}.table thead th{white-space:nowrap;font-size:.82rem;color:#66746e}.status-profit{background:#eaf8f0;color:#157347}.status-loss{background:#fff0f0;color:#b02a37}
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
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-cash-stack"></i></div><div class="metric-label">Total omzet</div><div class="metric-value">Rp{{ number_format($summary['omzet'],0,',','.') }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-wallet2"></i></div><div class="metric-label">Total modal</div><div class="metric-value">Rp{{ number_format($summary['modal'],0,',','.') }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-activity"></i></div><div class="metric-label">Laba / rugi</div><div class="metric-value {{ $summary['laba'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $summary['laba'] < 0 ? '-' : '' }}Rp{{ number_format(abs($summary['laba']),0,',','.') }}</div><small class="text-muted">Margin {{ number_format($summary['margin'],1,',','.') }}%</small></div></div>
    <div class="col-6 col-lg-3"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-3"><i class="bi bi-box-seam"></i></div><div class="metric-label">Produk terjual</div><div class="metric-value">{{ number_format($summary['terjual']) }} / {{ number_format($summary['stok']) }}</div><small class="text-muted">Sell-through {{ number_format($summary['sell_through'],1,',','.') }}%</small></div></div>
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
    <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="ps-4">Event</th><th>Produk</th><th>Terjual / Stok</th><th>Sell-through</th><th>Omzet</th><th>Modal</th><th class="pe-4">Laba/Rugi</th></tr></thead><tbody>
    @foreach($perEvent as $row)
        <tr><td class="ps-4"><strong>{{ $row['event'] }}</strong><div class="small text-muted">{{ $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') : '-' }}</div></td><td>{{ $row['produk'] }}<div class="small text-muted">{{ $row['jenis'] }}</div></td><td>{{ $row['terjual'] }} / {{ $row['stok'] }}</td><td><strong>{{ number_format($row['sell_through'],1,',','.') }}%</strong><div class="progress mt-1" style="height:5px"><div class="progress-bar bg-success" style="width:{{ min(100,$row['sell_through']) }}%"></div></div></td><td>Rp{{ number_format($row['omzet'],0,',','.') }}</td><td>Rp{{ number_format($row['modal'],0,',','.') }}</td><td class="pe-4"><span class="badge rounded-pill {{ $row['laba'] >= 0 ? 'status-profit' : 'status-loss' }}">{{ $row['laba'] < 0 ? '-' : '' }}Rp{{ number_format(abs($row['laba']),0,',','.') }}</span></td></tr>
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
new Chart(document.getElementById('financeChart'), {type:'bar',data:{labels:eventData.map(x=>x.event),datasets:[{label:'Omzet',data:eventData.map(x=>x.omzet)},{label:'Modal',data:eventData.map(x=>x.modal)},{label:'Laba/Rugi',data:eventData.map(x=>x.laba)}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'Rp'+new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v)}}}}});
new Chart(document.getElementById('productChart'), {type:'doughnut',data:{labels:['Terjual','Sisa'],datasets:[{data:[{{ $summary['terjual'] }},{{ max(0,$summary['stok']-$summary['terjual']) }}]}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}});
</script>
@endpush
@endif
