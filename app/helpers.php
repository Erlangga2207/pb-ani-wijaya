<?php

if (! function_exists('localized_route')) {
    /**
     * URL halaman publik sesuai bahasa: bahasa default tanpa prefix (/produk), lainnya /en/produk.
     * $name = nama route dasar (home, about, products, gallery, contact).
     */
    function localized_route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale === array_key_first(config('locales')) || ! array_key_exists($locale, config('locales'))) {
            return route($name, $parameters);
        }

        return route('localized.'.$name, ['locale' => $locale] + $parameters);
    }
}
