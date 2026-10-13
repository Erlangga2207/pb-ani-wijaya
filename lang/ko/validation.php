<?php

// Pesan validasi form kontak (draf, perlu dicek penutur asli). Hanya rule yang dipakai halaman publik.
return [
    'email' => ':attribute 항목은 올바른 이메일 주소여야 합니다.',
    'max' => [
        'string' => ':attribute 항목은 :max자를 넘을 수 없습니다.',
    ],
    'required' => ':attribute 항목은 필수입니다.',
    'string' => ':attribute 항목은 문자열이어야 합니다.',
];
