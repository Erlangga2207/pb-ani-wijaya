@extends('layouts.public')

@section('title', __('Beranda'))

@section('content')

{{-- ========================================== --}}
{{-- HERO SECTION                                --}}
{{-- ========================================== --}}
<section class="relative min-h-[90vh] flex items-center justify-center bg-gradient-to-br from-surface via-white to-primary-light/20 overflow-hidden">

    {{-- Decorative bg circles --}}
    <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-gold/10 rounded-full blur-3xl"></div>

    <div class="relative z-10 text-center px-5 max-w-4xl mx-auto py-20">

        <div class="flex justify-center mb-8" data-aos="fade-down">
            <img src="{{ asset('images/logo.png') }}"
                 alt="{{ __('PB. Ani Wijaya Logo') }}"
                 class="w-32 h-32 md:w-40 md:h-40 object-contain drop-shadow-2xl">
        </div>

        <div data-aos="fade-up" data-aos-delay="100">
            <span class="inline-block bg-gold/20 text-charcoal px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6">
                {{ $setting('hero_badge', "INDRAMAYU'S FINEST") }}
            </span>
        </div>

        <h1 class="font-heading text-4xl md:text-6xl lg:text-7xl font-bold text-charcoal mb-6 leading-tight" data-aos="fade-up" data-aos-delay="200">
            {{ $setting('hero_title', 'Heritage of Excellence') }}
            <br>
            <span class="text-primary italic">{{ $setting('hero_subtitle', 'Mutu Terjamin') }}</span>
        </h1>

        <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="300">
            {{ $setting('hero_description', 'Premium rice producer from Indramayu, West Java.') }}
        </p>

        <div class="flex flex-wrap gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
            <a href="{{ localized_route('products') }}"
               class="bg-primary text-white px-8 py-4 rounded-lg font-bold hover:bg-primary-hover transition-all shadow-lg hover:shadow-xl active:scale-95 inline-flex items-center gap-2">
                <span>{{ __('Lihat Produk') }}</span>
                <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
            </a>
            <a href="{{ localized_route('about') }}"
               class="border-2 border-charcoal text-charcoal px-8 py-4 rounded-lg font-bold hover:bg-charcoal hover:text-white transition-all active:scale-95">
                {{ __('Cerita Kami') }}
            </a>
        </div>

        <div class="mt-16 grid grid-cols-3 gap-4 md:gap-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="500">
            <div class="text-center">
                <div class="font-heading text-3xl md:text-5xl font-bold text-primary">{{ $setting('years_experience', '25') }}+</div>
                <div class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wider mt-1">{{ __('Tahun Pengalaman') }}</div>
            </div>
            <div class="text-center border-x border-gray-200">
                <div class="font-heading text-3xl md:text-5xl font-bold text-primary">{{ $setting('partners_count', '500') }}+</div>
                <div class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wider mt-1">{{ __('Mitra Distribusi') }}</div>
            </div>
            <div class="text-center">
                <div class="font-heading text-3xl md:text-5xl font-bold text-primary">100%</div>
                <div class="text-[10px] md:text-xs text-gray-500 uppercase tracking-wider mt-1">{{ __('Local Sourced') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================== --}}
{{-- WHY CHOOSE US SECTION                       --}}
{{-- ========================================== --}}
<section class="py-20 md:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-5 md:px-12">

        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-primary font-bold text-xs uppercase tracking-widest">{{ __('Why Choose Us') }}</span>
            <h2 class="font-heading text-3xl md:text-5xl font-bold text-charcoal mt-3 mb-4">
                {{ __('Pilar Kualitas') }} <span class="text-primary italic">{{ __('PB. Ani Wijaya') }}</span>
            </h2>
            <p class="text-gray-600">
                {{ __('Tiga prinsip yang menjadikan kami pilihan utama distributor dan pelanggan setia di Jawa Barat dan sekitarnya.') }}
            </p>
        </div>

        {{-- 3 Pillars Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Pilar 1: Quality --}}
            <div class="group p-8 bg-surface rounded-2xl border border-gray-200 hover:border-primary hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition-all">
                    <span class="material-symbols-outlined text-primary group-hover:text-white text-3xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                </div>
                <h3 class="font-heading text-2xl font-bold mb-3">{{ __('Kualitas Utama') }}</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('Setiap butir beras melalui pemeriksaan ketat di laboratorium internal. Hanya yang memenuhi standar "Kepala" yang masuk ke kemasan kami.') }}
                </p>
                <div class="mt-6 inline-flex items-center gap-2 text-gold text-sm font-bold">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                    <span>{{ __('Mutu Terjamin') }}</span>
                </div>
            </div>

            {{-- Pilar 2: Tradition (HIGHLIGHTED) --}}
            <div class="group p-8 bg-primary text-white rounded-2xl shadow-2xl md:-translate-y-4" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings: 'FILL' 1;">history_edu</span>
                </div>
                <h3 class="font-heading text-2xl font-bold mb-3">{{ __('Tradisi Indramayu') }}</h3>
                <p class="text-white/90 leading-relaxed">
                    {{ __('Lahir dari tanah subur Indramayu, kami memadukan kearifan petani lokal dengan teknologi pengolahan modern untuk hasil terbaik.') }}
                </p>
                <div class="mt-6 inline-flex items-center gap-2 text-gold text-sm font-bold">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">eco</span>
                    <span>{{ __('Heritage Quality') }}</span>
                </div>
            </div>

            {{-- Pilar 3: Scale --}}
            <div class="group p-8 bg-surface rounded-2xl border border-gray-200 hover:border-primary hover:shadow-xl transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition-all">
                    <span class="material-symbols-outlined text-primary group-hover:text-white text-3xl" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
                </div>
                <h3 class="font-heading text-2xl font-bold mb-3">{{ __('Skala Reliable') }}</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('Dari kebutuhan retail hingga distribusi grosir massal, kapasitas pabrik kami memastikan supply chain selalu stabil tanpa kompromi.') }}
                </p>
                <div class="mt-6 inline-flex items-center gap-2 text-gold text-sm font-bold">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                    <span>{{ __('Stock Tersedia') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================== --}}
{{-- FEATURED PRODUCT SECTION                    --}}
{{-- ========================================== --}}
@if($featuredProduct)
<section class="py-20 md:py-32 bg-gradient-to-br from-surface to-white">
    <div class="max-w-7xl mx-auto px-5 md:px-12">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Left: Product Image with Decoration --}}
            <div class="relative" data-aos="fade-right">
                <div class="aspect-square bg-white rounded-3xl shadow-2xl p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gold/20 rounded-full blur-3xl"></div>
                    <img src="{{ asset('images/foto_beras.jpeg') }}"
                         alt="{{ $featuredProduct->name }}"
                         loading="lazy"
                         class="absolute inset-0 w-full h-full object-cover z-10">
                    <div class="absolute inset-0 bg-gradient-to-t from-charcoal/30 via-transparent to-charcoal/10 z-10"></div>

                    {{-- Premium Badge --}}
                    <div class="absolute top-6 left-6 bg-gold text-charcoal px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center gap-2 z-20">
                        <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                        {{ __('Premium Grade') }}
                    </div>
                </div>

                {{-- Floating Stats Card --}}
                <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-xl border border-gray-100 hidden md:block">
                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('Per Karung') }}</div>
                    <div class="font-heading text-3xl font-bold text-primary">{{ number_format($featuredProduct->weight_per_karung, 0) }} kg</div>
                </div>
            </div>

            {{-- Right: Product Info --}}
            <div data-aos="fade-left">
                <span class="inline-block bg-primary/10 text-primary px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4">
                    {{ __('Produk Unggulan') }}
                </span>

                <h2 class="font-heading text-4xl md:text-5xl font-bold text-charcoal mb-2">
                    {{ $featuredProduct->name }}
                </h2>
                <p class="text-primary italic font-heading text-xl mb-6">{{ __('— The Master\'s Choice') }}</p>

                <p class="text-gray-600 leading-relaxed mb-8">
                    {{ $featuredProduct->description ?? 'Beras Ketan Putih premium kelas KEPALA dari ladang terbaik Indramayu. Diolah dengan teknologi modern, menghasilkan butiran bersih, putih, dan tekstur sempurna untuk masakan tradisional.' }}
                </p>

                {{-- Product Specs --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="p-4 bg-white rounded-lg border border-gray-200">
                        <div class="text-xs text-primary font-bold uppercase tracking-wider mb-1">{{ __('Grade') }}</div>
                        <div class="font-bold text-charcoal">{{ __('Kepala (Premium)') }}</div>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-gray-200">
                        <div class="text-xs text-primary font-bold uppercase tracking-wider mb-1">{{ __('Region') }}</div>
                        <div class="font-bold text-charcoal">{{ __('Indramayu Barat') }}</div>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-gray-200">
                        <div class="text-xs text-primary font-bold uppercase tracking-wider mb-1">{{ __('Texture') }}</div>
                        <div class="font-bold text-charcoal">{{ __('Ultra-Glutinous') }}</div>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-gray-200">
                        <div class="text-xs text-primary font-bold uppercase tracking-wider mb-1">{{ __('Purity') }}</div>
                        <div class="font-bold text-charcoal">{{ __('99.9% Clean') }}</div>
                    </div>
                </div>

                <a href="{{ localized_route('products') }}"
                   class="inline-flex items-center gap-2 text-primary font-bold hover:gap-4 transition-all">
                    <span>{{ __('Lihat Detail Produk') }}</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ========================================== --}}
{{-- GALLERY PREVIEW SECTION                     --}}
{{-- ========================================== --}}
@if($featuredGalleries->count() > 0)
<section class="py-20 md:py-32 bg-charcoal text-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-5 md:px-12">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6" data-aos="fade-up">
            <div>
                <span class="text-gold font-bold text-xs uppercase tracking-widest">{{ __('Behind The Scenes') }}</span>
                <h2 class="font-heading text-3xl md:text-5xl font-bold mt-3 mb-4">
                    {{ __('Dari') }} <span class="text-primary italic">{{ __('Sawah Hingga Karung') }}</span>
                </h2>
                <p class="text-gray-400 max-w-xl">
                    {{ __('Transparansi adalah pondasi kepercayaan kami. Lihat proses operasi kami secara langsung.') }}
                </p>
            </div>
            <a href="{{ localized_route('gallery') }}"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-primary text-white px-6 py-3 rounded-lg font-bold transition-all backdrop-blur whitespace-nowrap">
                <span>{{ __('Galeri Lengkap') }}</span>
                <span class="material-symbols-outlined">arrow_outward</span>
            </a>
        </div>

        {{-- Gallery Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 md:auto-rows-[200px]">
            @foreach($featuredGalleries as $index => $gallery)
                <div class="group relative overflow-hidden rounded-2xl
                           {{ $index === 0 ? 'md:col-span-2 md:row-span-2 aspect-square md:aspect-auto' : 'aspect-square md:aspect-auto' }}"
                     data-aos="fade-up"
                     data-aos-delay="{{ $index * 100 }}">
        
                    @if($gallery->file_type === 'image')
                        <img src="{{ asset('storage/' . $gallery->file_path) }}"
                             alt="{{ $gallery->title }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-primary to-charcoal flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-6xl">play_circle</span>
                        </div>
                    @endif
                    
                    {{-- Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4 md:p-6">
                        <span class="text-gold text-[10px] md:text-xs font-bold uppercase tracking-widest mb-2">
                            {{ $gallery->category_label }}
                        </span>
                        <h3 class="font-heading text-lg md:text-xl font-bold text-white">
                            {{ $gallery->title }}
                        </h3>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ========================================== --}}
{{-- CTA SECTION                                 --}}
{{-- ========================================== --}}
<section class="py-20 md:py-32 bg-white">
    <div class="max-w-5xl mx-auto px-5 md:px-12">
        <div class="bg-gradient-to-br from-primary to-primary-hover rounded-3xl p-10 md:p-16 text-center text-white relative overflow-hidden" data-aos="zoom-in">

            {{-- Decorative circles --}}
            <div class="absolute -top-10 -right-10 w-64 h-64 border-2 border-white/10 rounded-full"></div>
            <div class="absolute -bottom-20 -left-20 w-96 h-96 border-2 border-white/10 rounded-full"></div>

            <div class="relative z-10">
                <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur">
                    {{ __('Ready to Order?') }}
                </span>

                <h2 class="font-heading text-3xl md:text-5xl font-bold mb-6">
                    {{ __('Mulai Kerjasama dengan Kami') }}
                </h2>

                <p class="text-white/90 max-w-2xl mx-auto mb-10 leading-relaxed">
                    {{ __('Distributor, retailer, atau UKM kuliner — kami siap menjadi partner supply beras berkualitas untuk usaha Anda. Hubungi kami untuk diskusi harga grosir dan kerjasama jangka panjang.') }}
                </p>

                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ localized_route('contact') }}"
                       class="bg-white text-primary px-8 py-4 rounded-lg font-bold hover:bg-gold hover:text-charcoal transition-all shadow-xl active:scale-95 inline-flex items-center gap-2">
                        <span class="material-symbols-outlined">mail</span>
                        <span>{{ __('Kirim Inquiry') }}</span>
                    </a>
                    <a href="https://wa.me/{{ $setting('social_whatsapp', '6281324314300') }}"
                       target="_blank"
                       class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold hover:bg-white hover:text-primary transition-all active:scale-95 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>{{ __('WhatsApp') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
