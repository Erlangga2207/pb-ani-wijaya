@props(['nada' => 'warn', 'judul' => null])

<div {{ $attributes->class(['peringatan', 'peringatan-'.$nada]) }} role="status">
    @if ($judul) <strong>{{ $judul }}</strong> @endif
    {{ $slot }}
</div>
