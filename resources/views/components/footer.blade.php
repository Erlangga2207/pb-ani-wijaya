<footer class="bg-charcoal text-white mt-24">
    <div class="max-w-7xl mx-auto px-5 md:px-12 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

            {{-- Brand Column --}}
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 bg-white rounded-full p-1 flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}"
                             alt="{{ __('PB. Ani Wijaya Logo') }}"
                             class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h3 class="font-heading text-2xl font-bold leading-tight">{{ $setting('company_name', 'PB. Ani Wijaya') }}</h3>
                        <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">{{ $setting('company_tagline', 'Premium Rice Producer') }}</p>
                    </div>
                </div>
                <p class="text-gray-400 max-w-md leading-relaxed">
                    {{ __(':tagline dari :address, melayani distribusi grosir dan retail dengan komitmen kualitas terjamin.', ['tagline' => $setting('company_tagline', 'Premium Rice Producer'), 'address' => $setting('contact_short_address', 'Indramayu, Jawa Barat')]) }}
                </p>

                {{-- Trust Badges --}}
                <div class="flex flex-wrap gap-3 pt-4">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full text-xs">
                        <span class="material-symbols-outlined text-gold" style="font-size: 16px; font-variation-settings: 'FILL' 1;">verified</span>
                        <span>NIB. {{ $setting('company_nib', '1412210030165') }}</span>
                    </div>
                    <div class="inline-flex items-center gap-2 bg-gold/20 px-4 py-2 rounded-full text-xs text-gold">
                        <span class="material-symbols-outlined" style="font-size: 16px; font-variation-settings: 'FILL' 1;">workspace_premium</span>
                        <span class="font-bold">{{ __('Mutu Terjamin') }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="space-y-4">
                <h4 class="font-bold text-sm uppercase tracking-widest text-gold">{{ __('Navigasi') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ localized_route('home') }}" class="text-gray-300 hover:text-white transition-colors">{{ __('Beranda') }}</a></li>
                    <li><a href="{{ localized_route('about') }}" class="text-gray-300 hover:text-white transition-colors">{{ __('Tentang Kami') }}</a></li>
                    <li><a href="{{ localized_route('products') }}" class="text-gray-300 hover:text-white transition-colors">{{ __('Produk') }}</a></li>
                    <li><a href="{{ localized_route('gallery') }}" class="text-gray-300 hover:text-white transition-colors">{{ __('Galeri') }}</a></li>
                    <li><a href="{{ localized_route('contact') }}" class="text-gray-300 hover:text-white transition-colors">{{ __('Kontak') }}</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="space-y-4">
                <h4 class="font-bold text-sm uppercase tracking-widest text-gold">{{ __('Hubungi Kami') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5" style="font-size: 18px;">location_on</span>
                        <span class="text-gray-300 whitespace-pre-line">{{ $setting('contact_address', 'Indramayu, Jawa Barat') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary" style="font-size: 18px;">call</span>
                        <a href="tel:{{ $setting('contact_phone', '') }}" class="text-gray-300 hover:text-white transition-colors">
                            {{ $setting('contact_phone', '+62 812-3456-7890') }}
                        </a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary" style="font-size: 18px;">mail</span>
                        <a href="mailto:{{ $setting('contact_email', '') }}" class="text-gray-300 hover:text-white transition-colors break-all">
                            {{ $setting('contact_email', 'info@pbaniwijaya.com') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-white/10 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} {{ $setting('company_name', 'PB. Ani Wijaya') }}. {{ __('All Rights Reserved.') }}</p>
            <p class="text-xs">{{ __('Mutu Terjamin') }} — {{ $setting('contact_short_address', 'Indramayu') }}</p>
        </div>
    </div>
</footer>
