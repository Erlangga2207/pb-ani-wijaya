@php
    $locales = config('locales');
    $currentLocale = $locales[app()->getLocale()];
    // Nama route dasar halaman ini (tanpa prefix "localized.") untuk canonical, hreflang & pemilih bahasa.
    $basePage = Str::after(request()->route()?->getName() ?? 'home', 'localized.');
    $basePage = in_array($basePage, ['home', 'about', 'products', 'gallery', 'contact'], true) ? $basePage : 'home';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale['hreflang'] }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Deskripsi per halaman lewat @section('description'); beranda memakai setting SEO. --}}
    <meta name="description" content="@yield('description', $setting('seo_meta_description', $setting('company_tagline', 'Premium Rice Producer').' - '.__(':company di Indramayu, Jawa Barat.', ['company' => $setting('company_name', 'PB. Ani Wijaya')])))">

    <title>@yield('title', 'PB. Ani Wijaya') - {{ $setting('company_name', 'PB. Ani Wijaya') }}</title>

    {{-- SEO multi-bahasa: canonical + versi bahasa lain + x-default (bahasa default) --}}
    <link rel="canonical" href="{{ localized_route($basePage) }}">
    @foreach ($locales as $code => $locale)
        <link rel="alternate" hreflang="{{ $locale['hreflang'] }}" href="{{ localized_route($basePage, [], $code) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ localized_route($basePage, [], array_key_first($locales)) }}">

    @include('partials.seo', ['basePage' => $basePage])

    {{-- Favicon (placeholder, ganti nanti) --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Tailwind Config --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#ba0013',
                            hover: '#93000d',
                            light: '#ffdad6',
                        },
                        gold: {
                            DEFAULT: '#fdd828',
                            dark: '#e8c404',
                        },
                        charcoal: '#1a1c1c',
                        surface: '#f9f9f9',
                    },
                    fontFamily: {
                        heading: ['EB Garamond', 'serif'],
                        body: ['Manrope', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.7s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                        slideUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- AOS (Animate On Scroll) --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- Material Symbols --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background-color: #f9f9f9;
            color: #1a1c1c;
        }
        .font-heading { font-family: 'EB Garamond', serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ba0013; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #93000d; }
    </style>

    {{-- EB Garamond & Manrope tidak punya huruf CJK: font Noto hanya dimuat untuk zh/ja/ko --}}
    @if ($currentLocale['fonts'])
        <link href="https://fonts.googleapis.com/css2?family={{ urlencode($currentLocale['fonts']['sans']) }}:wght@400;500;700&family={{ urlencode($currentLocale['fonts']['serif']) }}:wght@600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Manrope', '{{ $currentLocale['fonts']['sans'] }}', sans-serif; }
            .font-heading { font-family: 'EB Garamond', '{{ $currentLocale['fonts']['serif'] }}', serif !important; }
            .font-body { font-family: 'Manrope', '{{ $currentLocale['fonts']['sans'] }}', sans-serif !important; }
            h1, h2, h3, h4, p, li, span, a, label { overflow-wrap: anywhere; }
            @if (app()->getLocale() === 'ko')
                /* Korea: jangan memotong di tengah kata */
                p, li, h1, h2, h3, h4 { word-break: keep-all; }
            @endif
        </style>
    @endif

    @stack('styles')
</head>
<body class="antialiased">

    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    {{-- WhatsApp Floating Button --}}
    @include('components.whatsapp-float')

    {{-- AOS Init --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 80,
        });
    </script>

    @stack('scripts')
</body>
</html>
