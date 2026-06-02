@extends('layouts.public')

@section('title', 'Tentang Kami')

@section('content')

{{-- ========================================== --}}
{{-- HERO SECTION                                --}}
{{-- ========================================== --}}
<section class="relative py-20 md:py-32 bg-gradient-to-br from-charcoal via-charcoal to-primary/30 overflow-hidden">

    {{-- Decorative bg --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gold/10 rounded-full blur-3xl"></div>

    <div class="relative z-10 max-w-5xl mx-auto px-5 md:px-12 text-center">

        {{-- Breadcrumb --}}
        <div class="flex items-center justify-center gap-2 text-white/60 text-sm mb-8" data-aos="fade-down">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-gold">Tentang Kami</span>
        </div>

        <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur" data-aos="fade-up">
            Established Legacy
        </span>

        <h1 class="font-heading text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
            Warisan Indramayu, <br>
            <span class="text-primary italic">Mutu Terjamin</span>
        </h1>

        <p class="text-base md:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="200">
            Selama puluhan tahun, PB. Ani Wijaya menjadi pondasi produksi beras berkualitas — memadukan kearifan tradisional Indramayu dengan teknologi modern untuk menghidupi keluarga di seluruh Indonesia.
        </p>
    </div>
</section>

{{-- ========================================== --}}
{{-- STORY SECTION                               --}}
{{-- ========================================== --}}
<section class="py-20 md:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-5 md:px-12">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Left: Image with Decoration --}}
            <div class="relative" data-aos="fade-right">
                <div class="aspect-[4/5] rounded-3xl bg-gradient-to-br from-primary/10 to-gold/10 overflow-hidden shadow-2xl relative">
                    {{-- Background ornament --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}"
                             alt="PB. Ani Wijaya Logo"
                             class="w-2/3 h-2/3 object-contain opacity-90 drop-shadow-2xl">
                    </div>

                    {{-- Floating decoration --}}
                    <div class="absolute top-6 right-6 bg-white/90 backdrop-blur px-4 py-2 rounded-full shadow-lg">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold text-base" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                            <span class="text-xs font-bold text-charcoal">Sejak Berdiri</span>
                        </div>
                    </div>
                </div>

                {{-- Quote Card Floating --}}
                <div class="hidden lg:block absolute -bottom-10 -right-10 bg-primary p-8 rounded-2xl shadow-2xl max-w-xs text-white">
                    <span class="material-symbols-outlined text-gold text-4xl mb-2">format_quote</span>
                    <p class="font-heading text-lg italic leading-relaxed">
                        "Setiap butir beras adalah janji kami untuk kualitas."
                    </p>
                    <div class="mt-4 pt-4 border-t border-white/20 text-xs uppercase tracking-widest text-white/70">
                        — Tim PB. Ani Wijaya
                    </div>
                </div>
            </div>

            {{-- Right: Story Text --}}
            <div data-aos="fade-left">
                <span class="text-primary font-bold text-xs uppercase tracking-widest">Cerita Kami</span>

                <h2 class="font-heading text-3xl md:text-5xl font-bold text-charcoal mt-3 mb-8 leading-tight">
                    {{ $setting('about_title', 'Perjalanan Ketekunan & Keunggulan') }}
                </h2>

                <div class="w-20 h-1 bg-gold mb-8"></div>

                <div class="space-y-6 text-gray-600 leading-relaxed">
                    <p>
                        {{ $setting('about_paragraph_1', 'Bermula dari mimpi keluarga di tanah subur Indramayu, Jawa Barat, PB. Ani Wijaya tumbuh dari penggilingan kecil menjadi produsen beras premium yang melayani distributor di seluruh Indonesia.') }}
                    </p>

                    <p>
                        {{ $setting('about_paragraph_2', 'Nama "Ani Wijaya" identik dengan kepercayaan. Kami memahami bahwa beras lebih dari sekadar bahan pokok — ia adalah pusat meja makan keluarga Indonesia. Tanggung jawab ini menggerakkan filosofi "Mutu Terjamin" kami.') }}
                    </p>
                </div>

                {{-- Stats Mini --}}
                <div class="mt-10 grid grid-cols-3 gap-6">
                    <div>
                        <div class="font-heading text-3xl md:text-4xl font-bold text-primary">{{ $setting('years_experience', '25') }}+</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Tahun Pengalaman</div>
                    </div>
                    <div>
                        <div class="font-heading text-3xl md:text-4xl font-bold text-primary">{{ $setting('partners_count', '500') }}+</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Mitra Distribusi</div>
                    </div>
                    <div>
                        <div class="font-heading text-3xl md:text-4xl font-bold text-primary">100%</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Local Sourced</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================== --}}
{{-- VISI MISI SECTION (BENTO)                   --}}
{{-- ========================================== --}}
<section class="py-20 md:py-32 bg-surface">
    <div class="max-w-7xl mx-auto px-5 md:px-12">

        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-primary font-bold text-xs uppercase tracking-widest">Visi & Misi</span>
            <h2 class="font-heading text-3xl md:text-5xl font-bold text-charcoal mt-3 mb-4">
                Tujuan & <span class="text-primary italic">Arah Langkah</span>
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Apa yang ingin kami capai, dan bagaimana cara kami mencapainya.
            </p>
        </div>

        <div class="grid md:grid-cols-12 gap-6 md:gap-8">

            {{-- Visi Card (Highlighted) --}}
            <div class="md:col-span-7 bg-primary rounded-3xl p-10 md:p-14 text-white relative overflow-hidden group" data-aos="fade-right">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-gold/10 rounded-full -ml-24 -mb-24"></div>

                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings: 'FILL' 1;">visibility</span>
                    </div>

                    <span class="text-gold font-bold text-xs uppercase tracking-widest">Visi Kami</span>

                    <h3 class="font-heading text-2xl md:text-4xl font-bold mt-3 mb-6 leading-tight">
                        Menjadi tolak ukur kualitas dan reliabilitas dalam industri penggilingan beras Indonesia.
                    </h3>

                    <p class="text-white/90 leading-relaxed">
                        Memberdayakan petani lokal dan memodernisasi rantai pasok beras tradisional — tanpa kehilangan jiwa budaya kami.
                    </p>
                </div>
            </div>

            {{-- Misi Card --}}
            <div class="md:col-span-5 bg-white border-2 border-gray-100 rounded-3xl p-10 md:p-14" data-aos="fade-left">
                <div class="w-16 h-16 bg-gold/20 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-gold text-3xl" style="font-variation-settings: 'FILL' 1;">flag</span>
                </div>

                <span class="text-primary font-bold text-xs uppercase tracking-widest">Misi Strategis</span>

                <h3 class="font-heading text-2xl md:text-3xl font-bold mt-3 mb-6 text-charcoal">
                    Bagaimana Kami Bertindak
                </h3>

                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="text-gray-600">Memanfaatkan teknologi mutakhir untuk kemurnian dan hasil panen yang optimal.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="text-gray-600">Membangun kemitraan jangka panjang berdasarkan transparansi dan integritas.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="text-gray-600">Berkontribusi pada ketahanan pangan nasional melalui supply chain yang konsisten.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ========================================== --}}
{{-- NILAI INTI SECTION                          --}}
{{-- ========================================== --}}
<section class="py-20 md:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-5 md:px-12">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
            <div data-aos="fade-up">
                <span class="text-primary font-bold text-xs uppercase tracking-widest">Pilar Kesuksesan</span>
                <h2 class="font-heading text-3xl md:text-5xl font-bold text-charcoal mt-3 leading-tight">
                    Nilai yang Kami <span class="text-primary italic">Pegang Teguh</span>
                </h2>
            </div>
            <p class="text-gray-600 max-w-md" data-aos="fade-up" data-aos-delay="100">
                Tiga prinsip yang memandu setiap keputusan kami — dari pemilihan ladang hingga kemasan final.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Value 1: Quality --}}
            <div class="group p-8 md:p-10 bg-surface rounded-3xl hover:bg-charcoal hover:text-white transition-all duration-500" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-white group-hover:bg-primary rounded-2xl flex items-center justify-center mb-8 transition-colors">
                    <span class="material-symbols-outlined text-primary group-hover:text-white text-3xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                </div>

                <h4 class="font-heading text-2xl font-bold mb-4">Kualitas Utama</h4>

                <p class="text-gray-600 group-hover:text-white/80 leading-relaxed transition-colors">
                    Kualitas bukanlah tindakan, melainkan kebiasaan. Kami menerapkan uji laboratorium ketat untuk kelembaban, kemurnian, dan integritas butiran.
                </p>
            </div>

            {{-- Value 2: Integrity --}}
            <div class="group p-8 md:p-10 bg-primary text-white rounded-3xl shadow-2xl md:-translate-y-4 hover:-translate-y-6 transition-all duration-500" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mb-8">
                    <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings: 'FILL' 1;">handshake</span>
                </div>

                <h4 class="font-heading text-2xl font-bold mb-4">Integritas</h4>

                <p class="text-white/90 leading-relaxed">
                    Membangun kepercayaan dengan distributor dan retail partner melalui harga yang jujur, sertifikasi berat akurat (NIB), dan konsistensi yang tak tergoyahkan.
                </p>
            </div>

            {{-- Value 3: Innovation --}}
            <div class="group p-8 md:p-10 bg-surface rounded-3xl hover:bg-charcoal hover:text-white transition-all duration-500" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-white group-hover:bg-primary rounded-2xl flex items-center justify-center mb-8 transition-colors">
                    <span class="material-symbols-outlined text-primary group-hover:text-white text-3xl" style="font-variation-settings: 'FILL' 1;">settings_suggest</span>
                </div>

                <h4 class="font-heading text-2xl font-bold mb-4">Inovasi Tradisional</h4>

                <p class="text-gray-600 group-hover:text-white/80 leading-relaxed transition-colors">
                    Menghormati kearifan pertanian tradisional sambil berinvestasi pada teknologi penggilingan modern untuk meminimalkan limbah dan memaksimalkan kualitas.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ========================================== --}}
{{-- CERTIFICATION BANNER                        --}}
{{-- ========================================== --}}
<section class="py-16 bg-gradient-to-r from-charcoal to-primary/30">
    <div class="max-w-5xl mx-auto px-5 md:px-12">
        <div class="text-center text-white" data-aos="zoom-in">
            <div class="inline-flex items-center justify-center gap-3 bg-gold/20 backdrop-blur px-6 py-2 rounded-full mb-6">
                <span class="material-symbols-outlined text-gold" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                <span class="text-gold font-bold text-xs uppercase tracking-widest">Resmi & Terpercaya</span>
            </div>

            <h3 class="font-heading text-2xl md:text-4xl font-bold mb-4">
                Terverifikasi NIB
            </h3>

            <p class="text-white/80 mb-8 max-w-2xl mx-auto">
                Lisensi resmi dari pemerintah Indonesia dengan nomor registrasi yang dapat diverifikasi publik.
            </p>

            <div class="inline-block bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-8 py-4">
                <div class="text-xs uppercase tracking-widest text-white/60 mb-1">Business Registration ID</div>
                <div class="font-heading text-3xl md:text-4xl font-bold text-gold">
                    {{ $setting('company_nib', '1412210030165') }}
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================== --}}
{{-- CTA SECTION                                 --}}
{{-- ========================================== --}}
<section class="py-20 md:py-32 bg-white">
    <div class="max-w-5xl mx-auto px-5 md:px-12 text-center" data-aos="fade-up">

        <h2 class="font-heading text-3xl md:text-5xl font-bold text-charcoal mb-6 leading-tight">
            Siap menjadi bagian dari <br>
            <span class="text-primary italic">cerita kami selanjutnya?</span>
        </h2>

        <p class="text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
            Distributor, retailer, atau pemilik usaha kuliner — kami terbuka untuk diskusi kerjasama jangka panjang yang saling menguntungkan.
        </p>

        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('contact') }}"
               class="bg-primary text-white px-8 py-4 rounded-lg font-bold hover:bg-primary-hover transition-all shadow-lg hover:shadow-xl active:scale-95 inline-flex items-center gap-2">
                <span class="material-symbols-outlined">mail</span>
                <span>Hubungi Kami</span>
            </a>
            <a href="{{ route('products') }}"
               class="border-2 border-charcoal text-charcoal px-8 py-4 rounded-lg font-bold hover:bg-charcoal hover:text-white transition-all active:scale-95 inline-flex items-center gap-2">
                <span>Lihat Produk</span>
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

@endsection
