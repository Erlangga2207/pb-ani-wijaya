@php
    $user = auth()->user();
    $roleLabel = match (true) {
        $user->isSuperAdmin() => 'Super Admin',
        $user->isOwnerOrAbove() => 'Pemilik',
        default => 'Staf',
    };
    // Bottom nav HP: [route, label, ikon]. Item yang route-nya belum ada disembunyikan.
    $mobileNav = collect([
        ['admin.dashboard', 'Beranda', 'M3 11l9-8 9 8M5 9.5V21h14V9.5M10 21v-6h4v6'],
        ['admin.purchases.index', 'Pembelian', 'M4 7h16l-1.5 12h-13zM9 7V5a3 3 0 0 1 6 0v2'],
        ['admin.sales.index', 'Penjualan', 'M3 4h2l2 12h11l2-8H6M9 20h.01M17 20h.01'],
        ['admin.stock-movements.index', 'Stok', 'M4 7l8-4 8 4-8 4zM4 12l8 4 8-4M4 17l8 4 8-4'],
    ])->filter(fn ($nav) => Route::has($nav[0]));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('admin.layouts.aset')
    <title>@yield('judul', 'Panel') — PB. Ani Wijaya</title>
</head>
<body>
<div class="admin">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <img src="{{ asset('images/logo.png') }}" alt="">
            <span>PB. Ani Wijaya</span>
        </a>
        <nav class="admin-nav" aria-label="Menu utama">
            @include('admin.layouts.menu')
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand d-lg-none">
                <img src="{{ asset('images/logo.png') }}" alt="">
                <span>PB. Ani Wijaya</span>
            </a>
            <div class="d-none d-lg-flex align-items-center gap-3">
                <span class="text-ink-2 small">{{ $user->name }} · {{ $roleLabel }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-garis btn-sm">Keluar</button>
                </form>
            </div>
        </header>

        <main class="admin-konten">
            @if (session('sukses'))
                <div class="alert alert-success" role="status">{{ session('sukses') }}</div>
            @endif
            @if (session('gagal'))
                <div class="alert alert-danger" role="alert">{{ session('gagal') }}</div>
            @endif
            @if (session('peringatan'))
                <div class="alert alert-warning" role="alert">{{ session('peringatan') }}</div>
            @endif
            {{-- Ringkasan error validasi; berguna untuk pesan dari Service yang tidak terikat satu field (mis. void ditolak). --}}
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Belum tersimpan:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('konten')
        </main>
    </div>
</div>

<nav class="nav-hp d-lg-none" aria-label="Navigasi cepat">
    @foreach ($mobileNav as [$route, $label, $icon])
        @php $active = request()->routeIs(Str::endsWith($route, '.index') ? Str::beforeLast($route, '.').'.*' : $route); @endphp
        <a href="{{ route($route) }}" @class(['nav-hp-item', 'aktif' => $active]) @if ($active) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icon }}"/></svg>
            {{ $label }}
        </a>
    @endforeach
    <button type="button" class="nav-hp-item" data-bs-toggle="offcanvas" data-bs-target="#menuHp" aria-controls="menuHp">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        Menu
    </button>
</nav>

<div class="offcanvas offcanvas-bottom admin-offcanvas d-lg-none" tabindex="-1" id="menuHp" aria-labelledby="menuHpJudul">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title fs-6 fw-bold" id="menuHpJudul">Menu</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="admin-nav" aria-label="Menu lengkap">
            @include('admin.layouts.menu')
        </nav>
        <div class="admin-offcanvas-user">
            <span class="text-ink-2">{{ $user->name }} · {{ $roleLabel }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-garis">Keluar</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
