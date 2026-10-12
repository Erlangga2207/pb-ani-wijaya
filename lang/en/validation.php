<?php

// Pesan validasi form kontak (draf, perlu dicek penutur asli). Hanya rule yang dipakai halaman publik.
return [
    'email' => 'The :attribute must be a valid email address.',
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be a string.',
];
