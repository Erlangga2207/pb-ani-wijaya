@extends('layouts.public')

@section('title', __('Beras Ketan Putih Premium - Supplier Grosir'))
@section('description', __('Beras ketan putih premium kelas kepala dari pabrik kami di Subang. Supplier beras ketan grosir untuk distributor, restoran, dan industri makanan di Jawa Barat dan seluruh Indonesia.'))

@section('content')

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
            <a href="{{ localized_route('home') }}" class="hover:text-gold transition-colors">{{ __('Beranda') }}</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-gold">{{ __('Produk') }}</span>
        </div>

        {{-- Badge --}}
        <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur" data-aos="fade-up">
            {{ __('Produk Unggulan') }}
        </span>

        {{-- Title --}}
        <h1 class="font-heading text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
            {{ __('Beras Ketan Putih') }} <br>
            <span class="text-primary italic">{{ __('Premium KEPALA') }}</span>
        </h1>

        {{-- Subtitle --}}
        <p class="text-base md:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="200">
            {{ __('Puncak panen Subang yang diproses dengan presisi tinggi — menghasilkan butiran putih mutiara dengan kelengketan sempurna untuk kuliner premium Indonesia.') }}
        </p>

        {{-- Quick Trust Indicators --}}
        <div class="flex flex-wrap justify-center gap-4 md:gap-8 mt-10" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center gap-2 text-white/90">
                <span class="material-symbols-outlined text-gold">verified</span>
                <span class="text-sm font-bold">{{ __('99,9% Murni') }}</span>
            </div>
            <div class="flex items-center gap-2 text-white/90">
                <span class="material-symbols-outlined text-gold">workspace_premium</span>
                <span class="text-sm font-bold">{{ __('Mutu Terjamin') }}</span>
            </div>
            <div class="flex items-center gap-2 text-white/90">
                <span class="material-symbols-outlined text-gold">eco</span>
                <span class="text-sm font-bold">{{ __('100% Lokal Subang') }}</span>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- FEATURED PRODUCT DETAIL                       --}}
{{-- ============================================ --}}
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-5 md:px-12">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">

            {{-- LEFT: Product Image --}}
            <div class="relative" data-aos="fade-right">
                {{-- Main image container --}}
                <div class="relative aspect-square bg-gradient-to-br from-surface to-white rounded-3xl overflow-hidden shadow-xl border border-surface">
                    {{-- Decorative grain pattern --}}
                    <div class="absolute inset-0 opacity-5">
                        <span class="material-symbols-outlined text-[300px] text-primary absolute -top-10 -right-10 rotate-12">grain</span>
                    </div>

                    {{-- Product Photo --}}
                    <img src="{{ asset('images/foto_beras.jpeg') }}"
                         alt="{{ __('Beras Ketan Putih PB. Ani Wijaya') }}"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-charcoal/30 via-transparent to-charcoal/10"></div>

                    {{-- Floating Premium Badge --}}
                    <div class="absolute top-5 left-5 md:top-8 md:left-8 bg-primary text-white px-4 py-2 md:px-5 md:py-2.5 rounded-full font-bold text-xs md:text-sm shadow-lg flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">star</span>
                        {{ __('PREMIUM GRADE') }}
                    </div>

                    {{-- Floating NIB Stamp --}}
                    <div class="absolute bottom-5 right-5 md:bottom-8 md:right-8 bg-white border-2 border-gold rounded-2xl px-4 py-3 shadow-lg max-w-[180px]">
                        <p class="text-[10px] font-bold text-gold uppercase tracking-widest mb-1">{{ __('NIB Terdaftar') }}</p>
                        <p class="text-xs md:text-sm font-bold text-charcoal">{{ $setting('company_nib', '1412210030165') }}</p>
                    </div>
                </div>

                {{-- Decorative accent --}}
                <div class="hidden lg:block absolute -z-10 top-8 -left-8 w-full h-full bg-primary/10 rounded-3xl"></div>
            </div>

            {{-- RIGHT: Product Specs --}}
            <div data-aos="fade-left">
                {{-- Tag --}}
                <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4">
                    {{ __('Spesifikasi Produk') }}
                </span>

                {{-- Title --}}
                <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold text-charcoal mb-6 leading-tight">
                    {{ __('Standar Mutu yang') }} <span class="text-primary italic">{{ __('Tidak Kompromi') }}</span>
                </h2>

                {{-- Description --}}
                <p class="text-base md:text-lg text-gray-600 mb-8 leading-relaxed">
                    {{ __('Setiap butir beras ketan kami melewati seleksi ketat dari sawah hingga karung. Diproses dengan teknologi modern untuk menjaga kelengketan alami dan aroma khas Subang.') }}
                </p>

                {{-- Specs Grid 2x2 --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    {{-- Grade --}}
                    <div class="p-5 bg-surface rounded-xl border border-surface hover:border-primary/30 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary text-xl">workspace_premium</span>
                            <span class="text-[10px] md:text-xs font-bold text-primary uppercase tracking-widest">{{ __('Grade') }}</span>
                        </div>
                        <p class="font-heading text-lg md:text-xl font-bold text-charcoal">{{ __('KEPALA') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Premium') }}</p>
                    </div>

                    {{-- Region --}}
                    <div class="p-5 bg-surface rounded-xl border border-surface hover:border-primary/30 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary text-xl">location_on</span>
                            <span class="text-[10px] md:text-xs font-bold text-primary uppercase tracking-widest">{{ __('Asal') }}</span>
                        </div>
                        <p class="font-heading text-lg md:text-xl font-bold text-charcoal">{{ __('Subang') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Jawa Barat') }}</p>
                    </div>

                    {{-- Texture --}}
                    <div class="p-5 bg-surface rounded-xl border border-surface hover:border-primary/30 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary text-xl">bubble_chart</span>
                            <span class="text-[10px] md:text-xs font-bold text-primary uppercase tracking-widest">{{ __('Tekstur') }}</span>
                        </div>
                        <p class="font-heading text-lg md:text-xl font-bold text-charcoal">{{ __('Ultra-Lengket') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Glutinous AAA') }}</p>
                    </div>

                    {{-- Purity --}}
                    <div class="p-5 bg-surface rounded-xl border border-surface hover:border-primary/30 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary text-xl">verified</span>
                            <span class="text-[10px] md:text-xs font-bold text-primary uppercase tracking-widest">{{ __('Kemurnian') }}</span>
                        </div>
                        <p class="font-heading text-lg md:text-xl font-bold text-charcoal">99,9%</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Bebas Kotoran') }}</p>
                    </div>
                </div>

                {{-- Packaging Variants --}}
                <div class="mb-8">
                    <p class="text-xs font-bold text-charcoal uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">package_2</span>
                        {{ __('Pilihan Kemasan Grosir') }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-white border-2 border-gray-200 px-4 py-2 rounded-full text-sm font-bold text-charcoal hover:border-primary hover:text-primary transition-colors cursor-default">{{ __('5 KG') }}</span>
                        <span class="bg-white border-2 border-gray-200 px-4 py-2 rounded-full text-sm font-bold text-charcoal hover:border-primary hover:text-primary transition-colors cursor-default">{{ __('10 KG') }}</span>
                        <span class="bg-primary text-white border-2 border-primary px-4 py-2 rounded-full text-sm font-bold cursor-default shadow-md">{{ __('25 KG ★') }}</span>
                        <span class="bg-white border-2 border-gray-200 px-4 py-2 rounded-full text-sm font-bold text-charcoal hover:border-primary hover:text-primary transition-colors cursor-default">{{ __('50 KG') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ __('★ Kemasan unggulan untuk distributor') }}</p>
                </div>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="https://wa.me/{{ $setting('social_whatsapp', '6281234567890') }}?text={{ rawurlencode(__('Halo PB. Ani Wijaya, saya tertarik dengan Beras Ketan Putih Premium. Mohon info harga grosir.')) }}"
                       target="_blank"
                       class="flex-1 bg-primary text-white px-6 py-4 rounded-xl font-bold hover:bg-primary/90 transition-all shadow-lg flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">chat</span>
                        {{ __('Tanya Harga Grosir') }}
                    </a>
                    <a href="{{ localized_route('contact') }}"
                       class="flex-1 border-2 border-charcoal text-charcoal px-6 py-4 rounded-xl font-bold hover:bg-charcoal hover:text-white transition-all flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">description</span>
                        {{ __('Minta Katalog') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- PRODUCTION PROCESS                            --}}
{{-- ============================================ --}}
<section class="py-16 md:py-24 bg-surface relative overflow-hidden">
    {{-- Decorative element --}}
    <div class="absolute top-10 right-10 opacity-5 hidden md:block pointer-events-none">
        <span class="material-symbols-outlined text-[200px] text-primary">grain</span>
    </div>

    <div class="max-w-7xl mx-auto px-5 md:px-12 relative z-10">
        {{-- Section Header --}}
        <div class="max-w-2xl mx-auto text-center mb-12 md:mb-16">
            <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4" data-aos="fade-up">
                {{ __('Proses Produksi') }}
            </span>
            <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold text-charcoal mb-4" data-aos="fade-up" data-aos-delay="100">
                {{ __('Dari Sawah Menjadi') }} <br> <span class="text-primary italic">{{ __('Produk Berkualitas') }}</span>
            </h2>
            <p class="text-base md:text-lg text-gray-600" data-aos="fade-up" data-aos-delay="200">
                {{ __('Tiga tahap presisi yang menjadikan setiap butir beras kami unggul di kelasnya.') }}
            </p>
        </div>

        {{-- Steps Grid --}}
        <div class="grid md:grid-cols-3 gap-6 md:gap-8 relative">
            {{-- Connector line (desktop only) --}}
            <div class="hidden md:block absolute top-12 left-[16.66%] right-[16.66%] h-0.5 bg-gradient-to-r from-primary/20 via-primary/40 to-primary/20"></div>

            {{-- Step 1 --}}
            <div class="relative bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-surface hover:shadow-lg hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-primary text-white flex items-center justify-center font-heading font-bold text-xl md:text-2xl flex-shrink-0 shadow-md relative z-10">
                        01
                    </div>
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-2xl md:text-3xl">agriculture</span>
                    </div>
                </div>
                <h3 class="font-heading text-xl md:text-2xl font-bold text-charcoal mb-3">{{ __('Seleksi Bahan Baku') }}</h3>
                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                    {{ __('Hanya padi ketan terbaik dari mitra petani Subang yang diterima. Kami memeriksa kadar air, ukuran butir, dan kebersihan secara manual sebelum diproses.') }}
                </p>
                {{-- Quality marker --}}
                <div class="mt-4 pt-4 border-t border-surface flex items-center gap-2 text-xs text-gray-500">
                    <span class="material-symbols-outlined text-base text-gold">check_circle</span>
                    <span class="font-bold">{{ __('Lolos uji kualitas') }}</span>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="relative bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-surface hover:shadow-lg hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-primary text-white flex items-center justify-center font-heading font-bold text-xl md:text-2xl flex-shrink-0 shadow-md relative z-10">
                        02
                    </div>
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-2xl md:text-3xl">settings_suggest</span>
                    </div>
                </div>
                <h3 class="font-heading text-xl md:text-2xl font-bold text-charcoal mb-3">{{ __('Penggilingan Presisi') }}</h3>
                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                    {{ __('Mesin penggiling modern menghilangkan sekam dengan tetap menjaga integritas butir. Proses ini meminimalkan beras patah dan mempertahankan tekstur alami.') }}
                </p>
                {{-- Quality marker --}}
                <div class="mt-4 pt-4 border-t border-surface flex items-center gap-2 text-xs text-gray-500">
                    <span class="material-symbols-outlined text-base text-gold">check_circle</span>
                    <span class="font-bold">{{ __('Teknologi modern') }}</span>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="relative bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-surface hover:shadow-lg hover:-translate-y-1 transition-all" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-primary text-white flex items-center justify-center font-heading font-bold text-xl md:text-2xl flex-shrink-0 shadow-md relative z-10">
                        03
                    </div>
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-2xl md:text-3xl">auto_awesome</span>
                    </div>
                </div>
                <h3 class="font-heading text-xl md:text-2xl font-bold text-charcoal mb-3">{{ __('Pemolesan Ganda') }}</h3>
                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                    {{ __('Proses pemolesan dua tahap memberikan kilau putih mutiara khas Beras Ketan KEPALA. Hasil akhir: butiran bersih, bebas kotoran, siap kemas.') }}
                </p>
                {{-- Quality marker --}}
                <div class="mt-4 pt-4 border-t border-surface flex items-center gap-2 text-xs text-gray-500">
                    <span class="material-symbols-outlined text-base text-gold">check_circle</span>
                    <span class="font-bold">{{ __('Kilau pearl-white') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- QUALITY STANDARDS - BENTO GRID                --}}
{{-- ============================================ --}}
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-5 md:px-12">
        {{-- Section Header --}}
        <div class="max-w-2xl mx-auto text-center mb-12 md:mb-16">
            <span class="inline-block bg-gold/20 text-gold px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4" data-aos="fade-up">
                {{ __('Standar Kualitas') }}
            </span>
            <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold text-charcoal mb-4" data-aos="fade-up" data-aos-delay="100">
                {{ __('Empat Pilar') }} <span class="text-primary italic">{{ __('Mutu Terjamin') }}</span>
            </h2>
            <p class="text-base md:text-lg text-gray-600" data-aos="fade-up" data-aos-delay="200">
                {{ __('Komitmen kami pada kualitas terdokumentasi secara legal dan diaudit secara konsisten.') }}
            </p>
        </div>

        {{-- Bento Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6">

            {{-- Card 1: 99% Purity (Large) --}}
            <div class="md:col-span-7 group relative overflow-hidden rounded-2xl bg-gradient-to-br from-surface to-white border border-surface p-8 md:p-10 hover:shadow-xl transition-all" data-aos="fade-up">
                <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-primary rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="material-symbols-outlined text-white text-3xl md:text-4xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="font-heading text-4xl md:text-5xl font-bold text-primary">99,9%</span>
                            <span class="text-xs md:text-sm font-bold text-gray-500 uppercase tracking-widest">{{ __('Kemurnian') }}</span>
                        </div>
                        <h3 class="font-heading text-xl md:text-2xl font-bold text-charcoal mb-2">{{ __('Bebas Kotoran & Batu') }}</h3>
                        <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                            {{ __('Teknologi optical sorting memastikan setiap karung bebas dari batu, kotoran, dan butir cacat. Konsisten setiap batch produksi.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card 2: Perfect Adhesion (Highlighted Primary) --}}
            <div class="md:col-span-5 group relative overflow-hidden rounded-2xl bg-primary text-white p-8 md:p-10 shadow-xl hover:shadow-2xl transition-all" data-aos="fade-up" data-aos-delay="100">
                <div class="absolute -top-6 -right-6 opacity-10">
                    <span class="material-symbols-outlined text-[120px]">bubble_chart</span>
                </div>
                <div class="relative z-10">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-white text-2xl md:text-3xl">bubble_chart</span>
                    </div>
                    <h3 class="font-heading text-xl md:text-2xl font-bold mb-2">{{ __('Adhesi Sempurna') }}</h3>
                    <p class="text-sm md:text-base text-white/90 leading-relaxed mb-6">
                        {{ __('Tekstur ultra-lengket khas ketan KEPALA — ideal untuk lemper, wajik, dan kue tradisional premium.') }}
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-white/20">
                        <span class="text-xs font-bold uppercase tracking-widest opacity-80">{{ __('Grade Tekstur') }}</span>
                        <span class="font-heading font-bold text-lg">{{ __('AAA') }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Flexible Packaging --}}
            <div class="md:col-span-4 group relative overflow-hidden rounded-2xl bg-white border-2 border-surface p-6 md:p-8 hover:border-primary/30 hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="200">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-2xl">package_2</span>
                </div>
                <h3 class="font-heading text-lg md:text-xl font-bold text-charcoal mb-2">{{ __('Kemasan Fleksibel') }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    {{ __('4 pilihan ukuran untuk berbagai kebutuhan: retail 5kg hingga grosir besar 50kg.') }}
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <span class="text-[10px] bg-surface px-2 py-1 rounded font-bold text-charcoal">{{ __('5KG') }}</span>
                    <span class="text-[10px] bg-surface px-2 py-1 rounded font-bold text-charcoal">{{ __('10KG') }}</span>
                    <span class="text-[10px] bg-surface px-2 py-1 rounded font-bold text-charcoal">{{ __('25KG') }}</span>
                    <span class="text-[10px] bg-surface px-2 py-1 rounded font-bold text-charcoal">{{ __('50KG') }}</span>
                </div>
            </div>

            {{-- Card 4: NIB Certified (Gold Accent) --}}
            <div class="md:col-span-8 group relative overflow-hidden rounded-2xl bg-gradient-to-br from-gold/20 to-gold/5 border-2 border-gold/30 p-6 md:p-8 hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="300">
                <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                    {{-- NIB Stamp Visual --}}
                    <div class="relative flex-shrink-0">
                        <div class="w-24 h-24 md:w-28 md:h-28 rounded-full border-4 border-gold flex items-center justify-center bg-white text-center p-2">
                            <div>
                                <span class="material-symbols-outlined text-gold text-2xl block mb-0.5" style="font-variation-settings: 'FILL' 1;">verified</span>
                                <span class="text-[9px] font-bold text-gold uppercase tracking-widest leading-tight block">{{ __('Mutu') }}<br>{{ __('Terjamin') }}</span>
                            </div>
                        </div>
                        <div class="absolute -top-1 -right-1 bg-primary text-white text-[9px] px-2 py-0.5 rounded-full font-bold">{{ __('CERTIFIED') }}</div>
                    </div>

                    <div class="flex-1">
                        <h3 class="font-heading text-lg md:text-2xl font-bold text-charcoal mb-2">{{ __('Sertifikasi Resmi NIB') }}</h3>
                        <p class="text-sm md:text-base text-gray-700 leading-relaxed mb-3">
                            {{ __('Terdaftar dan diverifikasi oleh regulator Indonesia. Komitmen kualitas kami terdokumentasi secara legal.') }}
                        </p>
                        <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur px-3 py-1.5 rounded-lg border border-gold/30">
                            <span class="text-[10px] font-bold text-gold uppercase tracking-widest">{{ __('NIB:') }}</span>
                            <span class="text-xs md:text-sm font-bold text-charcoal">{{ $setting('company_nib', '1412210030165') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- CTA INQUIRY                                   --}}
{{-- ============================================ --}}
<section class="py-16 md:py-24 px-5 md:px-12">
    <div class="max-w-6xl mx-auto">
        <div class="bg-gradient-to-br from-charcoal via-charcoal to-primary/40 rounded-3xl p-8 md:p-16 text-center relative overflow-hidden">
            {{-- Decorative pattern --}}
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <div class="absolute top-10 left-10 w-32 h-32 md:w-64 md:h-64 border-2 border-white rounded-full"></div>
                <div class="absolute -bottom-10 -right-10 w-48 h-48 md:w-96 md:h-96 border-2 border-white rounded-full"></div>
            </div>

            <div class="relative z-10 max-w-3xl mx-auto">
                {{-- Badge --}}
                <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur" data-aos="fade-up">
                    {{ __('Pasokan Grosir') }}
                </span>

                {{-- Heading --}}
                <h2 class="font-heading text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
                    {{ __('Siap Skala') }} <br class="md:hidden">
                    <span class="text-primary italic">{{ __('Bisnis Anda?') }}</span>
                </h2>

                {{-- Subtitle --}}
                <p class="text-base md:text-lg text-white/80 mb-10 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    {{ __('Distributor, retailer, atau pelaku UMKM kuliner — kami siap menjadi mitra pasokan beras ketan premium Anda.') }}
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-10" data-aos="fade-up" data-aos-delay="300">
                    <a href="https://wa.me/{{ $setting('social_whatsapp', '6281234567890') }}?text={{ rawurlencode(__('Halo PB. Ani Wijaya, saya ingin konsultasi harga grosir Beras Ketan Putih.')) }}"
                       target="_blank"
                       class="bg-primary text-white px-8 py-4 rounded-xl font-bold hover:bg-primary/90 transition-all shadow-xl flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">chat</span>
                        {{ __('Chat WhatsApp Sales') }}
                    </a>
                    <a href="mailto:{{ $setting('contact_email', 'info@pbaniwijaya.com') }}?subject={{ rawurlencode(__('Permintaan Katalog Harga Grosir')) }}"
                       class="bg-white/10 backdrop-blur text-white border-2 border-white/30 px-8 py-4 rounded-xl font-bold hover:bg-white/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">mail</span>
                        {{ __('Email Katalog') }}
                    </a>
                </div>

                {{-- Trust Indicators --}}
                <div class="flex flex-wrap justify-center gap-6 md:gap-10 pt-8 border-t border-white/10" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex items-center gap-2 text-white/70">
                        <span class="material-symbols-outlined text-gold text-base">public</span>
                        <span class="text-xs md:text-sm font-bold">{{ __('Pengiriman Nasional') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/70">
                        <span class="material-symbols-outlined text-gold text-base">verified_user</span>
                        <span class="text-xs md:text-sm font-bold">{{ __('Stok Terjamin') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/70">
                        <span class="material-symbols-outlined text-gold text-base">handshake</span>
                        <span class="text-xs md:text-sm font-bold">{{ __('Kemitraan Jangka Panjang') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection