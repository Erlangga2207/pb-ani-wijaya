@extends('layouts.public')

@section('title', __('Tentang Kami - Pabrik Beras Ketan Subang'))
@section('description', __('Profil PB. Ani Wijaya, pabrik penggilingan beras ketan di Subang, Jawa Barat. Mengolah gabah ketan dari petani lokal menjadi beras ketan premium.'))

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
            <a href="{{ localized_route('home') }}" class="hover:text-gold transition-colors">{{ __('Beranda') }}</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-gold">{{ __('Tentang Kami') }}</span>
        </div>

        <span class="inline-block bg-gold/20 text-gold px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur" data-aos="fade-up">
            {{ __('Established Legacy') }}
        </span>

        <h1 class="font-heading text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
            {{ __('Warisan Subang,') }} <br>
            <span class="text-primary italic">{{ __('Mutu Terjamin') }}</span>
        </h1>

        <p class="text-base md:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="200">
            {{ __('Selama puluhan tahun, PB. Ani Wijaya menjadi pondasi produksi beras berkualitas — memadukan kearifan tradisional Subang dengan teknologi modern untuk menghidupi keluarga di seluruh Indonesia.') }}
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
                    <img src="{{ asset('images/foto_pabrik.webp') }}"
                         alt="{{ __('Pabrik PB. Ani Wijaya') }}"
                         loading="lazy"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-charcoal/40 via-transparent to-transparent"></div>

                    {{-- Floating decoration --}}
                    <div class="absolute top-6 right-6 bg-white/90 backdrop-blur px-4 py-2 rounded-full shadow-lg">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold text-base" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                            <span class="text-xs font-bold text-charcoal">{{ __('Sejak Berdiri') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quote Card Floating --}}
                <div class="hidden lg:block absolute -bottom-10 -right-10 bg-primary p-8 rounded-2xl shadow-2xl max-w-xs text-white">
                    <span class="material-symbols-outlined text-gold text-4xl mb-2">format_quote</span>
                    <p class="font-heading text-lg italic leading-relaxed">
                        {{ __('"Setiap butir beras adalah janji kami untuk kualitas."') }}
                    </p>
                    <div class="mt-4 pt-4 border-t border-white/20 text-xs uppercase tracking-widest text-white/70">
                        {{ __('— Tim PB. Ani Wijaya') }}
                    </div>
                </div>
            </div>

            {{-- Right: Story Text --}}
            <div data-aos="fade-left">
                <span class="text-primary font-bold text-xs uppercase tracking-widest">{{ __('Cerita Kami') }}</span>

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
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">{{ __('Tahun Pengalaman') }}</div>
                    </div>
                    <div>
                        <div class="font-heading text-3xl md:text-4xl font-bold text-primary">{{ $setting('partners_count', '500') }}+</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">{{ __('Mitra Distribusi') }}</div>
                    </div>
                    <div>
                        <div class="font-heading text-3xl md:text-4xl font-bold text-primary">100%</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">{{ __('Local Sourced') }}</div>
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
            <span class="text-primary font-bold text-xs uppercase tracking-widest">{{ __('Visi & Misi') }}</span>
            <h2 class="font-heading text-3xl md:text-5xl font-bold text-charcoal mt-3 mb-4">
                {{ __('Tujuan &') }} <span class="text-primary italic">{{ __('Arah Langkah') }}</span>
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                {{ __('Apa yang ingin kami capai, dan bagaimana cara kami mencapainya.') }}
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

                    <span class="text-gold font-bold text-xs uppercase tracking-widest">{{ __('Visi Kami') }}</span>

                    <h3 class="font-heading text-2xl md:text-4xl font-bold mt-3 mb-6 leading-tight">
                        {{ __('Menjadi tolak ukur kualitas dan reliabilitas dalam industri penggilingan beras Indonesia.') }}
                    </h3>

                    <p class="text-white/90 leading-relaxed">
                        {{ __('Memberdayakan petani lokal dan memodernisasi rantai pasok beras tradisional — tanpa kehilangan jiwa budaya kami.') }}
                    </p>
                </div>
            </div>

            {{-- Misi Card --}}
            <div class="md:col-span-5 bg-white border-2 border-gray-100 rounded-3xl p-10 md:p-14" data-aos="fade-left">
                <div class="w-16 h-16 bg-gold/20 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-gold text-3xl" style="font-variation-settings: 'FILL' 1;">flag</span>
                </div>

                <span class="text-primary font-bold text-xs uppercase tracking-widest">{{ __('Misi Strategis') }}</span>

                <h3 class="font-heading text-2xl md:text-3xl font-bold mt-3 mb-6 text-charcoal">
                    {{ __('Bagaimana Kami Bertindak') }}
                </h3>

                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="text-gray-600">{{ __('Memanfaatkan teknologi mutakhir untuk kemurnian dan hasil panen yang optimal.') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="text-gray-600">{{ __('Membangun kemitraan jangka panjang berdasarkan transparansi dan integritas.') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="text-gray-600">{{ __('Berkontribusi pada ketahanan pangan nasional melalui supply chain yang konsisten.') }}</span>
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

        <div class="grid lg:grid-cols-2 gap-6 lg:gap-16 lg:items-end mb-12 md:mb-16">
            <div data-aos="fade-up">
                <span class="text-primary font-bold text-xs uppercase tracking-widest">{{ __('Pilar Kesuksesan') }}</span>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold text-charcoal mt-3 leading-tight">
                    {{ __('Nilai yang Kami') }} <span class="text-primary italic">{{ __('Pegang Teguh') }}</span>
                </h2>
            </div>
            <p class="text-gray-600 text-base md:text-lg leading-relaxed lg:max-w-md lg:justify-self-end lg:border-l-2 lg:border-primary/20 lg:pl-6" data-aos="fade-up" data-aos-delay="100">
                {{ __('Tiga prinsip yang memandu setiap keputusan kami — dari pemilihan ladang hingga kemasan final.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            {{-- Value 1: Quality --}}
            <div class="group relative flex flex-col p-7 md:p-9 bg-surface rounded-3xl border border-gray-100 hover:bg-charcoal hover:text-white hover:-translate-y-1 transition-all duration-500" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-14 h-14 bg-white group-hover:bg-primary rounded-2xl shadow-sm flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-primary group-hover:text-white text-3xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </div>
                    <span class="font-heading text-4xl font-bold text-primary/15 group-hover:text-white/20 transition-colors">01</span>
                </div>

                <h4 class="font-heading text-xl md:text-2xl font-bold mb-3">{{ __('Kualitas Utama') }}</h4>

                <p class="text-gray-600 group-hover:text-white/80 leading-relaxed transition-colors">
                    {{ __('Kualitas bukanlah tindakan, melainkan kebiasaan. Kami menerapkan uji laboratorium ketat untuk kelembaban, kemurnian, dan integritas butiran.') }}
                </p>

                <div class="mt-auto pt-6">
                    <div class="h-1 w-12 bg-primary rounded-full group-hover:w-20 transition-all duration-500"></div>
                </div>
            </div>

            {{-- Value 2: Integrity --}}
            <div class="group relative flex flex-col p-7 md:p-9 bg-primary text-white rounded-3xl shadow-2xl shadow-primary/30 overflow-hidden lg:-translate-y-4 hover:-translate-y-1 lg:hover:-translate-y-6 transition-all duration-500" data-aos="fade-up" data-aos-delay="200">
                <div class="absolute -top-16 -right-16 w-48 h-48 bg-white/10 rounded-full"></div>

                <div class="relative flex items-start justify-between mb-6">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings: 'FILL' 1;">handshake</span>
                    </div>
                    <span class="font-heading text-4xl font-bold text-white/25">02</span>
                </div>

                <h4 class="relative font-heading text-xl md:text-2xl font-bold mb-3">{{ __('Integritas') }}</h4>

                <p class="relative text-white/90 leading-relaxed">
                    {{ __('Membangun kepercayaan dengan distributor dan retail partner melalui harga yang jujur, sertifikasi berat akurat (NIB), dan konsistensi yang tak tergoyahkan.') }}
                </p>

                <div class="relative mt-auto pt-6">
                    <div class="h-1 w-12 bg-gold rounded-full group-hover:w-20 transition-all duration-500"></div>
                </div>
            </div>

            {{-- Value 3: Innovation --}}
            <div class="group relative flex flex-col p-7 md:p-9 bg-surface rounded-3xl border border-gray-100 hover:bg-charcoal hover:text-white hover:-translate-y-1 transition-all duration-500 md:col-span-2 lg:col-span-1" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-14 h-14 bg-white group-hover:bg-primary rounded-2xl shadow-sm flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-primary group-hover:text-white text-3xl" style="font-variation-settings: 'FILL' 1;">settings_suggest</span>
                    </div>
                    <span class="font-heading text-4xl font-bold text-primary/15 group-hover:text-white/20 transition-colors">03</span>
                </div>

                <h4 class="font-heading text-xl md:text-2xl font-bold mb-3">{{ __('Inovasi Tradisional') }}</h4>

                <p class="text-gray-600 group-hover:text-white/80 leading-relaxed transition-colors">
                    {{ __('Menghormati kearifan pertanian tradisional sambil berinvestasi pada teknologi penggilingan modern untuk meminimalkan limbah dan memaksimalkan kualitas.') }}
                </p>

                <div class="mt-auto pt-6">
                    <div class="h-1 w-12 bg-primary rounded-full group-hover:w-20 transition-all duration-500"></div>
                </div>
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
                <span class="text-gold font-bold text-xs uppercase tracking-widest">{{ __('Resmi & Terpercaya') }}</span>
            </div>

            <h3 class="font-heading text-2xl md:text-4xl font-bold mb-4">
                {{ __('Terverifikasi NIB') }}
            </h3>

            <p class="text-white/80 mb-8 max-w-2xl mx-auto">
                {{ __('Lisensi resmi dari pemerintah Indonesia dengan nomor registrasi yang dapat diverifikasi publik.') }}
            </p>

            <div class="inline-block bg-white/10 backdrop-blur border border-white/20 rounded-2xl px-8 py-4">
                <div class="text-xs uppercase tracking-widest text-white/60 mb-1">{{ __('Business Registration ID') }}</div>
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
            {{ __('Siap menjadi bagian dari') }} <br>
            <span class="text-primary italic">{{ __('cerita kami selanjutnya?') }}</span>
        </h2>

        <p class="text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
            {{ __('Distributor, retailer, atau pemilik usaha kuliner — kami terbuka untuk diskusi kerjasama jangka panjang yang saling menguntungkan.') }}
        </p>

        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ localized_route('contact') }}"
               class="bg-primary text-white px-8 py-4 rounded-lg font-bold hover:bg-primary-hover transition-all shadow-lg hover:shadow-xl active:scale-95 inline-flex items-center gap-2">
                <span class="material-symbols-outlined">mail</span>
                <span>{{ __('Hubungi Kami') }}</span>
            </a>
            <a href="{{ localized_route('products') }}"
               class="border-2 border-charcoal text-charcoal px-8 py-4 rounded-lg font-bold hover:bg-charcoal hover:text-white transition-all active:scale-95 inline-flex items-center gap-2">
                <span>{{ __('Lihat Produk') }}</span>
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

@endsection
