<x-filament-widgets::widget>
    <x-filament::section heading="Lewat Jatuh Tempo">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            @foreach (['Piutang Pembeli' => $receivables, 'Utang ke Petani' => $payables] as $title => $rows)
                <div>
                    <h3 style="font-weight: 600; margin-bottom: 8px;">{{ $title }}</h3>
                    @forelse ($rows as $row)
                        <a href="{{ $row['url'] }}" style="display: flex; justify-content: space-between; gap: 8px; padding: 6px 0; border-bottom: 1px solid rgba(128,128,128,.2); font-size: 14px;">
                            <span>
                                {{ $row['number'] }} &middot; {{ $row['name'] }}<br>
                                <span style="color: #dc2626; font-size: 12px;">Jatuh tempo {{ $row['due']->translatedFormat('d M Y') }}</span>
                            </span>
                            <strong>Rp {{ number_format($row['amount'], 0, ',', '.') }}</strong>
                        </a>
                    @empty
                        <p style="font-size: 14px; opacity: .7;">Tidak ada yang lewat jatuh tempo.</p>
                    @endforelse
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
