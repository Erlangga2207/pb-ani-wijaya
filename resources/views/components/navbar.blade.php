<nav x-data="{ mobileOpen: false, scrolled: false }"
     @scroll.window="scrolled = window.scrollY > 50"
     :class="scrolled ? 'shadow-md py-3' : 'py-4'"
     class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-200 transition-all duration-300">

    <div class="max-w-7xl mx-auto px-5 md:px-12">
        <div class="flex justify-between items-center">

            {{-- Logo / Brand --}}
            <a href="{{ localized_route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ __('PB. Ani Wijaya Logo') }}"
                     class="w-14 h-14 object-contain group-hover:scale-110 transition-transform duration-300">
                <div class="hidden sm:block">
                    <div class="font-heading text-xl font-bold text-primary leading-tight">
                        {{ $setting('company_name', 'PB. Ani Wijaya') }}
                    </div>
                    <div class="text-[10px] text-gray-500 uppercase tracking-widest">
                        {{ $setting('company_tagline', 'Premium Rice Producer') }}
                    </div>
                </div>
            </a>

            {{-- Desktop Menu --}}
            <div class="hidden lg:flex items-center gap-8">
                @php
                    $menuItems = [
                        ['route' => 'home', 'label' => __('Beranda')],
                        ['route' => 'about', 'label' => __('Tentang Kami')],
                        ['route' => 'products', 'label' => __('Produk')],
                        ['route' => 'gallery', 'label' => __('Galeri')],
                        ['route' => 'contact', 'label' => __('Kontak')],
                    ];
                @endphp

                @foreach($menuItems as $item)
                    <a href="{{ localized_route($item['route']) }}"
                       class="font-medium text-sm uppercase tracking-wider transition-colors
                              {{ request()->routeIs($item['route'], 'localized.'.$item['route']) ? 'text-primary border-b-2 border-primary pb-1 font-bold' : 'text-gray-700 hover:text-primary' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach

                {{-- Pemilih bahasa: tetap di halaman yang sama --}}
                <div x-data="{ langOpen: false }" @click.outside="langOpen = false" class="relative">
                    <button @click="langOpen = !langOpen" type="button"
                            class="font-medium text-sm uppercase tracking-wider text-gray-700 hover:text-primary transition-colors inline-flex items-center gap-1"
                            aria-label="{{ __('Pilih bahasa') }}" :aria-expanded="langOpen">
                        <span class="material-symbols-outlined" style="font-size: 18px;">language</span>
                        {{ strtoupper(app()->getLocale()) }}
                    </button>
                    <div x-show="langOpen" x-transition x-cloak
                         class="absolute right-0 mt-3 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-2">
                        @foreach (config('locales') as $code => $locale)
                            <a href="{{ localized_route($basePage, [], $code) }}" hreflang="{{ $locale['hreflang'] }}" lang="{{ $locale['hreflang'] }}"
                               class="block px-4 py-2 text-sm {{ app()->getLocale() === $code ? 'text-primary font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                                {{ $locale['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ localized_route('contact') }}"
                   class="bg-primary text-white px-6 py-2.5 rounded-lg font-bold text-sm uppercase tracking-wider hover:bg-primary-hover transition-all shadow-sm hover:shadow-md active:scale-95">
                    {{ __('Inquiry') }}
                </a>
            </div>

            {{-- Mobile Toggle --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden p-2 text-primary"
                    aria-label="{{ __('Toggle menu') }}">
                <span class="material-symbols-outlined text-3xl" x-show="!mobileOpen">menu</span>
                <span class="material-symbols-outlined text-3xl" x-show="mobileOpen" x-cloak>close</span>
            </button>
        </div>

        {{-- Mobile Menu Drawer --}}
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="lg:hidden mt-4 pb-4 space-y-2 border-t border-gray-200 pt-4"
             x-cloak>
            @foreach($menuItems as $item)
                <a href="{{ localized_route($item['route']) }}"
                   class="block px-4 py-3 rounded-lg font-medium
                          {{ request()->routeIs($item['route'], 'localized.'.$item['route']) ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ localized_route('contact') }}"
               class="block bg-primary text-white text-center px-4 py-3 rounded-lg font-bold mt-3">
                {{ __('Inquiry') }}
            </a>

            {{-- Pemilih bahasa (HP) --}}
            <div class="flex flex-wrap gap-2 px-4 pt-3" aria-label="{{ __('Pilih bahasa') }}">
                @foreach (config('locales') as $code => $locale)
                    <a href="{{ localized_route($basePage, [], $code) }}" hreflang="{{ $locale['hreflang'] }}" lang="{{ $locale['hreflang'] }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium {{ app()->getLocale() === $code ? 'bg-primary/10 text-primary font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                        {{ $locale['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>

{{-- Alpine.js untuk interactivity (mobile menu, scroll detect) --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak] { display: none !important; }</style>
