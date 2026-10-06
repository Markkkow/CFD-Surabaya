<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi CFD Surabaya')</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --cfd-green: #1a7a4c;
            --cfd-green-dark: #125c39;
            --cfd-green-light: #e7f5ee;
            --cfd-accent: #ff9f1c;
            --cfd-ink: #1e2a26;
        }

        * { font-family: 'Inter', 'Segoe UI', sans-serif; }
        h1, h2, h3, h4, h5, .navbar-brand, .fw-heading { font-family: 'Poppins', 'Segoe UI', sans-serif; }

        body {
            position: relative;
            background-color: #f6f8f7;
            color: var(--cfd-ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            left: -20%;
            right: -2%;
            bottom: -90px;
            height: clamp(150px, 22vw, 300px);
            background-image: url('{{ asset('images/runner-track.png') }}');
            background-repeat: no-repeat;
            background-position: center bottom;
            background-size: 100% auto;
            opacity: .12;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: "";
            position: fixed;
            left: 28%;
            bottom: clamp(35px, 5vw, 70px);
            width: clamp(150px, 20vw, 300px);
            aspect-ratio: 864 / 976;
            background-image: url('{{ asset('images/runner.png') }}');
            background-repeat: no-repeat;
            background-position: center bottom;
            background-size: contain;
            background-color: transparent;
            transform: translate3d(var(--runner-x, 0px), 0, 0);
            will-change: transform;
            opacity: .14;
            pointer-events: none;
            z-index: 0;
        }

        body > nav,
        body > main,
        body > footer {
            position: relative;
            z-index: 1;
        }

        main { flex: 1; }

        @media (prefers-reduced-motion: no-preference) {
            body::after {
                transition: transform .08s linear;
            }
        }

        /* Navbar + custom sandwich navigation */
        .cfd-navbar { min-height: 64px; position: relative; z-index: 10000; }
        .cfd-menu-btn {
            border: 1px solid rgba(255,255,255,.45);
            color: #fff;
            width: 44px;
            height: 40px;
            border-radius: .75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            padding: 0;
            position: relative;
            z-index: 10002;
            -webkit-tap-highlight-color: transparent;
        }
        .cfd-menu-btn:hover { background: rgba(255,255,255,.12); color: #fff; }
        .cfd-menu-btn:focus-visible {
            outline: 3px solid rgba(255,255,255,.35);
            outline-offset: 2px;
        }

        .cfd-menu-icon {
            display: inline-flex;
            width: 22px;
            height: 18px;
            position: relative;
            align-items: center;
            justify-content: center;
        }
        .cfd-menu-icon::before,
        .cfd-menu-icon::after,
        .cfd-menu-icon span {
            content: "";
            position: absolute;
            left: 0;
            width: 22px;
            height: 2px;
            border-radius: 2px;
            background: currentColor;
            transition: transform .22s ease, opacity .16s ease, top .22s ease;
            will-change: transform;
        }
        .cfd-menu-icon::before { top: 1px; }
        .cfd-menu-icon span { top: 8px; }
        .cfd-menu-icon::after { top: 15px; }

        .cfd-menu-btn.is-open .cfd-menu-icon::before {
            top: 8px;
            transform: rotate(45deg);
        }
        .cfd-menu-btn.is-open .cfd-menu-icon span {
            opacity: 0;
            transform: scaleX(.5);
        }
        .cfd-menu-btn.is-open .cfd-menu-icon::after {
            top: 8px;
            transform: rotate(-45deg);
        }

        .cfd-drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(12, 25, 20, .46);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 10010;
            transition: opacity .22s ease, visibility 0s linear .22s;
            -webkit-backdrop-filter: blur(1.5px);
            backdrop-filter: blur(1.5px);
        }
        .cfd-drawer-overlay.is-open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transition: opacity .22s ease;
        }

        .cfd-drawer {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: min(330px, 86vw);
            max-width: 100%;
            background: #fff;
            box-shadow: 12px 0 35px rgba(0,0,0,.16);
            transform: translate3d(-102%, 0, 0);
            visibility: hidden;
            pointer-events: none;
            z-index: 10020;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            will-change: transform;
            transition: transform .24s cubic-bezier(.22,.8,.24,1), visibility 0s linear .24s;
        }
        .cfd-drawer.is-open {
            transform: translate3d(0, 0, 0);
            visibility: visible;
            pointer-events: auto;
            transition: transform .24s cubic-bezier(.22,.8,.24,1);
        }

        .cfd-drawer-header {
            flex: 0 0 auto;
            min-height: 62px;
            padding: 12px 14px;
            background: linear-gradient(135deg, var(--cfd-green-dark), var(--cfd-green));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cfd-drawer-close {
            width: 38px;
            height: 38px;
            border: 0;
            background: transparent;
            color: #fff;
            border-radius: .65rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        .cfd-drawer-close:hover { background: rgba(255,255,255,.12); }
        .cfd-drawer-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            padding: 14px 10px;
        }
        .cfd-drawer .nav-link {
            color: var(--cfd-ink);
            border-radius: .8rem;
            padding: .72rem .78rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            min-height: 44px;
            text-decoration: none;
            transition: background-color .14s ease, color .14s ease;
        }
        .cfd-drawer .nav-link i { width: 1.35rem; text-align: center; flex: 0 0 1.35rem; }
        .cfd-drawer .nav-link:hover,
        .cfd-drawer .nav-link.active {
            color: var(--cfd-green-dark);
            background: var(--cfd-green-light);
        }
        .cfd-drawer .nav-section-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #87928d;
            margin: .5rem .78rem .35rem;
        }
        .cfd-drawer .menu-account {
            background: #f6f8f7;
            border: 1px solid #e9eeeb;
            border-radius: 1rem;
            padding: .85rem;
        }
        .menu-account-clickable { cursor: pointer; color: inherit; transition: transform .16s ease, border-color .16s ease, background-color .16s ease, box-shadow .16s ease; }
        .menu-account-clickable:hover { background: #eef8f3; border-color: #cfe9db; box-shadow: 0 5px 14px rgba(18, 92, 57, .08); transform: translateY(-1px); }
        .menu-account-clickable:focus-visible { outline: 3px solid rgba(26, 122, 76, .18); outline-offset: 2px; }
        .menu-account-avatar, .profile-avatar { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 40px; background: var(--cfd-green-light); color: var(--cfd-green-dark); }
        .profile-summary { display: flex; align-items: center; gap: .85rem; padding: .9rem; background: #f6f8f7; border: 1px solid #e9eeeb; border-radius: 1rem; }
        .profile-avatar { width: 52px; height: 52px; flex-basis: 52px; font-size: 1.25rem; }
        .profile-field { background: #f8faf9; border: 1px solid #edf1ef; border-radius: .8rem; padding: .7rem .8rem; min-height: 64px; }
        .profile-field span { display: block; color: #7a8580; font-size: .76rem; margin-bottom: .15rem; }
        .profile-field strong { display: block; font-size: .9rem; font-weight: 600; color: var(--cfd-ink); }
        body.cfd-menu-open { overflow: hidden; touch-action: none; }
        /* Profile modal: fokus setelah sandwich menu ditutup */
        #cfdProfileModal { z-index: 1060; }
        .modal-backdrop { z-index: 1055; }
        #cfdProfileModal .modal-content { border-radius: 1rem; overflow: hidden; }
        @media (max-width: 575.98px) {
            #cfdProfileModal .modal-dialog { margin: .75rem; }
            #cfdProfileModal .modal-content { border-radius: .9rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .cfd-drawer, .cfd-drawer-overlay,
            .cfd-menu-icon::before, .cfd-menu-icon::after, .cfd-menu-icon span {
                transition: none !important;
            }
        }
        .navbar {
            background: linear-gradient(90deg, var(--cfd-green-dark), var(--cfd-green) 60%, #d9a91a 100%);
            box-shadow: 0 4px 14px rgba(18, 92, 57, 0.25);
        }
        .navbar-brand {
            font-weight: 800;
            letter-spacing: .3px;
        }
        .navbar-brand .bi { color: var(--cfd-accent); }
        .nav-link {
            font-weight: 500;
            position: relative;
        }
        .nav-link.active, .nav-link:hover {
            color: #fff;
        }
        .btn-cfd-outline {
            border: 1px solid rgba(255,255,255,.6);
            color: #fff;
        }
        .btn-cfd-outline:hover { background: rgba(255,255,255,.12); color: #fff; }
        .btn-cfd-accent {
            background: var(--cfd-accent);
            border: none;
            color: #2a1a00;
            font-weight: 700;
        }
        .btn-cfd-accent:hover { background: #ffb347; color: #2a1a00; }

        .btn-primary, .btn-success {
            background-color: var(--cfd-green);
            border-color: var(--cfd-green);
        }
        .btn-primary:hover, .btn-success:hover {
            background-color: var(--cfd-green-dark);
            border-color: var(--cfd-green-dark);
        }
        .btn-outline-primary, .btn-outline-success {
            color: var(--cfd-green);
            border-color: var(--cfd-green);
        }
        .btn-outline-primary:hover, .btn-outline-success:hover {
            background-color: var(--cfd-green);
            border-color: var(--cfd-green);
        }
        a { color: var(--cfd-green-dark); }

        .badge.bg-primary { background-color: var(--cfd-green) !important; }

        /* Footer */
        footer.cfd-footer {
            background: var(--cfd-ink);
            color: rgba(255,255,255,.75);
            margin-top: auto;
        }
        footer.cfd-footer a { color: rgba(255,255,255,.9); text-decoration: none; }
        footer.cfd-footer a:hover { color: var(--cfd-accent); }

        .form-control:focus, .form-select:focus {
            border-color: var(--cfd-green);
            box-shadow: 0 0 0 .2rem rgba(26, 122, 76, .15);
        }

        .shadow-soft { box-shadow: 0 10px 30px rgba(30, 42, 38, 0.08); }
    </style>

    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-dark cfd-navbar">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <button
                    id="cfdMenuButton"
                    class="btn cfd-menu-btn"
                    type="button"
                    aria-label="Buka menu"
                    aria-controls="cfdMenu"
                    aria-expanded="false">
                    <span class="cfd-menu-icon" aria-hidden="true"><span></span></span>
                </button>

                <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="{{ route('home') }}">
                    <i class="bi bi-shop-window fs-4"></i> CFD Surabaya
                </a>
            </div>
        </div>
    </nav>

    <div id="cfdMenuOverlay" class="cfd-drawer-overlay" aria-hidden="true"></div>

    <aside id="cfdMenu"
           class="cfd-drawer"
           aria-label="Menu navigasi"
           aria-hidden="true">
        <div class="cfd-drawer-header">
            <div>
                <div class="fw-bold fs-5"><i class="bi bi-shop-window me-2"></i>CFD Surabaya</div>
                <div class="small text-white-50">Menu navigasi</div>
            </div>
            <button id="cfdMenuClose" type="button" class="cfd-drawer-close" aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="cfd-drawer-body">
            <div class="nav-section-label">Navigasi</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="bi bi-house-door"></i><span>Beranda</span>
                </a>
                @auth('pedagang')
                    <a class="nav-link {{ request()->routeIs('lapak.index') ? 'active' : '' }}" href="{{ route('lapak.index') }}">
                        <i class="bi bi-grid-3x3-gap"></i><span>Pilih Area Lapak</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('penjualan.laporan') ? 'active' : '' }}" href="{{ route('penjualan.laporan') }}">
                        <i class="bi bi-receipt-cutoff"></i><span>Laporan Penjualan</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('penjualan.analytics') ? 'active' : '' }}" href="{{ route('penjualan.analytics') }}">
                        <i class="bi bi-graph-up-arrow"></i><span>Analytics Saya</span>
                    </a>
                    @php
                        $unreadInboxCount = \App\Models\InboxPedagang::where(
                            'nik_pedagang',
                            Auth::guard('pedagang')->user()->nik_pedagang
                        )->whereNull('read_at')->count();
                    @endphp
                    <a class="nav-link {{ request()->routeIs('inbox.*') ? 'active' : '' }}" href="{{ route('inbox.index') }}">
                        <i class="bi bi-inbox"></i><span class="flex-grow-1">Inbox</span>
                        @if($unreadInboxCount > 0)
                            <span class="badge rounded-pill bg-danger">{{ $unreadInboxCount > 99 ? '99+' : $unreadInboxCount }}</span>
                        @endif
                    </a>
                @endauth

                @auth('admin')
                    <div class="nav-section-label mt-3">Admin</div>
                    <a class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}">
                        <i class="bi bi-speedometer2"></i><span>Panel Admin</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.verifikasi') ? 'active' : '' }}" href="{{ route('admin.verifikasi') }}">
                        <i class="bi bi-patch-check"></i><span>Verifikasi Pendaftaran</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                        <i class="bi bi-bar-chart-line"></i><span>Analytics Penjualan</span>
                    </a>
                @endauth
            </nav>

            <div class="mt-auto pt-4">
                @if(Auth::guard('pedagang')->check())
                    <button type="button" id="cfdPedagangProfileButton" class="menu-account menu-account-clickable mb-2 w-100 text-start" aria-label="Lihat biodata pedagang">
                        <div class="d-flex align-items-center gap-2">
                            <span class="menu-account-avatar"><i class="bi bi-person-fill"></i></span>
                            <span class="flex-grow-1 min-w-0">
                                <span class="small text-muted d-block">Profil Pedagang</span>
                                <span class="fw-semibold d-block text-truncate">{{ Auth::guard('pedagang')->user()->nama_pedagang }}</span>
                            </span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                        <span class="small text-success d-block mt-2"><i class="bi bi-person-vcard me-1"></i>Lihat biodata</span>
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                @elseif(Auth::guard('admin')->check())
                    <button type="button" class="menu-account menu-account-clickable mb-2 w-100 text-start" data-bs-toggle="modal" data-bs-target="#cfdProfileModal" aria-label="Lihat profil admin">
                        <div class="d-flex align-items-center gap-2">
                            <span class="menu-account-avatar"><i class="bi bi-person-fill"></i></span>
                            <span class="flex-grow-1 min-w-0">
                                <span class="small text-muted d-block">Profil Admin</span>
                                <span class="fw-semibold d-block text-truncate">{{ Auth::guard('admin')->user()->nama_admin }}</span>
                            </span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                        <span class="small text-success d-block mt-2"><i class="bi bi-person-vcard me-1"></i>Lihat profil</span>
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                @else
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-success">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-cfd-accent">Daftar Sekarang</a>
                    </div>
                @endif
            </div>
        </div>
    </aside>

    @if(Auth::guard('pedagang')->check())
        @php($pedagang = Auth::guard('pedagang')->user())
        <div class="modal fade" id="cfdProfileModal" tabindex="-1" aria-labelledby="cfdProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--cfd-green-dark), var(--cfd-green));">
                        <div><h5 class="modal-title mb-1" id="cfdProfileModalLabel"><i class="bi bi-person-vcard me-2"></i>Biodata Pedagang</h5><div class="small text-white-50">Informasi akun dan data usaha</div></div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-3 p-md-4">
                        <div class="profile-summary mb-3"><div class="profile-avatar"><i class="bi bi-person-fill"></i></div><div class="min-w-0"><div class="fw-bold fs-5 text-truncate">{{ $pedagang->nama_pedagang }}</div><div class="small text-muted">NIK: {{ $pedagang->nik_pedagang }}</div></div></div>
                        <div class="row g-2">
                            <div class="col-12"><div class="profile-field"><span>Nama Usaha</span><strong>{{ $pedagang->nama_usaha ?: '-' }}</strong></div></div>
                            <div class="col-12 col-sm-6"><div class="profile-field"><span>No. Telepon</span><strong>{{ $pedagang->no_telepon ?: '-' }}</strong></div></div>
                            <div class="col-12 col-sm-6"><div class="profile-field"><span>Email</span><strong class="text-break">{{ $pedagang->email ?: '-' }}</strong></div></div>
                            <div class="col-12 col-sm-6"><div class="profile-field"><span>Username</span><strong>{{ $pedagang->username ?: '-' }}</strong></div></div>
                            <div class="col-12 col-sm-6"><div class="profile-field"><span>Status Verifikasi</span><strong>{{ $pedagang->status_verifikasi ?: '-' }}</strong></div></div>
                            <div class="col-12"><div class="profile-field"><span>Sosial Media Usaha</span><strong class="text-break">{{ $pedagang->sosial_media_usaha ?: '-' }}</strong></div></div>
                            <div class="col-12"><div class="profile-field"><span>Alamat</span><strong class="text-break">{{ $pedagang->alamat ?: '-' }}</strong></div></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0"><button type="button" class="btn btn-success" data-bs-dismiss="modal">Tutup</button></div>
                </div>
            </div>
        </div>
    @elseif(Auth::guard('admin')->check())
        @php($adminUser = Auth::guard('admin')->user())
        <div class="modal fade" id="cfdProfileModal" tabindex="-1" aria-labelledby="cfdProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--cfd-green-dark), var(--cfd-green));"><div><h5 class="modal-title mb-1" id="cfdProfileModalLabel"><i class="bi bi-person-vcard me-2"></i>Profil Admin</h5><div class="small text-white-50">Informasi akun</div></div><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body p-4"><div class="profile-summary"><div class="profile-avatar"><i class="bi bi-person-fill"></i></div><div><div class="fw-bold fs-5">{{ $adminUser->nama_admin }}</div><div class="small text-muted">Akun Administrator</div></div></div></div>
            </div></div>
        </div>
    @endif

    <main>
        <div class="container mt-4 mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger shadow-sm border-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <strong>Terjadi kesalahan:</strong>
                    </div>
                    <ul class="mb-0 ps-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="cfd-footer py-4 mt-5">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center text-md-start">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shop-window fs-5"></i>
                <span class="fw-semibold text-white">CFD Surabaya</span>
                <span class="small">Pengelolaan Ekonomi Komunitas Car Free Day</span>
            </div>
            <div class="small d-flex align-items-center gap-3">
                <span>&copy; {{ date('Y') }} Dinas Terkait Kota Surabaya. Semua hak dilindungi.</span>
                <a href="{{ route('admin.index') }}" class="small"><i class="bi bi-gear me-1"></i>Panel Admin</a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        (() => {
            const button = document.getElementById('cfdMenuButton');
            const drawer = document.getElementById('cfdMenu');
            const overlay = document.getElementById('cfdMenuOverlay');
            const closeButton = document.getElementById('cfdMenuClose');

            if (!button || !drawer || !overlay || !closeButton) return;

            let lastFocused = null;
            let scrollY = 0;

            const openMenu = () => {
                if (drawer.classList.contains('is-open')) return;

                lastFocused = document.activeElement;
                scrollY = window.scrollY || 0;

                document.body.style.position = 'fixed';
                document.body.style.top = `-${scrollY}px`;
                document.body.style.left = '0';
                document.body.style.right = '0';
                document.body.classList.add('cfd-menu-open');

                drawer.classList.add('is-open');
                overlay.classList.add('is-open');
                button.classList.add('is-open');

                button.setAttribute('aria-expanded', 'true');
                button.setAttribute('aria-label', 'Tutup menu');
                drawer.setAttribute('aria-hidden', 'false');
                overlay.setAttribute('aria-hidden', 'false');

                requestAnimationFrame(() => closeButton.focus({ preventScroll: true }));
            };

            const closeMenu = (restoreFocus = true) => {
                if (!drawer.classList.contains('is-open')) return;

                drawer.classList.remove('is-open');
                overlay.classList.remove('is-open');
                button.classList.remove('is-open');

                button.setAttribute('aria-expanded', 'false');
                button.setAttribute('aria-label', 'Buka menu');
                drawer.setAttribute('aria-hidden', 'true');
                overlay.setAttribute('aria-hidden', 'true');

                const currentScroll = scrollY;

                document.body.classList.remove('cfd-menu-open');
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.left = '';
                document.body.style.right = '';

                window.scrollTo(0, currentScroll);

                if (restoreFocus && lastFocused && typeof lastFocused.focus === 'function') {
                    requestAnimationFrame(() => lastFocused.focus({ preventScroll: true }));
                }
            };

            button.addEventListener('click', () => {
                drawer.classList.contains('is-open') ? closeMenu() : openMenu();
            });

            closeButton.addEventListener('click', () => closeMenu());
            overlay.addEventListener('click', () => closeMenu());

            drawer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => closeMenu(false));
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && drawer.classList.contains('is-open')) {
                    event.preventDefault();
                    closeMenu();
                }
            });

            // Prevent clicks inside the drawer from bubbling to page-level handlers.
            drawer.addEventListener('click', event => event.stopPropagation());
        })();
    </script>

    <script>
        (() => {
            const profileButton = document.getElementById('cfdPedagangProfileButton');
            const profileModal = document.getElementById('cfdProfileModal');
            const drawer = document.getElementById('cfdMenu');
            const closeButton = document.getElementById('cfdMenuClose');

            if (!profileButton || !profileModal) return;

            profileButton.addEventListener('click', () => {
                const open = drawer && drawer.classList.contains('is-open');
                const showProfile = () => {
                    const modal = bootstrap.Modal.getOrCreateInstance(profileModal);
                    modal.show();
                };

                if (open && closeButton) {
                    closeButton.click();
                    window.setTimeout(showProfile, 260);
                } else {
                    showProfile();
                }
            });
        })();
    </script>

    <script>
        (() => {
            const root = document.documentElement;
            let ticking = false;

            const updateRunner = () => {
                const scrollY = window.scrollY || window.pageYOffset || 0;
                // Runner starts slightly left of center and moves smoothly to the right.
                const maxTravel = Math.max(window.innerWidth * 0.52, 180);
                const x = Math.min(scrollY * 0.35, maxTravel);
                root.style.setProperty('--runner-x', `${x}px`);
                ticking = false;
            };

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateRunner);
                    ticking = true;
                }
            }, { passive: true });

            window.addEventListener('resize', updateRunner, { passive: true });
            updateRunner();
        })();
    </script>
</body>
</html>
