<?php

// Pesan validasi form kontak (draf, perlu dicek penutur asli). Hanya rule yang dipakai halaman publik.
return [
    'email' => ':attribute には有効なメールアドレスを入力してください。',
    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'required' => ':attribute は必須です。',
    'string' => ':attribute は文字列で入力してください。',
];
