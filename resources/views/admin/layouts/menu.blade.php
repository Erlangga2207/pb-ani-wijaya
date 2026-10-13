{{-- Menu berkelompok, dipakai sidebar desktop dan offcanvas HP. Sumber: config/admin-menu.php lewat $adminMenu. --}}
@foreach ($adminMenu as $group)
    <div @class(['admin-grup', 'admin-grup-bawah' => $group['bawah'] ?? false])>
        @if ($group['judul'])
            <div class="admin-grup-judul">{{ $group['judul'] }}</div>
        @endif
        <ul class="admin-menu">
            @foreach ($group['item'] as $item)
                @php
                    $pattern = Str::endsWith($item['route'], ['.index', '.edit']) ? Str::beforeLast($item['route'], '.').'.*' : $item['route'];
                    $active = request()->routeIs($pattern);
                @endphp
                <li>
                    <a href="{{ route($item['route']) }}" @class(['aktif' => $active]) @if ($active) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $item['ikon'] }}"/></svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endforeach
