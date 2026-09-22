@extends('layouts.app')

@section('title', 'Daftar Pedagang - CFD Surabaya')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-12 col-lg-9 col-xl-8">
        <div class="card border-0 rounded-4 shadow-soft overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width:64px;height:64px;background:var(--cfd-green-light);">
                        <i class="bi bi-shop fs-2" style="color:var(--cfd-green);"></i>
                    </div>
                    <h2 class="fw-heading fw-bold mb-1">Daftar Pedagang / UMKM</h2>
                    <p class="text-muted mb-0">Lengkapi data di bawah untuk bergabung sebagai tenant CFD Surabaya</p>
                </div>

                <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <h6 class="text-uppercase small fw-bold text-muted mb-3 mt-2">
                        <i class="bi bi-person-vcard me-1"></i> Data Diri
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK</label>
                            <input type="text" name="nik_pedagang" maxlength="16"
                                   class="form-control @error('nik_pedagang') is-invalid @enderror"
                                   placeholder="16 digit NIK" value="{{ old('nik_pedagang') }}" required>
                            @error('nik_pedagang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama_pedagang"
                                   class="form-control @error('nama_pedagang') is-invalid @enderror"
                                   placeholder="Sesuai KTP" value="{{ old('nama_pedagang') }}" required>
                            @error('nama_pedagang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="no_telepon"
                                       class="form-control @error('no_telepon') is-invalid @enderror"
                                       placeholder="08xxxxxxxxxx" value="{{ old('no_telepon') }}" required>
                            </div>
                            @error('no_telepon')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="nama@email.com" value="{{ old('email') }}" required>
                            </div>
                            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" rows="2"
                                      class="form-control @error('alamat') is-invalid @enderror"
                                      placeholder="Alamat tempat tinggal" required>{{ old('alamat') }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Upload Foto KTP</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="ktpPreviewWrap" class="d-none flex-shrink-0">
                                    <img id="ktpPreview" src="" alt="Preview KTP" class="rounded-3 border" style="width:110px;height:70px;object-fit:cover;">
                                </div>
                                <input type="file" name="ktp_pedagang" id="ktpInput" accept="image/*"
                                       class="form-control @error('ktp_pedagang') is-invalid @enderror" required>
                            </div>
                            <div class="form-text">Foto/scan KTP yang jelas dan tidak buram, maksimal 2MB.</div>
                            @error('ktp_pedagang')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-uppercase small fw-bold text-muted mb-3">
                        <i class="bi bi-briefcase me-1"></i> Data Usaha
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Usaha</label>
                            <input type="text" name="nama_usaha"
                                   class="form-control @error('nama_usaha') is-invalid @enderror"
                                   placeholder="Contoh: Warung Kopi Bu Sari" value="{{ old('nama_usaha') }}" required>
                            @error('nama_usaha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-uppercase small fw-bold text-muted mb-3">
                        <i class="bi bi-shield-lock me-1"></i> Akun Login
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username"
                                   class="form-control @error('username') is-invalid @enderror"
                                   placeholder="Untuk login" value="{{ old('username') }}" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Minimal 6 karakter" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-3">
                        <i class="bi bi-person-plus me-1"></i> Daftar Sekarang
                    </button>
                </form>

                <hr class="my-4">

                <p class="text-center text-muted mb-0">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('ktpInput');
    const wrap = document.getElementById('ktpPreviewWrap');
    const preview = document.getElementById('ktpPreview');
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
