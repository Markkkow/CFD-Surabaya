@extends('layouts.app')

@section('title', 'Laporan Penjualan - CFD Surabaya')

@push('styles')
<style>
    .page-hero { background:linear-gradient(125deg,#0f5132,var(--cfd-green)); color:#fff; border-radius:1.6rem; padding:2rem; position:relative; overflow:hidden; }
    .page-hero:after { content:""; position:absolute; width:220px; height:220px; border-radius:50%; right:-70px; top:-95px; background:rgba(255,255,255,.09); }
    .report-card { border:1px solid #e6ede9; border-radius:1.25rem; overflow:hidden; }
    .report-card .card-header { background:#fff; border-bottom:1px solid #edf1ef; }
    .metric-box { background:#f7faf8; border:1px solid #e5ece8; border-radius:1rem; padding:.85rem 1rem; }
    .metric-box small { color:#748079; display:block; margin-bottom:.1rem; }
    .money-preview { border-radius:1rem; background:#eef8f3; border:1px solid #d4ebdf; padding:1rem; }
    .empty-state { border:1px dashed #cbd8d1; border-radius:1.4rem; background:#fff; padding:2.6rem 1.5rem; }
    .section-title { font-family:'Poppins',sans-serif; font-weight:700; }
</style>
@endpush

@section('content')
<div class="page-hero shadow-soft mb-4">
    <div class="position-relative" style="z-index:1">
        <span class="badge rounded-pill bg-light text-success mb-3"><i class="bi bi-receipt-cutoff me-1"></i>Pasca Event</span>
        <h1 class="fw-bold h2 mb-2">Laporan Penjualan</h1>
        <p class="mb-0 opacity-75" style="max-width:720px">Isi hasil penjualan setelah event selesai. Data ini menjadi sumber analytics pribadi Anda dan rekap analytics admin.</p>
    </div>
</div>

<div class="d-flex justify-content-between align-items-end gap-3 mb-3">
    <div>
        <h3 class="section-title h4 mb-1">Perlu dilaporkan</h3>
        <p class="text-muted mb-0">Hanya pendaftaran terverifikasi dari event yang sudah selesai.</p>
    </div>
    <span class="badge rounded-pill text-bg-warning px-3 py-2">{{ $belumLapor->count() }} belum diisi</span>
</div>

@if($belumLapor->isEmpty())
    <div class="empty-state text-center mb-5">
        <i class="bi bi-check2-circle fs-1 text-success"></i>
        <h5 class="fw-bold mt-2">Semua laporan sudah lengkap</h5>
        <p class="text-muted mb-3">Saat event lain selesai dan pendaftaran Anda terverifikasi, laporan baru akan muncul di sini.</p>
        <a href="{{ route('penjualan.analytics') }}" class="btn btn-success"><i class="bi bi-graph-up-arrow me-1"></i>Lihat Analytics Saya</a>
    </div>
@else
    <div class="row g-4 mb-5">
        @foreach($belumLapor as $p)
            <div class="col-12">
                <div class="card report-card shadow-sm">
                    <div class="card-header p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <div>
                                <div class="small text-success fw-bold text-uppercase mb-1">{{ $p->event->nama_event }}</div>
                                <h4 class="fw-bold mb-1">{{ $p->nama_produk }}</h4>
                                <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($p->event->tanggal_event)->translatedFormat('d F Y') }} &nbsp;·&nbsp; <i class="bi bi-geo-alt me-1"></i>{{ $p->event->lokasi }}</div>
                            </div>
                            <span class="badge align-self-start rounded-pill bg-success-subtle text-success px-3 py-2">Lapak {{ $p->lapak->nomor_lapak ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="metric-box mb-3"><small>Produk dibawa / stok awal</small><strong class="fs-4">{{ number_format($p->jumlah_produk) }}</strong> unit</div>
                                <div class="metric-box"><small>Jenis produk</small><strong>{{ $p->jenis_produk }}</strong></div>
                            </div>
                            <div class="col-lg-8">
                                <form method="POST" action="{{ route('penjualan.simpan', $p) }}" class="sales-form" data-stock="{{ $p->jumlah_produk }}">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Produk terjual</label>
                                            <div class="input-group"><input type="number" class="form-control sold-input" name="jumlah_terjual" min="0" max="{{ $p->jumlah_produk }}" value="{{ old('jumlah_terjual', 0) }}" required><span class="input-group-text">unit</span></div>
                                            <div class="form-text">Maks. {{ $p->jumlah_produk }} unit.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Total pendapatan / omzet</label>
                                            <div class="input-group"><span class="input-group-text">Rp</span><input type="number" class="form-control revenue-input" name="total_pendapatan" min="0" step="1000" value="{{ old('total_pendapatan', 0) }}" required></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Total modal</label>
                                            <div class="input-group"><span class="input-group-text">Rp</span><input type="number" class="form-control capital-input" name="total_modal" min="0" step="1000" value="{{ old('total_modal', 0) }}" required></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Catatan penjualan <span class="text-muted fw-normal">(opsional)</span></label>
                                            <textarea name="catatan_penjualan" rows="2" maxlength="1000" class="form-control" placeholder="Contoh: produk paling ramai dibeli pukul 08.00, ada sisa stok karena hujan...">{{ old('catatan_penjualan') }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <div class="money-preview d-flex flex-wrap justify-content-between align-items-center gap-3">
                                                <div><small class="text-muted d-block">Preview hasil</small><strong class="preview-copy">0 dari {{ $p->jumlah_produk }} produk terjual · Sisa {{ $p->jumlah_produk }}</strong></div>
                                                <div class="text-md-end"><small class="text-muted d-block">Estimasi laba/rugi</small><strong class="fs-5 profit-preview">Rp0</strong></div>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-success px-4 fw-bold"><i class="bi bi-save2 me-1"></i>Simpan Laporan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="d-flex justify-content-between align-items-end gap-3 mb-3">
    <div><h3 class="section-title h4 mb-1">Riwayat laporan</h3><p class="text-muted mb-0">Data yang sudah Anda kirim dan dapat dipakai untuk analytics.</p></div>
    <a href="{{ route('penjualan.analytics') }}" class="btn btn-outline-success"><i class="bi bi-bar-chart-line me-1"></i>Analytics Saya</a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-4">Event / Produk</th><th>Terjual</th><th>Omzet</th><th>Modal</th><th>Laba/Rugi</th><th class="pe-4">Laporan</th></tr></thead>
            <tbody>
            @forelse($sudahLapor as $p)
                @php($r = $p->laporanPenjualan)
                @php($laba = (float)$r->total_pendapatan - (float)$r->total_modal)
                <tr>
                    <td class="ps-4"><strong>{{ $p->event->nama_event }}</strong><div class="small text-muted">{{ $p->nama_produk }}</div></td>
                    <td><strong>{{ $r->jumlah_terjual }}</strong> / {{ $p->jumlah_produk }}</td>
                    <td>Rp{{ number_format($r->total_pendapatan,0,',','.') }}</td>
                    <td>Rp{{ number_format($r->total_modal,0,',','.') }}</td>
                    <td class="fw-bold {{ $laba >= 0 ? 'text-success' : 'text-danger' }}">{{ $laba < 0 ? '-' : '' }}Rp{{ number_format(abs($laba),0,',','.') }}</td>
                    <td class="pe-4"><span class="small text-muted">{{ optional($r->tanggal_laporan)->format('d/m/Y H:i') }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada laporan penjualan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rupiah = value => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(value || 0);
    document.querySelectorAll('.sales-form').forEach(form => {
        const stock = Number(form.dataset.stock || 0);
        const sold = form.querySelector('.sold-input');
        const revenue = form.querySelector('.revenue-input');
        const capital = form.querySelector('.capital-input');
        const copy = form.querySelector('.preview-copy');
        const profit = form.querySelector('.profit-preview');
        const update = () => {
            const s = Math.max(0, Math.min(stock, Number(sold.value || 0)));
            const p = Number(revenue.value || 0) - Number(capital.value || 0);
            copy.textContent = `${s} dari ${stock} produk terjual · Sisa ${Math.max(0, stock-s)}`;
            profit.textContent = (p < 0 ? '- ' : '') + rupiah(Math.abs(p));
            profit.classList.toggle('text-danger', p < 0);
            profit.classList.toggle('text-success', p >= 0);
        };
        [sold,revenue,capital].forEach(el => el.addEventListener('input', update));
        update();
    });
</script>
@endpush
