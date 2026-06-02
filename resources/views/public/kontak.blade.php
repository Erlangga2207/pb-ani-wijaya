@extends('layouts.public')

@section('title', 'Kontak - Hubungi PB. Ani Wijaya')

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
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-gold">Kontak</span>
        </div>

        {{-- Badge --}}
        <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur" data-aos="fade-up">
            Mari Berkolaborasi
        </span>

        {{-- Title --}}
        <h1 class="font-heading text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
            Hubungi Kami, <br>
            <span class="text-primary italic">Kami Siap Melayani.</span>
        </h1>

        {{-- Subtitle --}}
        <p class="text-base md:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="200">
            Distributor, retailer, atau pelaku UMKM kuliner — tim PB. Ani Wijaya siap merespons setiap pertanyaan kemitraan, harga grosir, atau kunjungan fasilitas.
        </p>
    </div>
</section>

{{-- ============================================ --}}
{{-- MAIN CONTACT SECTION (Form + Sidebar)         --}}
{{-- ============================================ --}}
<section class="py-12 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-5 md:px-12">

        {{-- Success Flash Message --}}
        @if(session('success'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-transition
                 class="mb-8 bg-green-50 border-l-4 border-green-500 rounded-r-xl p-4 md:p-5 flex items-start gap-4"
                 data-aos="fade-down">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white">check</span>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-green-900 mb-1">Berhasil Terkirim!</h4>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-700 hover:text-green-900 transition-colors p-1">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        @endif

        {{-- Validation Errors Summary --}}
        @if($errors->any())
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 md:p-5 flex items-start gap-4" data-aos="fade-down">
                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white">error</span>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-red-900 mb-1">Mohon periksa kembali:</h4>
                    <ul class="text-sm text-red-800 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- 2-Column Layout: Form (8 cols) + Sidebar (4 cols) --}}
        <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            {{-- ===== LEFT: FORM (8 cols) ===== --}}
            <div class="lg:col-span-8" data-aos="fade-right">
                <div class="bg-surface/50 border border-surface rounded-3xl p-6 md:p-10 lg:p-12">
                    {{-- Header --}}
                    <div class="mb-8">
                        <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-3">
                            Formulir Pertanyaan
                        </span>
                        <h2 class="font-heading text-2xl md:text-4xl font-bold text-charcoal mb-3">
                            Kirim Pesan ke <span class="text-primary italic">Tim Kami</span>
                        </h2>
                        <p class="text-sm md:text-base text-gray-600">
                            Isi formulir di bawah dan tim sales akan merespons dalam <strong>1×24 jam kerja</strong>.
                        </p>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Name + Email Grid --}}
                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="flex items-center gap-1.5 text-xs font-bold text-charcoal uppercase tracking-widest mb-2">
                                    <span class="material-symbols-outlined text-primary text-base">person</span>
                                    Nama Lengkap <span class="text-primary">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Nama Anda"
                                       required
                                       class="w-full bg-white border-2 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }} rounded-xl px-4 py-3 md:py-3.5 text-charcoal placeholder:text-gray-400 focus:outline-none focus:border-primary transition-colors">
                                @error('name')
                                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">error</span>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="flex items-center gap-1.5 text-xs font-bold text-charcoal uppercase tracking-widest mb-2">
                                    <span class="material-symbols-outlined text-primary text-base">mail</span>
                                    Email <span class="text-primary">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="email@perusahaan.com"
                                       required
                                       class="w-full bg-white border-2 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} rounded-xl px-4 py-3 md:py-3.5 text-charcoal placeholder:text-gray-400 focus:outline-none focus:border-primary transition-colors">
                                @error('email')
                                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">error</span>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="subject" class="flex items-center gap-1.5 text-xs font-bold text-charcoal uppercase tracking-widest mb-2">
                                <span class="material-symbols-outlined text-primary text-base">label</span>
                                Subjek <span class="text-gray-400 normal-case font-normal">(opsional)</span>
                            </label>
                            <input type="text"
                                   id="subject"
                                   name="subject"
                                   value="{{ old('subject') }}"
                                   placeholder="Mis. Permintaan Harga Grosir / Kemitraan / Kunjungan Pabrik"
                                   class="w-full bg-white border-2 {{ $errors->has('subject') ? 'border-red-500' : 'border-gray-200' }} rounded-xl px-4 py-3 md:py-3.5 text-charcoal placeholder:text-gray-400 focus:outline-none focus:border-primary transition-colors">
                            @error('subject')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div>
                            <label for="message" class="flex items-center gap-1.5 text-xs font-bold text-charcoal uppercase tracking-widest mb-2">
                                <span class="material-symbols-outlined text-primary text-base">edit_note</span>
                                Pesan <span class="text-primary">*</span>
                            </label>
                            <textarea id="message"
                                      name="message"
                                      rows="6"
                                      placeholder="Ceritakan kebutuhan Anda — jumlah kebutuhan beras, lokasi pengiriman, jadwal, atau pertanyaan lainnya..."
                                      required
                                      class="w-full bg-white border-2 {{ $errors->has('message') ? 'border-red-500' : 'border-gray-200' }} rounded-xl px-4 py-3 md:py-3.5 text-charcoal placeholder:text-gray-400 focus:outline-none focus:border-primary transition-colors resize-none">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-500">Maksimal 5000 karakter</p>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full md:w-auto bg-primary text-white px-8 md:px-12 py-4 rounded-xl font-bold text-base hover:bg-primary/90 transition-all shadow-lg flex items-center justify-center gap-2 active:scale-95">
                                <span class="material-symbols-outlined">send</span>
                                Kirim Pesan Sekarang
                            </button>
                            <p class="mt-3 text-xs text-gray-500 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm text-gold">lock</span>
                                Data Anda aman dan hanya digunakan untuk merespons pertanyaan ini.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== RIGHT: SIDEBAR INFO (4 cols) ===== --}}
            <div class="lg:col-span-4 space-y-6" data-aos="fade-left">

                {{-- Card 1: Kontak Langsung --}}
                <div class="bg-charcoal text-white rounded-2xl p-6 md:p-8 relative overflow-hidden">
                    {{-- Decorative element --}}
                    <div class="absolute -top-8 -right-8 opacity-10">
                        <span class="material-symbols-outlined text-[120px]">support_agent</span>
                    </div>

                    <div class="relative z-10">
                        <h3 class="font-heading text-xl md:text-2xl font-bold mb-2">Kontak Langsung</h3>
                        <p class="text-xs text-white/60 mb-6">Respons cepat melalui channel berikut</p>

                        <div class="space-y-4">
                            {{-- WhatsApp --}}
                            <a href="https://wa.me/{{ $setting('social_whatsapp', '6281234567890') }}?text=Halo%20PB.%20Ani%20Wijaya,%20saya%20ingin%20bertanya..."
                               target="_blank"
                               class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-colors group">
                                <div class="w-11 h-11 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-white">chat</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest">WhatsApp</p>
                                    <p class="text-sm font-bold truncate">+{{ $setting('social_whatsapp', '6281234567890') }}</p>
                                </div>
                                <span class="material-symbols-outlined text-white/40 group-hover:text-white transition-colors">arrow_outward</span>
                            </a>

                            {{-- Phone --}}
                            <a href="tel:{{ $setting('contact_phone', '+6281234567890') }}"
                               class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-colors group">
                                <div class="w-11 h-11 bg-primary rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-white">call</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Telepon</p>
                                    <p class="text-sm font-bold truncate">{{ $setting('contact_phone', '+62 812-3456-7890') }}</p>
                                </div>
                                <span class="material-symbols-outlined text-white/40 group-hover:text-white transition-colors">arrow_outward</span>
                            </a>

                            {{-- Email --}}
                            <a href="mailto:{{ $setting('contact_email', 'info@pbaniwijaya.com') }}"
                               class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-colors group">
                                <div class="w-11 h-11 bg-gold rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-charcoal">mail</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Email</p>
                                    <p class="text-sm font-bold truncate">{{ $setting('contact_email', 'info@pbaniwijaya.com') }}</p>
                                </div>
                                <span class="material-symbols-outlined text-white/40 group-hover:text-white transition-colors">arrow_outward</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Alamat Pabrik --}}
                <div class="bg-white border-2 border-surface rounded-2xl p-6 md:p-8 hover:border-primary/30 transition-colors">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-11 h-11 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">location_on</span>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-bold text-charcoal">Alamat Pabrik</h3>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Kantor &amp; Produksi</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-700 leading-relaxed mb-4">
                        {{ $setting('contact_address', 'Indramayu, Jawa Barat, Indonesia') }}
                    </p>

                    {{-- NIB Verification --}}
                    <div class="pt-4 border-t border-surface">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="material-symbols-outlined text-gold text-sm">verified</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">NIB Terdaftar</span>
                        </div>
                        <p class="text-sm font-bold text-charcoal">{{ $setting('company_nib', '1412210030165') }}</p>
                    </div>
                </div>

                {{-- Card 3: Jam Operasional --}}
                <div class="bg-gradient-to-br from-gold/20 to-gold/5 border-2 border-gold/30 rounded-2xl p-6 md:p-8">
                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-11 h-11 bg-gold rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-charcoal" style="font-variation-settings: 'FILL' 1;">schedule</span>
                        </div>
                        <div>
                            <h3 class="font-heading text-lg font-bold text-charcoal">Jam Operasional</h3>
                            <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Waktu Pelayanan</p>
                        </div>
                    </div>

                    <ul class="space-y-3">
                        {{-- Senin - Jumat --}}
                        <li class="flex items-center justify-between pb-3 border-b border-gold/20">
                            <span class="text-sm font-bold text-charcoal">Senin – Jumat</span>
                            <span class="text-sm text-charcoal/80">08:00 – 17:00</span>
                        </li>
                        {{-- Sabtu --}}
                        <li class="flex items-center justify-between pb-3 border-b border-gold/20">
                            <span class="text-sm font-bold text-charcoal">Sabtu</span>
                            <span class="text-sm text-charcoal/80">08:00 – 14:00</span>
                        </li>
                        {{-- Minggu --}}
                        <li class="flex items-center justify-between">
                            <span class="text-sm font-bold text-charcoal">Minggu</span>
                            <span class="text-sm text-red-600 font-bold">Tutup</span>
                        </li>
                    </ul>

                    {{-- Note --}}
                    <div class="mt-5 pt-4 border-t border-gold/20 flex items-start gap-2">
                        <span class="material-symbols-outlined text-gold text-base mt-0.5">info</span>
                        <p class="text-xs text-charcoal/80 leading-relaxed">
                            Untuk kunjungan pabrik, mohon konfirmasi via WhatsApp <strong>minimal 1 hari sebelumnya</strong>.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- GOOGLE MAPS SECTION                           --}}
{{-- ============================================ --}}
<section class="py-12 md:py-20 bg-surface">
    <div class="max-w-7xl mx-auto px-5 md:px-12">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8" data-aos="fade-up">
            <div>
                <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-3">
                    Lokasi Fasilitas
                </span>
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-charcoal">
                    Temukan Kami di <span class="text-primary italic">Indramayu</span>
                </h2>
            </div>

            @php
                $mapsUrl = $setting('maps_embed_url');
                $mapsLink = $setting('contact_maps_link', '');
                $addressForDirections = urlencode($setting('contact_address', 'Indramayu, Jawa Barat'));
                $directionsHref = !empty($mapsLink) ? $mapsLink : 'https://www.google.com/maps/dir/?api=1&destination=' . $addressForDirections;
            @endphp

            <a href="{{ $directionsHref }}"
               target="_blank"
               class="inline-flex items-center gap-2 bg-charcoal text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-charcoal/90 transition-all active:scale-95">
                <span class="material-symbols-outlined text-base">directions</span>
                Petunjuk Arah
            </a>
        </div>

        {{-- Maps Container --}}
        <div class="relative w-full aspect-[16/10] md:aspect-[21/9] rounded-2xl md:rounded-3xl overflow-hidden border border-outline-variant shadow-lg" data-aos="fade-up" data-aos-delay="100">
            @if(!empty($mapsUrl))
                {{-- Google Maps Embed --}}
                <iframe src="{{ $mapsUrl }}"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="absolute inset-0 w-full h-full">
                </iframe>
            @else
                {{-- Fallback if maps_embed_url is empty --}}
                <div class="absolute inset-0 bg-gradient-to-br from-charcoal to-charcoal/80 flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-20 h-20 bg-white/10 backdrop-blur rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-gold text-4xl" style="font-variation-settings: 'FILL' 1;">location_on</span>
                    </div>
                    <h3 class="font-heading text-2xl md:text-3xl font-bold text-white mb-3">{{ $setting('company_name', 'PB. Ani Wijaya') }}</h3>
                    <p class="text-white/80 text-sm md:text-base max-w-md mb-6">
                        {{ $setting('contact_address', 'Indramayu, Jawa Barat, Indonesia') }}
                    </p>
                    <a href="{{ !empty($mapsLink) ? $mapsLink : 'https://www.google.com/maps/search/?api=1&query=' . $addressForDirections }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-primary/90 transition-all active:scale-95">
                        <span class="material-symbols-outlined">map</span>
                        Buka di Google Maps
                    </a>
                </div>
            @endif

            {{-- Floating Info Card (overlay on maps) --}}
            @if(!empty($mapsUrl))
                <div class="absolute bottom-4 left-4 md:bottom-6 md:left-6 bg-white/95 backdrop-blur-md rounded-xl shadow-xl border border-surface p-4 md:p-5 max-w-[260px] md:max-w-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-primary text-base" style="font-variation-settings: 'FILL' 1;">location_on</span>
                        <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Lokasi Utama</span>
                    </div>
                    <h4 class="font-heading text-base md:text-lg font-bold text-charcoal mb-2 leading-tight">
                        {{ $setting('company_name', 'PB. Ani Wijaya') }}
                    </h4>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        {{ $setting('contact_short_address', $setting('contact_address', 'Indramayu, Jawa Barat')) }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- TRUST CTA BANNER                              --}}
{{-- ============================================ --}}
<section class="py-12 md:py-20 px-5 md:px-12">
    <div class="max-w-6xl mx-auto">
        <div class="bg-gradient-to-br from-charcoal via-charcoal to-primary/40 rounded-3xl p-8 md:p-12 lg:p-16 text-center relative overflow-hidden">
            {{-- Decorative circles --}}
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <div class="absolute top-10 left-10 w-32 h-32 md:w-64 md:h-64 border-2 border-white rounded-full"></div>
                <div class="absolute -bottom-10 -right-10 w-48 h-48 md:w-96 md:h-96 border-2 border-white rounded-full"></div>
            </div>

            <div class="relative z-10 max-w-3xl mx-auto">
                {{-- Badge --}}
                <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur" data-aos="fade-up">
                    Mutu Terjamin
                </span>

                {{-- Heading --}}
                <h2 class="font-heading text-3xl md:text-5xl font-bold text-white mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
                    Butuh Respons <span class="text-primary italic">Lebih Cepat?</span>
                </h2>

                {{-- Subtitle --}}
                <p class="text-base md:text-lg text-white/80 mb-10 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    Untuk konsultasi mendesak atau permintaan harga grosir hari ini, langsung hubungi tim sales kami via WhatsApp.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 justify-center" data-aos="fade-up" data-aos-delay="300">
                    <a href="https://wa.me/{{ $setting('social_whatsapp', '6281234567890') }}?text=Halo%20PB.%20Ani%20Wijaya,%20saya%20ingin%20konsultasi%20mendesak."
                       target="_blank"
                       class="bg-green-500 text-white px-8 py-4 rounded-xl font-bold hover:bg-green-600 transition-all shadow-xl flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">chat</span>
                        Chat WhatsApp Sekarang
                    </a>
                    <a href="{{ route('products') }}"
                       class="bg-white/10 backdrop-blur text-white border-2 border-white/30 px-8 py-4 rounded-xl font-bold hover:bg-white/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">grid_view</span>
                        Lihat Produk Dulu
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection