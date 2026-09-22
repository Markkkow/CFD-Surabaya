@extends('layouts.app')

@section('title', 'Login Pedagang - CFD Surabaya')

@section('content')
<div class="row justify-content-center align-items-center py-4">
    <div class="col-11 col-sm-8 col-md-6 col-lg-5">
        <div class="card border-0 rounded-4 shadow-soft overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width:64px;height:64px;background:var(--cfd-green-light);">
                        <i class="bi bi-person-check fs-2" style="color:var(--cfd-green);"></i>
                    </div>
                    <h2 class="fw-heading fw-bold mb-1">Selamat Datang Kembali</h2>
                    <p class="text-muted mb-0">Login untuk Pedagang maupun Admin CFD Surabaya</p>
                </div>

                <form action="{{ route('login') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text"
                                   id="username"
                                   name="username"
                                   class="form-control border-start-0 @error('username') is-invalid @enderror"
                                   placeholder="Masukkan username Anda"
                                   value="{{ old('username') }}"
                                   required autofocus>
                        </div>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control border-start-0"
                                   placeholder="Masukkan password Anda"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </button>
                </form>

                <hr class="my-4">

                <p class="text-center text-muted mb-0">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar sebagai Pedagang/UMKM</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
