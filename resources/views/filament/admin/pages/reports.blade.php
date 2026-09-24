<x-filament-panels::page>
    <x-filament::section>
        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <label style="display: grid; gap: 4px; font-size: 13px;">
                Periode
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="preset">
                        @foreach ($presets as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>

            @if ($preset === 'custom')
                <label style="display: grid; gap: 4px; font-size: 13px;">
                    Dari
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="from" />
                    </x-filament::input.wrapper>
                </label>
                <label style="display: grid; gap: 4px; font-size: 13px;">
                    Sampai
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="to" />
                    </x-filament::input.wrapper>
                </label>
            @endif

            <div style="margin-left: auto; display: flex; gap: 8px;">
                <x-filament::button tag="a" :href="$printUrl" target="_blank" icon="heroicon-o-printer" color="gray">Cetak</x-filament::button>
                <x-filament::button tag="a" :href="$csvUrl" icon="heroicon-o-arrow-down-tray">Unduh CSV</x-filament::button>
            </div>
        </div>
        <p style="margin-top: 8px; font-size: 13px; opacity: .7;">{{ $periodLabel }}</p>
    </x-filament::section>

    <x-filament::tabs>
        @foreach ($tabs as $key => $label)
            <x-filament::tabs.item :active="$tab === $key" wire:click="$set('tab', '{{ $key }}')">
                {{ $label }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <x-filament::section>
        @include('reports.sections', ['sections' => $sections])
    </x-filament::section>
</x-filament-panels::page>
