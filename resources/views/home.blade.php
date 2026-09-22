@extends('layouts.app')

@section('title', 'Beranda - CFD Surabaya')

@push('styles')
<style>
    .hero {
        position: relative;
        border-radius: 2rem;
        overflow: hidden;
        background: linear-gradient(125deg, #0f5132 0%, var(--cfd-green) 58%, #2ca56a 100%);
        color: #fff;
        padding: 3.75rem 2.5rem;
        isolation: isolate;
    }
    .hero-photo {
        position: absolute;
        inset: 0 auto 0 12%;
        width: 78%;
        z-index: -2;
        background-image: url('{{ asset('images/cfd-hero.jpg') }}');
        background-size: cover;
        background-position: 55% center;
        opacity: .96;
        -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0,0,0,.08) 8%, rgba(0,0,0,.45) 18%, #000 32%, #000 68%, rgba(0,0,0,.5) 82%, rgba(0,0,0,.12) 94%, transparent 100%);
        mask-image: linear-gradient(90deg, transparent 0%, rgba(0,0,0,.08) 8%, rgba(0,0,0,.45) 18%, #000 32%, #000 68%, rgba(0,0,0,.5) 82%, rgba(0,0,0,.12) 94%, transparent 100%);
    }
    .hero-photo::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15,81,50,.02), rgba(15,81,50,.16));
    }
    .hero::before, .hero::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        z-index: -1;
    }
    .hero::before { width: 320px; height: 320px; top: -145px; right: -65px; background: rgba(255,255,255,.09); }
    .hero::after { width: 230px; height: 230px; bottom: -125px; left: 22%; background: rgba(255,159,28,.18); }
    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 999px;
        padding: .45rem 1rem;
        font-size: .86rem;
        font-weight: 700;
    }
    .stat-chip {
        min-height: 104px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.22);
        backdrop-filter: blur(8px);
        border-radius: 1.15rem;
        padding: 1rem;
    }
    .stat-chip .num { font-size: 1.65rem; font-weight: 800; line-height: 1; margin-bottom: .45rem; }

    .section-kicker { color: var(--cfd-green); font-size: .8rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .event-card {
        position: relative;
        transition: transform .2s ease, box-shadow .2s ease;
        border: 1px solid #e8eeeb !important;
    }
    .event-card:hover { transform: translateY(-5px); box-shadow: 0 18px 38px rgba(30,42,38,.12) !important; }
    .event-date {
        width: 64px;
        min-width: 64px;
        border-radius: 1rem;
        background: var(--cfd-green-light);
        color: var(--cfd-green-dark);
        text-align: center;
        padding: .65rem .4rem;
    }
    .event-date .day { display:block; font-family:'Poppins',sans-serif; font-size:1.5rem; font-weight:800; line-height:1; }
    .event-date .month { display:block; font-size:.7rem; font-weight:800; text-transform:uppercase; margin-top:.25rem; }
    .event-meta {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        color: #66746e;
        font-size: .9rem;
    }
    .event-meta i { color: var(--cfd-green); margin-top: .08rem; }
    .active-badge { background: #e8f7ef; color: #157347; border: 1px solid #c8ead8; }
    .empty-events {
        border: 1px dashed #cbd8d1;
        background: linear-gradient(180deg, #fff, #f7faf8);
        border-radius: 1.5rem;
        padding: 3.5rem 1.5rem;
    }
    @media (max-width: 991.98px) {
        .hero-photo {
            left: 8%;
            width: 86%;
            opacity: .82;
            background-position: 55% center;
        }
    }
    @media (max-width: 767.98px) {
        .hero { padding: 2.5rem 1.4rem; border-radius: 1.4rem; }
        .hero h1 { font-size: 2rem; }
        .hero-photo {
            left: 0;
            width: 100%;
            opacity: .42;
            background-position: 53% center;
        }
    }
</style>
@endpush

@section('content')
@auth('pedagang')
    @if(($pendingSalesReportsCount ?? 0) > 0)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px"><i class="bi bi-receipt-cutoff fs-5"></i></div>
                <div><strong class="d-block mb-1">Ada {{ $pendingSalesReportsCount }} laporan penjualan yang belum diisi</strong><span class="small">Event Anda sudah selesai. Isi hasil penjualan agar analytics Anda tetap lengkap.</span></div>
            </div>
            <a href="{{ route('penjualan.laporan') }}" class="btn btn-dark flex-shrink-0">Isi Laporan <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    @endif
@endauth
<div class="hero shadow-soft mb-5">
    <div class="hero-photo" aria-hidden="true"></div>
    <div class="row align-items-center g-4 position-relative" style="z-index:1">
        <div class="col-lg-8">
            <span class="hero-kicker mb-3"><i class="bi bi-stars"></i> Ekonomi Kreatif Surabaya</span>
            <h1 class="fw-heading fw-bold display-6 mb-3">Kelola dan daftar lapak CFD<br class="d-none d-md-block"> dengan lebih mudah.</h1>
            <p class="fs-5 mb-4" style="max-width: 660px; opacity:.9;">
                Temukan event CFD yang masih aktif, pilih area lapak sesuai kategori usaha,
                dan selesaikan pendaftaran tanpa antre.
            </p>
            @auth('pedagang')
                <a href="{{ route('lapak.index') }}" class="btn btn-cfd-accent btn-lg px-4 fw-bold shadow-sm">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Pilih Area Lapak
                </a>
            @else
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('login') }}" class="btn btn-cfd-accent btn-lg px-4 fw-bold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Mendaftar
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-cfd-outline btn-lg px-4 fw-bold">Daftar Pedagang</a>
                </div>
            @endauth
        </div>
        <div class="col-lg-4">
            <div class="row g-3">
                <div class="col-6"><div class="stat-chip"><div class="num">{{ $events->count() }}</div><div class="small">Event Aktif</div></div></div>
                <div class="col-6"><div class="stat-chip"><div class="num">4</div><div class="small">Zona Lapak</div></div></div>
                <div class="col-6"><div class="stat-chip"><div class="num"><i class="bi bi-cup-hot"></i></div><div class="small">Kuliner</div></div></div>
                <div class="col-6"><div class="stat-chip"><div class="num"><i class="bi bi-bag-heart"></i></div><div class="small">UMKM Lokal</div></div></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-4">
    <div>
        <div class="section-kicker mb-1">Jadwal Tersedia</div>
        <h2 class="fw-heading fw-bold mb-1">Daftar Event Aktif</h2>
        <p class="text-muted mb-0">Hanya event yang belum melewati waktu selesai yang ditampilkan di sini.</p>
    </div>
    @if($events->isNotEmpty())
        <span class="badge active-badge rounded-pill px-3 py-2 align-self-start align-self-md-auto">
            <i class="bi bi-broadcast-pin me-1"></i>{{ $events->count() }} event tersedia
        </span>
    @endif
</div>

@if($events->isEmpty())
    <div class="empty-events text-center shadow-sm">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width:72px;height:72px;background:var(--cfd-green-light);color:var(--cfd-green);">
            <i class="bi bi-calendar2-check fs-2"></i>
        </div>
        <h4 class="fw-bold">Belum ada event aktif</h4>
        <p class="text-muted mb-0">Event yang sudah selesai otomatis tidak ditampilkan agar informasi di beranda tetap akurat.</p>
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @foreach($events as $event)
            @php($eventDate = \Carbon\Carbon::parse($event->tanggal_event))
            <div class="col">
                <div class="event-card card h-100 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                            <div class="event-date">
                                <span class="day">{{ $eventDate->format('d') }}</span>
                                <span class="month">{{ $eventDate->translatedFormat('M') }}</span>
                            </div>
                            <span class="badge active-badge rounded-pill px-3 py-2">
                                <span class="d-inline-block rounded-circle bg-success me-1" style="width:7px;height:7px;"></span> Aktif
                            </span>
                        </div>

                        <h4 class="card-title fw-bold mb-3">{{ $event->nama_event }}</h4>
                        <div class="d-grid gap-2 mb-4">
                            <div class="event-meta"><i class="bi bi-geo-alt-fill"></i><span>{{ $event->lokasi }}</span></div>
                            <div class="event-meta"><i class="bi bi-calendar-event-fill"></i><span>{{ $eventDate->translatedFormat('l, d F Y') }}</span></div>
                            <div class="event-meta"><i class="bi bi-clock-fill"></i><span>{{ substr($event->waktu_mulai, 0, 5) }} - {{ substr($event->waktu_selesai, 0, 5) }} WIB</span></div>
                            <div class="event-meta"><i class="bi bi-pencil-square"></i><span>Pendaftaran dibuka {{ \Carbon\Carbon::parse($event->tanggal_buka_pendaftaran)->translatedFormat('d F Y') }}</span></div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                        @auth('pedagang')
                            <a href="{{ route('lapak.index') }}" class="btn btn-success w-100 fw-bold rounded-3 py-2">
                                Pilih Lapak <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-success w-100 fw-bold rounded-3 py-2">Login untuk Daftar</a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
