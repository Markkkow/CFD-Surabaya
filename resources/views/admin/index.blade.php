@extends('layouts.app')

@section('title', 'Panel Admin - CFD Surabaya')

@push('styles')
<style>
    .zone-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .72rem;
        font-weight: 600;
        padding: .25rem .6rem;
        border-radius: 999px;
        background: #eef2f0;
        color: #55625d;
        margin: 0 .25rem .25rem 0;
    }
    .zone-dot { width: 8px; height: 8px; border-radius: 50%; }
    .admin-table th { font-size: .78rem; text-transform: uppercase; color: #8a978f; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h2 class="fw-heading fw-bold mb-1">
        <i class="bi bi-speedometer2 me-2" style="color:var(--cfd-green);"></i>Panel Admin
    </h2>
    <p class="text-muted mb-0">Kelola event CFD dan susun denah lapak per zona di sini.</p>
</div>

<div class="row g-4 mb-4">
    <!-- Form Tambah Event -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-soft h-100">
            <div class="card-body p-4">
                <h5 class="fw-heading fw-bold mb-3">
                    <i class="bi bi-calendar-plus me-2" style="color:var(--cfd-green);"></i>Tambah Event Baru
                </h5>
                <form action="{{ route('admin.events.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Event</label>
                        <input type="text" name="nama_event" class="form-control" placeholder="Contoh: CFD Jalan Darmo - Minggu Pagi" value="{{ old('nama_event') }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Event</label>
                            <input type="date" name="tanggal_event" class="form-control" value="{{ old('tanggal_event') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Buka Pendaftaran</label>
                            <input type="date" name="tanggal_buka_pendaftaran" class="form-control" value="{{ old('tanggal_buka_pendaftaran') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jalan Darmo, Surabaya" value="{{ old('lokasi') }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status Event</label>
                        <select name="status_event" class="form-select" required>
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        <div class="form-text">Beranda hanya menampilkan event <strong>Aktif</strong> yang tanggal dan jam selesainya belum lewat.</div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold rounded-3">
                        <i class="bi bi-plus-circle me-1"></i> Simpan Event
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Tambah Baris Lapak -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-soft h-100">
            <div class="card-body p-4">
                <h5 class="fw-heading fw-bold mb-3">
                    <i class="bi bi-grid-3x3-gap-fill me-2" style="color:var(--cfd-green);"></i>Tambah Baris Lapak
                </h5>
                <p class="small text-muted">
                    Isi satu baris kursi sekaligus untuk sebuah zona. Misalnya baris <strong>G</strong> dengan
                    <strong>8 kolom</strong> akan otomatis membuat lapak G1 sampai G8.
                </p>
                @if($activeEvents->isEmpty())
                    <div class="alert alert-warning border-0 small mb-0">Belum ada event aktif yang belum selesai. Tambahkan atau aktifkan event terlebih dahulu.</div>
                @else
                    <form action="{{ route('admin.lapak.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Event</label>
                            <select name="id_event" class="form-select" required>
                                @foreach($activeEvents as $event)
                                    <option value="{{ $event->id_event }}">{{ $event->nama_event }} — {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Zona Kategori</label>
                                <select name="kategori_lapak" class="form-select" required>
                                    @foreach($zonaList as $zona)
                                        <option value="{{ $zona }}">{{ $zona }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kode Baris</label>
                                <input type="text" name="baris" maxlength="2" class="form-control text-uppercase" placeholder="Misal: G" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Kolom (Kursi)</label>
                                <input type="number" name="jumlah_kolom" min="1" max="26" class="form-control" placeholder="Misal: 8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ukuran Lapak</label>
                                <input type="text" name="ukuran_lapak" class="form-control" placeholder="Misal: 2x2 m" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Lokasi / Keterangan</label>
                                <input type="text" name="lokasi_lapak" class="form-control" placeholder="Misal: Dekat pintu masuk utara">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-success w-100 fw-bold rounded-3">
                            <i class="bi bi-plus-circle me-1"></i> Tambahkan Baris Lapak
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Daftar Event -->
<div class="card border-0 rounded-4 shadow-soft">
    <div class="card-body p-4">
        <h5 class="fw-heading fw-bold mb-3">
            <i class="bi bi-list-check me-2" style="color:var(--cfd-green);"></i>Semua Event
        </h5>

        @if($events->isEmpty())
            <p class="text-muted mb-0">Belum ada event. Tambahkan lewat form di atas.</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Tanggal</th>
                            <th>Zona Lapak</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $event->nama_event }}</div>
                                    <div class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $event->lokasi }}</div>
                                </td>
                                <td class="small">
                                    {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}<br>
                                    <span class="text-muted">{{ $event->waktu_mulai }}&ndash;{{ $event->waktu_selesai }}</span>
                                </td>
                                <td>
                                    @forelse(($rekapZona[$event->id_event] ?? []) as $zona)
                                        <span class="zone-badge">
                                            <span class="zone-dot" style="background: {{ ['Kuliner' => '#e08e0b', 'Fashion' => '#c23b8f', 'Kerajinan' => '#7a4fd1', 'Jasa & Lainnya' => '#2b7fb8'][$zona->kategori_lapak] ?? '#999' }};"></span>
                                            {{ $zona->kategori_lapak }} ({{ $zona->total }})
                                        </span>
                                    @empty
                                        <span class="small text-muted">Belum ada lapak</span>
                                    @endforelse
                                    <div class="small text-muted mt-1">Total: {{ $event->lapak_count }} lapak</div>
                                </td>
                                <td>
                                    <form action="{{ route('admin.events.status', $event->id_event) }}" method="POST" class="d-flex align-items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status_event" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;">
                                            @foreach(['Aktif', 'Nonaktif', 'Selesai'] as $status)
                                                <option value="{{ $status }}" {{ $event->status_event === $status ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.events.destroy', $event->id_event) }}" method="POST"
                                          onsubmit="return confirm('Hapus event ini beserta seluruh lapak dan pendaftarannya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
