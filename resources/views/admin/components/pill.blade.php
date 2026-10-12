{{-- Badge status. warna = nama warna Filament dari enum getColor() (success/warning/danger/info/gray). --}}
@props(['label', 'warna' => 'gray'])

@php
    $class = ['success' => 'pill-hijau', 'warning' => 'pill-amber', 'danger' => 'pill-merah', 'info' => 'pill-primer'][$warna] ?? null;
@endphp

<span {{ $attributes->class(['pill', $class]) }}>{{ $label }}</span>
