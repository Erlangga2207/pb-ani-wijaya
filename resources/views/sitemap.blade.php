@php echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'.PHP_EOL; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($pages as $page)
    @foreach (config('locales') as $code => $locale)
    <url>
        <loc>{{ localized_route($page, [], $code) }}</loc>
        @foreach (config('locales') as $altCode => $alt)
        <xhtml:link rel="alternate" hreflang="{{ $alt['hreflang'] }}" href="{{ localized_route($page, [], $altCode) }}"/>
        @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ localized_route($page, [], array_key_first(config('locales'))) }}"/>
    </url>
    @endforeach
@endforeach
</urlset>
