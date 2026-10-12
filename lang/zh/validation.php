<?php

// Pesan validasi form kontak (draf, perlu dicek penutur asli). Hanya rule yang dipakai halaman publik.
return [
    'email' => ':attribute 必须是有效的电子邮箱地址。',
    'max' => [
        'string' => ':attribute 不能超过 :max 个字符。',
    ],
    'required' => ':attribute 为必填项。',
    'string' => ':attribute 必须是文本。',
];
