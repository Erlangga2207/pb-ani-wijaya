@props(['judul' => null, 'tautan' => null])

<section {{ $attributes->class('kotak panel') }}>
    @if ($judul || $tautan || isset($aksi))
        <header class="panel-header">
            <h2>{{ $judul }}</h2>
            @isset($aksi)
                {{ $aksi }}
            @elseif ($tautan)
                <a href="{{ $tautan }}" class="small">Lihat semua</a>
            @endisset
        </header>
    @endif
    <div class="panel-isi">
        {{ $slot }}
    </div>
</section>
