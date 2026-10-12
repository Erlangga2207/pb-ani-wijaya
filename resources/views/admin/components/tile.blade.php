@props(['label', 'nilai', 'sub' => null, 'nada' => null])

<div {{ $attributes->class(['kotak tile', 'tile-'.$nada => $nada]) }}>
    <div class="tile-label">{{ $label }}</div>
    <div class="tile-nilai font-display angka">{{ $nilai }}</div>
    @if ($sub) <div class="tile-sub">{{ $sub }}</div> @endif
</div>
