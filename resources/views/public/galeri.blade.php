@extends('layouts.public')

@section('title', 'Galeri - Dokumentasi Fasilitas & Produk')

@section('content')

{{-- Alpine.js component untuk filter + lightbox --}}
<div x-data="galeriPage()" x-cloak>

    {{-- ============================================ --}}
    {{-- HERO SECTION                                  --}}
    {{-- ============================================ --}}
    <section class="relative py-20 md:py-32 bg-gradient-to-br from-charcoal via-charcoal to-primary/30 overflow-hidden">
        {{-- Decorative Background Orbs --}}
        <div class="absolute top-0 right-0 w-72 md:w-96 h-72 md:h-96 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-72 md:w-96 h-72 md:h-96 bg-gold/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-5 md:px-12 text-center">
            {{-- Breadcrumb --}}
            <div class="flex items-center justify-center gap-2 text-white/60 text-sm mb-6 md:mb-8" data-aos="fade-down">
                <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span class="text-gold">Galeri</span>
            </div>

            {{-- Badge --}}
            <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur" data-aos="fade-up">
                Dokumentasi Operasi
            </span>

            {{-- Title --}}
            <h1 class="font-heading text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
                Mutu Terjamin, <br>
                <span class="text-primary italic">Butir Demi Butir.</span>
            </h1>

            {{-- Subtitle --}}
            <p class="text-base md:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                Jelajahi fasilitas penggilingan modern kami di Indramayu — dari sawah emas Jawa Barat hingga lini pengemasan presisi. Transparansi adalah pondasi PB. Ani Wijaya.
            </p>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FILTER BAR (Sticky)                           --}}
    {{-- ============================================ --}}
    <section class="sticky top-16 md:top-20 z-30 bg-white/95 backdrop-blur-md border-b border-surface shadow-sm">
        <div class="max-w-7xl mx-auto px-5 md:px-12 py-4">
            {{-- Scrollable on mobile --}}
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide -mx-2 px-2 pb-1">
                {{-- All --}}
                <button @click="setFilter('all')"
                    :class="filter === 'all' ? 'bg-primary text-white shadow-md' : 'bg-surface text-charcoal hover:bg-surface/70'"
                    class="flex-shrink-0 px-4 md:px-5 py-2 rounded-full text-xs md:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                    <span class="material-symbols-outlined text-base">apps</span>
                    Semua
                    <span class="bg-white/20 px-2 py-0.5 rounded-full text-[10px]" x-text="counts.all"></span>
                </button>

                {{-- Origin --}}
                <button @click="setFilter('origin')"
                    :class="filter === 'origin' ? 'bg-primary text-white shadow-md' : 'bg-surface text-charcoal hover:bg-surface/70'"
                    class="flex-shrink-0 px-4 md:px-5 py-2 rounded-full text-xs md:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                    <span class="material-symbols-outlined text-base">agriculture</span>
                    Asal Padi
                    <span class="bg-black/10 px-2 py-0.5 rounded-full text-[10px]" x-text="counts.origin"></span>
                </button>

                {{-- Processing --}}
                <button @click="setFilter('processing')"
                    :class="filter === 'processing' ? 'bg-primary text-white shadow-md' : 'bg-surface text-charcoal hover:bg-surface/70'"
                    class="flex-shrink-0 px-4 md:px-5 py-2 rounded-full text-xs md:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                    <span class="material-symbols-outlined text-base">settings_suggest</span>
                    Produksi
                    <span class="bg-black/10 px-2 py-0.5 rounded-full text-[10px]" x-text="counts.processing"></span>
                </button>

                {{-- Quality --}}
                <button @click="setFilter('quality')"
                    :class="filter === 'quality' ? 'bg-primary text-white shadow-md' : 'bg-surface text-charcoal hover:bg-surface/70'"
                    class="flex-shrink-0 px-4 md:px-5 py-2 rounded-full text-xs md:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                    <span class="material-symbols-outlined text-base">verified</span>
                    Kualitas
                    <span class="bg-black/10 px-2 py-0.5 rounded-full text-[10px]" x-text="counts.quality"></span>
                </button>

                {{-- Logistics --}}
                <button @click="setFilter('logistics')"
                    :class="filter === 'logistics' ? 'bg-primary text-white shadow-md' : 'bg-surface text-charcoal hover:bg-surface/70'"
                    class="flex-shrink-0 px-4 md:px-5 py-2 rounded-full text-xs md:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                    <span class="material-symbols-outlined text-base">local_shipping</span>
                    Logistik
                    <span class="bg-black/10 px-2 py-0.5 rounded-full text-[10px]" x-text="counts.logistics"></span>
                </button>

                {{-- Product --}}
                <button @click="setFilter('product')"
                    :class="filter === 'product' ? 'bg-primary text-white shadow-md' : 'bg-surface text-charcoal hover:bg-surface/70'"
                    class="flex-shrink-0 px-4 md:px-5 py-2 rounded-full text-xs md:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                    <span class="material-symbols-outlined text-base">rice_bowl</span>
                    Produk
                    <span class="bg-black/10 px-2 py-0.5 rounded-full text-[10px]" x-text="counts.product"></span>
                </button>

                {{-- Facility --}}
                <button @click="setFilter('facility')"
                    :class="filter === 'facility' ? 'bg-primary text-white shadow-md' : 'bg-surface text-charcoal hover:bg-surface/70'"
                    class="flex-shrink-0 px-4 md:px-5 py-2 rounded-full text-xs md:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                    <span class="material-symbols-outlined text-base">factory</span>
                    Fasilitas
                    <span class="bg-black/10 px-2 py-0.5 rounded-full text-[10px]" x-text="counts.facility"></span>
                </button>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- GALLERY GRID                                  --}}
    {{-- ============================================ --}}
    <section class="py-12 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-5 md:px-12">

            @if($galleries && $galleries->count() > 0)
                {{-- Bento Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-12 auto-rows-[180px] md:auto-rows-[220px] gap-3 md:gap-4">
                    @foreach($galleries as $index => $gallery)
                        @php
                            // Bento layout pattern - bervariasi tiap item
                            $layouts = [
                                'md:col-span-6 md:row-span-2 col-span-2',  // 0: big featured
                                'md:col-span-3',                            // 1: small
                                'md:col-span-3 md:row-span-2',              // 2: tall
                                'md:col-span-4',                            // 3: medium
                                'md:col-span-5',                            // 4: wide
                                'md:col-span-3',                            // 5: small
                                'md:col-span-5 md:row-span-2',              // 6: tall wide
                                'md:col-span-4',                            // 7: medium
                                'md:col-span-3',                            // 8: small
                                'md:col-span-6',                            // 9: wide
                                'md:col-span-3',                            // 10: small
                                'md:col-span-3',                            // 11: small
                            ];
                            $layoutClass = $layouts[$index % count($layouts)];

                            // Detect if video
                            $extension = pathinfo($gallery->file_path, PATHINFO_EXTENSION);
                            $isVideo = strtolower($extension) === 'mp4';

                            // Category label mapping
                            $categoryLabels = [
                                'origin' => ['label' => 'Asal Padi', 'icon' => 'agriculture'],
                                'processing' => ['label' => 'Produksi', 'icon' => 'settings_suggest'],
                                'quality' => ['label' => 'Kualitas', 'icon' => 'verified'],
                                'logistics' => ['label' => 'Logistik', 'icon' => 'local_shipping'],
                                'product' => ['label' => 'Produk', 'icon' => 'rice_bowl'],
                                'facility' => ['label' => 'Fasilitas', 'icon' => 'factory'],
                            ];
                            $catInfo = $categoryLabels[$gallery->category] ?? ['label' => 'Galeri', 'icon' => 'image'];

                            $fileUrl = asset('storage/' . $gallery->file_path);
                        @endphp

                        <div x-show="filter === 'all' || filter === '{{ $gallery->category }}'"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="{{ $layoutClass }} group relative overflow-hidden rounded-xl md:rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 cursor-pointer bg-surface"
                             @click="openLightbox({
                                 url: '{{ $fileUrl }}',
                                 title: '{{ addslashes($gallery->title ?? $catInfo['label']) }}',
                                 caption: '{{ addslashes($gallery->caption ?? '') }}',
                                 category: '{{ $catInfo['label'] }}',
                                 isVideo: {{ $isVideo ? 'true' : 'false' }}
                             })"
                             data-aos="fade-up"
                             data-aos-delay="{{ ($index % 6) * 50 }}">

                            @if($isVideo)
                                {{-- Video thumbnail with play button overlay --}}
                                <video class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                       muted
                                       playsinline
                                       preload="metadata">
                                    <source src="{{ $fileUrl }}" type="video/mp4">
                                </video>

                                {{-- Play button overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-all">
                                    <div class="w-14 h-14 md:w-16 md:h-16 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-primary text-3xl md:text-4xl ml-1" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                    </div>
                                </div>

                                {{-- Video badge --}}
                                <div class="absolute top-3 right-3 bg-black/60 backdrop-blur text-white text-[10px] px-2 py-1 rounded-full font-bold uppercase tracking-widest flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">videocam</span>
                                    Video
                                </div>
                            @else
                                {{-- Image --}}
                                <img src="{{ $fileUrl }}"
                                     alt="{{ $gallery->title ?? $catInfo['label'] }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                     loading="lazy">
                            @endif

                            {{-- Hover Overlay with Info --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4 md:p-6">
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="material-symbols-outlined text-gold text-base">{{ $catInfo['icon'] }}</span>
                                    <span class="text-gold font-bold text-[10px] md:text-xs uppercase tracking-widest">{{ $catInfo['label'] }}</span>
                                </div>
                                @if($gallery->title)
                                    <h3 class="text-white font-heading text-base md:text-xl font-bold leading-tight line-clamp-2">{{ $gallery->title }}</h3>
                                @endif

                                {{-- Zoom icon (image) or play icon (video) --}}
                                <div class="absolute top-3 left-3 w-9 h-9 bg-white/90 backdrop-blur rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-primary text-base">
                                        {{ $isVideo ? 'play_circle' : 'zoom_in' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty state per filter (Alpine shows when no items match) --}}
                <div x-show="isEmpty()" x-cloak class="text-center py-20">
                    <div class="w-20 h-20 bg-surface rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-gray-400 text-4xl">image_search</span>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-charcoal mb-2">Belum ada konten di kategori ini</h3>
                    <p class="text-sm text-gray-600 mb-6">Konten untuk kategori ini akan segera ditambahkan.</p>
                    <button @click="setFilter('all')"
                            class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-full font-bold text-sm hover:bg-primary/90 transition-all active:scale-95">
                        <span class="material-symbols-outlined text-base">apps</span>
                        Lihat Semua
                    </button>
                </div>

            @else
                {{-- Full Empty State (no galleries at all) --}}
                <div class="text-center py-20 md:py-32">
                    <div class="w-24 h-24 bg-surface rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-gray-400 text-5xl">photo_library</span>
                    </div>
                    <h3 class="font-heading text-2xl md:text-3xl font-bold text-charcoal mb-3">Galeri Sedang Dipersiapkan</h3>
                    <p class="text-base text-gray-600 max-w-md mx-auto mb-8">
                        Tim kami sedang mengkurasi foto dan video terbaik dari fasilitas produksi. Silakan kembali sebentar lagi!
                    </p>
                    <a href="{{ route('products') }}"
                       class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary/90 transition-all active:scale-95">
                        <span class="material-symbols-outlined">arrow_forward</span>
                        Jelajahi Produk Kami
                    </a>
                </div>
            @endif

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CERTIFICATION TRUST BANNER                    --}}
    {{-- ============================================ --}}
    <section class="py-16 md:py-24 bg-surface">
        <div class="max-w-5xl mx-auto px-5 md:px-12">
            <div class="bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-surface text-center">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <div class="h-px w-12 bg-primary"></div>
                    <span class="text-primary font-bold text-xs uppercase tracking-widest">Terverifikasi</span>
                    <div class="h-px w-12 bg-primary"></div>
                </div>

                <h2 class="font-heading text-2xl md:text-4xl font-bold text-charcoal mb-8" data-aos="fade-up">
                    NIB Terdaftar &amp; <span class="text-primary italic">Kualitas Tersertifikasi</span>
                </h2>

                <div class="flex flex-wrap justify-center gap-8 md:gap-16">
                    {{-- NIB --}}
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="100">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gold/20 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-gold text-3xl md:text-4xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <span class="text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">NIB</span>
                        <span class="text-xs md:text-sm font-bold text-charcoal">{{ $setting('company_nib', '1412210030165') }}</span>
                    </div>

                    {{-- Natural Process --}}
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gold/20 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-gold text-3xl md:text-4xl" style="font-variation-settings: 'FILL' 1;">eco</span>
                        </div>
                        <span class="text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Proses</span>
                        <span class="text-xs md:text-sm font-bold text-charcoal">100% Natural</span>
                    </div>

                    {{-- Mutu Terjamin --}}
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gold/20 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-gold text-3xl md:text-4xl" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                        </div>
                        <span class="text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Mutu</span>
                        <span class="text-xs md:text-sm font-bold text-charcoal">Terjamin</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- LIGHTBOX MODAL                                --}}
    {{-- ============================================ --}}
    <div x-show="lightboxOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeLightbox()"
         @keydown.escape.window="closeLightbox()"
         class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-sm flex items-center justify-center p-4 md:p-8"
         x-cloak>

        {{-- Close Button --}}
        <button @click="closeLightbox()"
                class="absolute top-4 right-4 md:top-6 md:right-6 w-12 h-12 bg-white/10 backdrop-blur hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-all active:scale-95 z-20">
            <span class="material-symbols-outlined text-2xl">close</span>
        </button>

        {{-- Content Container --}}
        <div @click.stop class="relative max-w-6xl max-h-[90vh] w-full flex flex-col items-center justify-center">

            {{-- Image OR Video --}}
            <template x-if="!lightboxData.isVideo">
                <img :src="lightboxData.url"
                     :alt="lightboxData.title"
                     class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-2xl">
            </template>

            <template x-if="lightboxData.isVideo">
                <video :src="lightboxData.url"
                       controls
                       autoplay
                       class="max-w-full max-h-[75vh] rounded-lg shadow-2xl">
                </video>
            </template>

            {{-- Info Bar --}}
            <div class="mt-4 md:mt-6 bg-white/10 backdrop-blur rounded-xl px-5 py-3 md:px-6 md:py-4 max-w-2xl w-full text-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <span class="text-gold font-bold text-[10px] md:text-xs uppercase tracking-widest" x-text="lightboxData.category"></span>
                </div>
                <h3 class="text-white font-heading text-lg md:text-2xl font-bold leading-tight" x-text="lightboxData.title"></h3>
                <p x-show="lightboxData.caption" class="text-white/70 text-xs md:text-sm mt-2" x-text="lightboxData.caption"></p>
            </div>
        </div>
    </div>

</div>

{{-- ============================================ --}}
{{-- ALPINE.JS COMPONENT                           --}}
{{-- ============================================ --}}
<script>
    function galeriPage() {
        return {
            filter: 'all',
            lightboxOpen: false,
            lightboxData: {
                url: '',
                title: '',
                caption: '',
                category: '',
                isVideo: false
            },
            counts: {
                all: {{ $galleries->count() ?? 0 }},
                origin: {{ $galleries->where('category', 'origin')->count() ?? 0 }},
                processing: {{ $galleries->where('category', 'processing')->count() ?? 0 }},
                quality: {{ $galleries->where('category', 'quality')->count() ?? 0 }},
                logistics: {{ $galleries->where('category', 'logistics')->count() ?? 0 }},
                product: {{ $galleries->where('category', 'product')->count() ?? 0 }},
                facility: {{ $galleries->where('category', 'facility')->count() ?? 0 }},
            },

            setFilter(category) {
                this.filter = category;
                // Smooth scroll to gallery section
                window.scrollTo({
                    top: document.querySelector('section.py-12.md\\:py-20')?.offsetTop - 100,
                    behavior: 'smooth'
                });
            },

            isEmpty() {
                if (this.filter === 'all') return this.counts.all === 0;
                return this.counts[this.filter] === 0;
            },

            openLightbox(data) {
                this.lightboxData = data;
                this.lightboxOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeLightbox() {
                this.lightboxOpen = false;
                this.lightboxData = { url: '', title: '', caption: '', category: '', isVideo: false };
                document.body.style.overflow = '';
            }
        }
    }
</script>

{{-- Custom utility: hide scrollbar --}}
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    [x-cloak] { display: none !important; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@endsection