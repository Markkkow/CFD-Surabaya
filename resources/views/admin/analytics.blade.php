@extends('layouts.app')

@section('title', 'Analytics Penjualan Admin - CFD Surabaya')

@push('styles')
<style>
    .admin-hero{background:linear-gradient(125deg,#17251f,#244c3a 65%,#1a7a4c);color:#fff;border-radius:1.6rem;padding:2rem;position:relative;overflow:hidden}.admin-hero:after{content:"";position:absolute;width:260px;height:260px;border-radius:50%;right:-80px;top:-120px;background:rgba(255,255,255,.08)}
    .metric-card{border:1px solid #e4ebe7;border-radius:1.15rem;background:#fff;height:100%}.metric-icon{width:42px;height:42px;border-radius:12px;background:#e9f6ef;color:#167548;display:flex;align-items:center;justify-content:center}.metric-label{font-size:.8rem;color:#728078}.metric-value{font-family:'Poppins',sans-serif;font-weight:800;font-size:1.35rem}.chart-card{border:0;border-radius:1.3rem}.analysis-box{background:#f4faf7;border:1px solid #dfeee6;border-radius:1rem;padding:1rem 1.15rem}.table thead th{white-space:nowrap;font-size:.8rem;color:#66746e}.nav-pills .nav-link.active{background:var(--cfd-green)}
</style>
@endpush

@section('content')
<div class="admin-hero shadow-soft mb-4"><div class="position-relative" style="z-index:1"><span class="badge rounded-pill bg-light text-success mb-3"><i class="bi bi-speedometer2 me-1"></i>Admin Analytics</span><h1 class="h2 fw-bold mb-2">Analisa Penjualan Seluruh Pedagang</h1><p class="mb-0 opacity-75">Rekap laporan pasca-event untuk membaca performa omzet, modal, laba/rugi, produk terjual, kategori, event, dan pedagang.</p></div></div>

@if($laporan->isEmpty())
<div class="card border-0 shadow-sm rounded-4"><div class="card-body text-center py-5"><i class="bi bi-database-x fs-1 text-success"></i><h4 class="fw-bold mt-3">Belum ada laporan penjualan</h4><p class="text-muted mb-0">Analytics akan terisi otomatis setelah penjual mengirim laporan dari event yang telah selesai.</p></div></div>
@else
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-2"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-2"><i class="bi bi-people"></i></div><div class="metric-label">Pedagang melapor</div><div class="metric-value">{{ number_format($summary['pedagang']) }}</div></div></div>
    <div class="col-6 col-xl-2"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-2"><i class="bi bi-box-seam"></i></div><div class="metric-label">Produk terjual</div><div class="metric-value">{{ number_format($summary['terjual']) }}</div><small class="text-muted">dari {{ number_format($summary['stok']) }}</small></div></div>
    <div class="col-6 col-xl-2"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-2"><i class="bi bi-percent"></i></div><div class="metric-label">Sell-through</div><div class="metric-value">{{ number_format($summary['sell_through'],1,',','.') }}%</div></div></div>
    <div class="col-6 col-xl-2"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-2"><i class="bi bi-cash-stack"></i></div><div class="metric-label">Total omzet</div><div class="metric-value">Rp{{ number_format($summary['omzet'],0,',','.') }}</div></div></div>
    <div class="col-6 col-xl-2"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-2"><i class="bi bi-wallet2"></i></div><div class="metric-label">Total modal</div><div class="metric-value">Rp{{ number_format($summary['modal'],0,',','.') }}</div></div></div>
    <div class="col-6 col-xl-2"><div class="metric-card shadow-sm p-3"><div class="metric-icon mb-2"><i class="bi bi-activity"></i></div><div class="metric-label">Laba / rugi</div><div class="metric-value {{ $summary['laba'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $summary['laba'] < 0 ? '-' : '' }}Rp{{ number_format(abs($summary['laba']),0,',','.') }}</div><small class="text-muted">Margin {{ number_format($summary['margin'],1,',','.') }}%</small></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8"><div class="card chart-card shadow-sm h-100"><div class="card-body p-4"><h5 class="fw-bold mb-1">Performa Keuangan per Event</h5><small class="text-muted">Akumulasi laporan seluruh pedagang pada masing-masing event.</small><div style="height:330px" class="mt-3"><canvas id="eventFinanceChart"></canvas></div></div></div></div>
    <div class="col-lg-4"><div class="card chart-card shadow-sm h-100"><div class="card-body p-4"><h5 class="fw-bold mb-1">Omzet per Kategori</h5><small class="text-muted">Kontribusi berdasarkan jenis produk.</small><div style="height:300px" class="mt-3"><canvas id="categoryChart"></canvas></div></div></div></div>
</div>

@php
    $eventTop = $perEvent->sortByDesc('omzet')->first();
    $categoryTop = $perKategori->first();
    $sellerTop = $perPedagang->first();
@endphp
<div class="analysis-box mb-4"><div class="fw-bold mb-2"><i class="bi bi-lightbulb-fill text-success me-1"></i>Ringkasan analisa sistem</div><div class="row g-2 small text-muted">
    <div class="col-md-4">Total <strong class="text-dark">{{ $summary['terjual'] }} dari {{ $summary['stok'] }} produk</strong> terjual, setara sell-through <strong class="text-dark">{{ number_format($summary['sell_through'],1,',','.') }}%</strong>.</div>
    <div class="col-md-4">@if($eventTop) Event dengan omzet tercatat paling besar: <strong class="text-dark">{{ $eventTop['event'] }}</strong> (Rp{{ number_format($eventTop['omzet'],0,',','.') }}). @endif</div>
    <div class="col-md-4">@if($categoryTop) Kategori dengan kontribusi omzet terbesar: <strong class="text-dark">{{ $categoryTop['kategori'] }}</strong>. @endif @if($sellerTop) Pedagang dengan omzet tercatat terbesar: <strong class="text-dark">{{ $sellerTop['usaha'] }}</strong>. @endif</div>
</div></div>

<ul class="nav nav-pills gap-2 mb-3" role="tablist"><li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#by-event">Per Event</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#by-seller">Per Pedagang</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#by-category">Per Kategori</button></li></ul>
<div class="tab-content">
<div class="tab-pane fade show active" id="by-event"><div class="card border-0 shadow-sm rounded-4 overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="ps-4">Event</th><th>Pedagang</th><th>Terjual / Stok</th><th>Sell-through</th><th>Omzet</th><th>Modal</th><th class="pe-4">Laba/Rugi</th></tr></thead><tbody>@foreach($perEvent as $r)<tr><td class="ps-4"><strong>{{ $r['event'] }}</strong><div class="small text-muted">{{ $r['tanggal'] ? \Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y') : '-' }}</div></td><td>{{ $r['pedagang'] }}</td><td>{{ $r['terjual'] }} / {{ $r['stok'] }}</td><td>{{ number_format($r['sell_through'],1,',','.') }}%</td><td>Rp{{ number_format($r['omzet'],0,',','.') }}</td><td>Rp{{ number_format($r['modal'],0,',','.') }}</td><td class="pe-4 fw-bold {{ $r['laba'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $r['laba'] < 0 ? '-' : '' }}Rp{{ number_format(abs($r['laba']),0,',','.') }}</td></tr>@endforeach</tbody></table></div></div></div>
<div class="tab-pane fade" id="by-seller"><div class="card border-0 shadow-sm rounded-4 overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="ps-4">Pedagang / Usaha</th><th>Laporan</th><th>Produk terjual</th><th>Sell-through</th><th>Omzet</th><th>Modal</th><th class="pe-4">Laba/Rugi</th></tr></thead><tbody>@foreach($perPedagang as $r)<tr><td class="ps-4"><strong>{{ $r['usaha'] }}</strong><div class="small text-muted">{{ $r['nama'] }}</div></td><td>{{ $r['event'] }}</td><td>{{ $r['terjual'] }}</td><td>{{ number_format($r['sell_through'],1,',','.') }}%</td><td>Rp{{ number_format($r['omzet'],0,',','.') }}</td><td>Rp{{ number_format($r['modal'],0,',','.') }}</td><td class="pe-4 fw-bold {{ $r['laba'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $r['laba'] < 0 ? '-' : '' }}Rp{{ number_format(abs($r['laba']),0,',','.') }}</td></tr>@endforeach</tbody></table></div></div></div>
<div class="tab-pane fade" id="by-category"><div class="card border-0 shadow-sm rounded-4 overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="ps-4">Kategori</th><th>Terjual / Stok</th><th>Sell-through</th><th class="pe-4">Omzet</th></tr></thead><tbody>@foreach($perKategori as $r)<tr><td class="ps-4"><strong>{{ $r['kategori'] }}</strong></td><td>{{ $r['terjual'] }} / {{ $r['stok'] }}</td><td>{{ number_format($r['sell_through'],1,',','.') }}%</td><td class="pe-4">Rp{{ number_format($r['omzet'],0,',','.') }}</td></tr>@endforeach</tbody></table></div></div></div>
</div>
@endif
@endsection

@if($laporan->isNotEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const eventRows=@json($perEvent), categoryRows=@json($perKategori);
new Chart(document.getElementById('eventFinanceChart'),{type:'bar',data:{labels:eventRows.map(x=>x.event),datasets:[{label:'Omzet',data:eventRows.map(x=>x.omzet)},{label:'Modal',data:eventRows.map(x=>x.modal)},{label:'Laba/Rugi',data:eventRows.map(x=>x.laba)}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'Rp'+new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v)}}}}});
new Chart(document.getElementById('categoryChart'),{type:'doughnut',data:{labels:categoryRows.map(x=>x.kategori),datasets:[{data:categoryRows.map(x=>x.omzet)}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}});
</script>
@endpush
@endif
