{{-- Indikator kelengkapan terjemahan, mis. "EN ✓ 中文 ✗".
     $fields = [field => [locale => teks]]. Suatu bahasa lengkap bila semua field yang terisi di ID juga terisi di bahasa itu. --}}
@props(['fields'])

@php
    $locales = config('locales');
    $default = array_key_first($locales);
@endphp

<span {{ $attributes->class('d-inline-flex flex-wrap gap-1') }}>
    @foreach (array_slice($locales, 1, null, true) as $code => $locale)
        @php
            $complete = collect($fields)->every(fn ($t) => blank($t[$default] ?? null) || filled($t[$code] ?? null));
        @endphp
        <span @class(['badge-bahasa', 'lengkap' => $complete]) title="{{ $locale['name'] }}: {{ $complete ? 'lengkap' : 'belum diterjemahkan' }}">
            {{ $locale['tab'] }} {{ $complete ? '✓' : '✗' }}
        </span>
    @endforeach
</span>
