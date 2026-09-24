<?php

// Rentang rendemen normal (beras ÷ gabah × 100%) dari wawancara pemilik.
// Di luar rentang ini produksi tetap tersimpan, hanya diberi peringatan.
return [
    'yield_min' => (float) env('PRODUCTION_YIELD_MIN', 50),
    'yield_max' => (float) env('PRODUCTION_YIELD_MAX', 60),
];
