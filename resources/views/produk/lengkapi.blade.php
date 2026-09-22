@extends('layouts.app')

@section('title', 'Lengkapi Data Produk - CFD Surabaya')

@section('content')
<div class="row justify-content-center py-2">
    <div class="col-12 col-lg-8">

        <div class="alert border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="background:var(--cfd-green-light); color:var(--cfd-green-dark);">
            <i class="bi bi-check-circle-fill fs-4"></i>
            <div>
                Lapak <strong>{{ $pendaftaran->lapak->nomor_lapak }}</strong>
                (Zona {{ $pendaftaran->lapak->kategori_lapak }}) pada event
                <strong>{{ $pendaftaran->event->nama_event }}</strong> berhasil Anda pilih.
                Satu langkah lagi, lengkapi data produk di bawah ini.
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-soft">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width:64px;height:64px;background:var(--cfd-green-light);">
                        <i class="bi bi-box-seam fs-2" style="color:var(--cfd-green);"></i>
                    </div>
                    <h2 class="fw-heading fw-bold mb-1">Lengkapi Data Produk</h2>
                    <p class="text-muted mb-0">Informasi ini akan ditinjau oleh admin sebelum lapak Anda diverifikasi.</p>
                </div>

                <form action="{{ route('produk.simpan') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="id_pendaftaran" value="{{ $pendaftaran->id_pendaftaran }}">

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror"
                                   placeholder="Contoh: Nasi Goreng Spesial" value="{{ old('nama_produk') }}" required>
                            @error('nama_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Produk</label>
                            <input type="number" name="jumlah_produk" min="1" class="form-control @error('jumlah_produk') is-invalid @enderror"
                                   placeholder="Contoh: 50" value="{{ old('jumlah_produk') }}" required>
                            @error('jumlah_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jenis Produk</label>
                            <select name="jenis_produk" class="form-select @error('jenis_produk') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Pilih Jenis Produk --</option>
                                @foreach($jenisProdukList as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis_produk') === $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Sesuaikan dengan zona lapak Anda ({{ $pendaftaran->lapak->kategori_lapak }}).</div>
                            @error('jenis_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Foto Produk <span class="text-muted fw-normal">(opsional, maks. 2MB)</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="fotoPreviewWrap" class="d-none">
                                    <img id="fotoPreview" src="" alt="Preview" class="rounded-3 border" style="width:90px;height:90px;object-fit:cover;">
                                </div>
                                <input type="file" name="foto_produk" id="fotoInput" accept="image/*"
                                       class="form-control @error('foto_produk') is-invalid @enderror">
                            </div>
                            @error('foto_produk')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan Tambahan <span class="text-muted fw-normal">(opsional)</span></label>
                            <textarea name="keterangan_tambahan" rows="3" class="form-control @error('keterangan_tambahan') is-invalid @enderror"
                                      placeholder="Contoh: harga rata-rata, jam operasional, kebutuhan listrik, dsb.">{{ old('keterangan_tambahan') }}</textarea>
                            @error('keterangan_tambahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-3">
                        <i class="bi bi-send-check me-1"></i> Kirim & Ajukan Verifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('fotoInput');
    const wrap = document.getElementById('fotoPreviewWrap');
    const preview = document.getElementById('fotoPreview');
    if (!input) return;

    input.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) {
            wrap.classList.add('d-none');
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            wrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
})();
</script>
@endpush
