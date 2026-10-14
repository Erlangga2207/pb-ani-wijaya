<?php

// Bahasa website publik. Tambah bahasa baru cukup di sini (+ file lang/<kode>.json & lang/<kode>/validation.php).
// Kunci pertama = bahasa default: URL tanpa prefix (/produk). Bahasa lain memakai prefix (/en/produk).
// tab: label tab bahasa di admin. hreflang: dipakai untuk <html lang> & <link hreflang>. og: locale Open Graph. fonts: font Google tambahan khusus aksara CJK.

return [
    'id' => ['name' => 'Indonesia', 'tab' => 'ID', 'hreflang' => 'id', 'og' => 'id_ID', 'fonts' => null],
    'en' => ['name' => 'English', 'tab' => 'EN', 'hreflang' => 'en', 'og' => 'en_US', 'fonts' => null],
    'zh' => ['name' => '中文', 'tab' => '中文', 'hreflang' => 'zh-Hans', 'og' => 'zh_CN', 'fonts' => ['sans' => 'Noto Sans SC', 'serif' => 'Noto Serif SC']],
    'ja' => ['name' => '日本語', 'tab' => '日本語', 'hreflang' => 'ja', 'og' => 'ja_JP', 'fonts' => ['sans' => 'Noto Sans JP', 'serif' => 'Noto Serif JP']],
    'ko' => ['name' => '한국어', 'tab' => '한국어', 'hreflang' => 'ko', 'og' => 'ko_KR', 'fonts' => ['sans' => 'Noto Sans KR', 'serif' => 'Noto Serif KR']],
];
