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

        /* Navbar */
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
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="bi bi-shop-window fs-4"></i> CFD Surabaya
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i>Beranda
                        </a>
                    </li>
                    @auth('pedagang')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lapak.index') ? 'active' : '' }}" href="{{ route('lapak.index') }}">
                                <i class="bi bi-grid-3x3-gap me-1"></i>Pilih Area Lapak
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('penjualan.laporan') ? 'active' : '' }}" href="{{ route('penjualan.laporan') }}">
                                <i class="bi bi-receipt-cutoff me-1"></i>Laporan Penjualan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('penjualan.analytics') ? 'active' : '' }}" href="{{ route('penjualan.analytics') }}">
                                <i class="bi bi-graph-up-arrow me-1"></i>Analytics Saya
                            </a>
                        </li>
                    @endauth
                    @auth('admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}">
                                <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.verifikasi') ? 'active' : '' }}" href="{{ route('admin.verifikasi') }}">
                                <i class="bi bi-patch-check me-1"></i>Verifikasi Pendaftaran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                                <i class="bi bi-bar-chart-line me-1"></i>Analytics Penjualan
                            </a>
                        </li>
                    @endauth
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @if(Auth::guard('pedagang')->check())
                        <span class="text-white-50 me-1 d-none d-lg-inline">
                            <i class="bi bi-person-circle me-1"></i>Halo, <strong class="text-white">{{ Auth::guard('pedagang')->user()->nama_pedagang }}</strong>
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </button>
                        </form>
                    @elseif(Auth::guard('admin')->check())
                        <span class="text-white-50 me-1 d-none d-lg-inline">
                            <i class="bi bi-shield-lock me-1"></i>Admin: <strong class="text-white">{{ Auth::guard('admin')->user()->nama_admin }}</strong>
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-cfd-outline">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-cfd-accent">Daftar Sekarang</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

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
