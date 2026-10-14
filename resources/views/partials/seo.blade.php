{{-- Open Graph, Twitter card & JSON-LD halaman publik. Judul & deskripsi sama dengan <title> / meta description. --}}
@php
    $companyName = $setting('company_name', 'PB. Ani Wijaya');
    // ponytail: satu gambar untuk semua halaman; idealnya JPG 1200x630 khusus share.
    $ogImage = asset('images/foto_pabrik.webp');
@endphp
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $companyName }}">
<meta property="og:title" content="@yield('title', 'PB. Ani Wijaya') - {{ $companyName }}">
<meta property="og:description" content="@yield('description', $setting('seo_meta_description', ''))">
<meta property="og:url" content="{{ localized_route($basePage) }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1320">
<meta property="og:image:height" content="1020">
<meta property="og:image:alt" content="{{ $companyName }}">
<meta property="og:locale" content="{{ config('locales.'.app()->getLocale().'.og') }}">
@foreach (config('locales') as $code => $locale)
    @if ($code !== app()->getLocale())
        <meta property="og:locale:alternate" content="{{ $locale['og'] }}">
    @endif
@endforeach
<meta name="twitter:card" content="summary_large_image">

@if ($basePage === 'home')
    @php
        $address = (string) $setting('contact_address', '');
        $phone = $setting('contact_phone');
        // Alamat bisnis = kantor di Subang (contact_address); pabrik di Indramayu sengaja tidak dicantumkan.
        // Baris pertama setting = jalan/desa/kecamatan, kode pos diambil dari angka 5 digit.
        $business = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $companyName,
            'description' => $setting('seo_meta_description'),
            'url' => localized_route('home'),
            'logo' => asset('images/logo.png'),
            'image' => $ogImage,
            'telephone' => $phone,
            'email' => $setting('contact_email'),
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => trim(preg_split('/\R/', $address)[0]),
                'addressLocality' => 'Subang',
                'addressRegion' => 'Jawa Barat',
                'postalCode' => preg_match('/\b\d{5}\b/', $address, $match) ? $match[0] : null,
                'addressCountry' => 'ID',
            ]),
            'sameAs' => array_values(array_filter([$setting('social_instagram'), $setting('social_facebook'), $setting('social_tiktok')])),
        ]);
    @endphp
    <script type="application/ld+json">@json($business)</script>
@endif
