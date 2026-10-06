@extends('layouts.app')

@section('title', 'Pengajuan Perizinan - CFD Surabaya')

@section('content')
<div class="row justify-content-center py-2">
    <div class="col-12 col-lg-8">
        <div class="alert border-0 shadow-sm mb-4" style="background:var(--cfd-green-light); color:var(--cfd-green-dark);">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-file-earmark-check-fill fs-3"></i>
                <div>
                    <strong>Langkah terakhir sebelum verifikasi.</strong><br>
                    Pendaftaran <strong>{{ $pendaftaran->event->nama_event }}</strong> · Lapak <strong>{{ $pendaftaran->lapak->nomor_lapak }}</strong>.
                    Lengkapi dokumen perizinan agar admin dapat memverifikasi pendaftaran Anda.
                </div>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-soft">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width:64px;height:64px;background:var(--cfd-green-light);">
                        <i class="bi bi-file-earmark-text fs-2" style="color:var(--cfd-green);"></i>
                    </div>
                    <h2 class="fw-heading fw-bold mb-1">Pengajuan Perizinan</h2>
                    <p class="text-muted mb-0">Dokumen ini akan diperiksa oleh admin CFD.</p>
                </div>

                <form action="{{ route('perizinan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_pendaftaran" value="{{ $pendaftaran->id_pendaftaran }}">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jenis Perizinan</label>
                            <select name="jenis_perizinan" class="form-select @error('jenis_perizinan') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Pilih Jenis Perizinan --</option>
                                @foreach($jenisPerizinan as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis_perizinan', $perizinan?->jenis_perizinan) === $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                            @error('jenis_perizinan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Berlaku</label>
                            <input type="date" name="tanggal_berlaku" value="{{ old('tanggal_berlaku', optional($perizinan?->tanggal_berlaku)->format('Y-m-d')) }}" class="form-control @error('tanggal_berlaku') is-invalid @enderror" required>
                            @error('tanggal_berlaku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dokumen Perizinan</label>
                            <input type="file" name="dokumen_perizinan" accept=".pdf,.jpg,.jpeg,.png" class="form-control @error('dokumen_perizinan') is-invalid @enderror" required>
                            <div class="form-text">PDF/JPG/PNG, maksimal 4 MB.</div>
                            @error('dokumen_perizinan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="alert alert-light border mt-4 mb-4 small">
                        <strong>Data yang diajukan:</strong><br>
                        {{ $pendaftaran->produk->nama_produk ?? '-' }} · {{ $pendaftaran->produk->kategori_produk ?? '-' }} · stok {{ $pendaftaran->produk->stok_produk ?? 0 }} unit
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-3">
                        <i class="bi bi-send-check me-1"></i> Ajukan Perizinan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
