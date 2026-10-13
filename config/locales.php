<?php

// Bahasa website publik. Tambah bahasa baru cukup di sini (+ file lang/<kode>.json & lang/<kode>/validation.php).
// Kunci pertama = bahasa default: URL tanpa prefix (/produk). Bahasa lain memakai prefix (/en/produk).
// tab: label tab bahasa di admin. hreflang: dipakai untuk <html lang> & <link hreflang>. fonts: font Google tambahan khusus aksara CJK.

return [
    'id' => ['name' => 'Indonesia', 'tab' => 'ID', 'hreflang' => 'id', 'fonts' => null],
    'en' => ['name' => 'English', 'tab' => 'EN', 'hreflang' => 'en', 'fonts' => null],
    'zh' => ['name' => '中文', 'tab' => '中文', 'hreflang' => 'zh-Hans', 'fonts' => ['sans' => 'Noto Sans SC', 'serif' => 'Noto Serif SC']],
    'ja' => ['name' => '日本語', 'tab' => '日本語', 'hreflang' => 'ja', 'fonts' => ['sans' => 'Noto Sans JP', 'serif' => 'Noto Serif JP']],
    'ko' => ['name' => '한국어', 'tab' => '한국어', 'hreflang' => 'ko', 'fonts' => ['sans' => 'Noto Sans KR', 'serif' => 'Noto Serif KR']],
];
