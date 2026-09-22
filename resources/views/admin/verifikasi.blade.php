@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran - CFD Surabaya')

@push('styles')
<style>
    .verif-card { border: 1px solid #edf1ef; border-radius: 1.1rem; overflow: hidden; margin-bottom: 1.25rem; }
    .verif-head {
        background: var(--cfd-green-light);
        padding: 1rem 1.3rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
    }
    .verif-body { padding: 1.3rem; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
    .info-item .label { font-size: .72rem; text-transform: uppercase; font-weight: 700; color: #8a978f; margin-bottom: .15rem; }
    .info-item .value { font-weight: 600; color: #1e2a26; }
    .status-pill { padding: .3rem .8rem; border-radius: 999px; font-size: .78rem; font-weight: 700; }
    .status-menunggu { background: #fff4d6; color: #8a6d00; }
    .status-terverifikasi { background: #dff3e6; color: var(--cfd-green-dark); }
    .status-ditolak { background: #fde2e2; color: #a3282c; }
    .product-photo-thumb {
        width: 96px;
        height: 96px;
        object-fit: cover;
        cursor: zoom-in;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .product-photo-thumb:hover {
        transform: scale(1.04);
        box-shadow: 0 .45rem 1rem rgba(0,0,0,.14);
    }
    .photo-zoom-stage {
        min-height: 60vh;
        max-height: 72vh;
        overflow: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #111;
        border-radius: .75rem;
    }
    #zoomProductImage {
        max-width: 100%;
        max-height: 68vh;
        object-fit: contain;
        transition: transform .15s ease;
        transform-origin: center center;
        user-select: none;
        -webkit-user-drag: none;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h2 class="fw-heading fw-bold mb-1">
        <i class="bi bi-patch-check-fill me-2" style="color:var(--cfd-green);"></i>Verifikasi Pendaftaran Tenant
    </h2>
    <p class="text-muted mb-0">Tinjau data produk pedagang sebelum lapak resmi diverifikasi.</p>
</div>

<h5 class="fw-heading fw-bold mb-3">
    <i class="bi bi-hourglass-split me-2" style="color:#c98a00;"></i>Menunggu Verifikasi
    <span class="badge rounded-pill text-bg-warning ms-1">{{ $menunggu->count() }}</span>
</h5>

@if($menunggu->isEmpty())
    <div class="alert alert-light border shadow-sm text-center py-4 mb-5">
        <i class="bi bi-emoji-smile fs-3 d-block mb-2 text-muted"></i>
        Tidak ada pendaftaran yang menunggu verifikasi saat ini.
    </div>
@else
    <div class="mb-5">
        @foreach($menunggu as $p)
            <div class="verif-card shadow-sm">
                <div class="verif-head">
                    <div>
                        <div class="fw-bold"><i class="bi bi-person-badge me-1"></i>{{ $p->pedagang->nama_pedagang }} &middot; {{ $p->pedagang->nama_usaha }}</div>
                        <div class="small text-muted">Mendaftar {{ \Carbon\Carbon::parse($p->tanggal_pendaftaran)->format('d M Y, H:i') }}</div>
                    </div>
                    <span class="status-pill status-menunggu"><i class="bi bi-clock-history me-1"></i>Menunggu Verifikasi</span>
                </div>
                <div class="verif-body">
                    <div class="info-grid mb-3">
                        <div class="info-item">
                            <div class="label">Event</div>
                            <div class="value">{{ $p->event->nama_event }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">Lapak</div>
                            <div class="value">{{ $p->lapak->nomor_lapak }} &middot; Zona {{ $p->lapak->kategori_lapak }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">Kontak</div>
                            <div class="value">{{ $p->pedagang->no_telepon }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">NIK</div>
                            <div class="value">{{ $p->pedagang->nik_pedagang }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex gap-3 mb-3">
                        @if($p->foto_produk)
                            <img src="{{ asset('storage/' . $p->foto_produk) }}"
                                 alt="Foto produk {{ $p->nama_produk }}"
                                 class="rounded-3 border flex-shrink-0 product-photo-thumb js-product-photo"
                                 data-bs-toggle="modal"
                                 data-bs-target="#productPhotoModal"
                                 data-photo-src="{{ asset('storage/' . $p->foto_produk) }}"
                                 data-photo-name="{{ $p->nama_produk }}"
                                 title="Klik untuk memperbesar foto">
                        @else
                            <div class="rounded-3 border d-flex align-items-center justify-content-center flex-shrink-0 text-muted"
                                 style="width:96px;height:96px;background:#f6f8f7;">
                                <i class="bi bi-image fs-3"></i>
                            </div>
                        @endif
                        <div class="info-grid flex-grow-1">
                            <div class="info-item">
                                <div class="label">Nama Produk</div>
                                <div class="value">{{ $p->nama_produk }}</div>
                            </div>
                            <div class="info-item">
                                <div class="label">Jenis Produk</div>
                                <div class="value">{{ $p->jenis_produk }}</div>
                            </div>
                            <div class="info-item">
                                <div class="label">Jumlah Produk</div>
                                <div class="value">{{ $p->jumlah_produk }}</div>
                            </div>
                        </div>
                    </div>
                    @if($p->keterangan_tambahan)
                        <div class="info-item mb-3">
                            <div class="label">Keterangan Tambahan</div>
                            <div class="value fw-normal">{{ $p->keterangan_tambahan }}</div>
                        </div>
                    @endif

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <form action="{{ route('admin.verifikasi.terima', $p->id_pendaftaran) }}" method="POST"
                              onsubmit="return confirm('Verifikasi pendaftaran ini?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="bi bi-check2-circle me-1"></i> Verifikasi / Terima
                            </button>
                        </form>

                        <button type="button" class="btn btn-outline-danger fw-bold" data-bs-toggle="collapse" data-bs-target="#tolak-{{ $p->id_pendaftaran }}">
                            <i class="bi bi-x-circle me-1"></i> Tolak
                        </button>
                    </div>

                    <div class="collapse mt-3" id="tolak-{{ $p->id_pendaftaran }}">
                        <form action="{{ route('admin.verifikasi.tolak', $p->id_pendaftaran) }}" method="POST"
                              onsubmit="return confirm('Tolak pendaftaran ini? Lapak akan dikembalikan menjadi tersedia.');"
                              class="p-3 rounded-3" style="background:#fdf2f2;">
                            @csrf
                            @method('PATCH')
                            <label class="form-label fw-semibold small">Alasan Penolakan (opsional, akan tersimpan sebagai catatan)</label>
                            <div class="input-group">
                                <input type="text" name="catatan_admin" class="form-control" placeholder="Contoh: data produk tidak lengkap">
                                <button type="submit" class="btn btn-danger fw-bold">Kirim Penolakan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<h5 class="fw-heading fw-bold mb-3">
    <i class="bi bi-clock-history me-2" style="color:var(--cfd-green);"></i>Riwayat Verifikasi Terakhir
</h5>

@if($riwayat->isEmpty())
    <p class="text-muted">Belum ada riwayat verifikasi.</p>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr class="small text-uppercase text-muted">
                    <th>Pedagang</th>
                    <th>Produk</th>
                    <th>Lapak</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p->pedagang->nama_pedagang ?? '-' }}</div>
                            <div class="small text-muted">{{ $p->pedagang->nama_usaha ?? '' }}</div>
                        </td>
                        <td>{{ $p->nama_produk }}</td>
                        <td>{{ $p->lapak->nomor_lapak ?? '-' }} <span class="small text-muted">({{ $p->lapak->kategori_lapak ?? '-' }})</span></td>
                        <td>
                            @if($p->status_pendaftaran === 'Terverifikasi')
                                <span class="status-pill status-terverifikasi">Terverifikasi</span>
                            @else
                                <span class="status-pill status-ditolak">Ditolak</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $p->catatan_admin ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<!-- Modal Zoom Foto Produk -->
<div class="modal fade" id="productPhotoModal" tabindex="-1" aria-labelledby="productPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="productPhotoModalLabel">
                        <i class="bi bi-zoom-in me-2"></i>Foto Produk
                    </h5>
                    <div class="small text-muted" id="productPhotoName"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-3">
                <div class="photo-zoom-stage" id="photoZoomStage">
                    <img id="zoomProductImage" src="" alt="Foto produk diperbesar">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <span class="small text-muted">Gunakan tombol + / − untuk memperbesar atau memperkecil.</span>
                <div class="btn-group" role="group" aria-label="Kontrol zoom foto">
                    <button type="button" class="btn btn-outline-secondary" id="zoomOutBtn" title="Perkecil">
                        <i class="bi bi-dash-lg"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="zoomResetBtn">100%</button>
                    <button type="button" class="btn btn-outline-secondary" id="zoomInBtn" title="Perbesar">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('productPhotoModal');
    const image = document.getElementById('zoomProductImage');
    const name = document.getElementById('productPhotoName');
    const resetBtn = document.getElementById('zoomResetBtn');
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    const stage = document.getElementById('photoZoomStage');
    let scale = 1;

    function applyZoom() {
        image.style.transform = `scale(${scale})`;
        resetBtn.textContent = `${Math.round(scale * 100)}%`;
    }

    function resetZoom() {
        scale = 1;
        stage.scrollTop = 0;
        stage.scrollLeft = 0;
        applyZoom();
    }

    modalEl.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        image.src = trigger.getAttribute('data-photo-src');
        name.textContent = trigger.getAttribute('data-photo-name') || '';
        resetZoom();
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        image.src = '';
        name.textContent = '';
        resetZoom();
    });

    zoomInBtn.addEventListener('click', function () {
        scale = Math.min(3, scale + 0.25);
        applyZoom();
    });

    zoomOutBtn.addEventListener('click', function () {
        scale = Math.max(0.5, scale - 0.25);
        applyZoom();
    });

    resetBtn.addEventListener('click', resetZoom);

    image.addEventListener('dblclick', function () {
        scale = scale === 1 ? 2 : 1;
        applyZoom();
    });
});
</script>
@endpush
