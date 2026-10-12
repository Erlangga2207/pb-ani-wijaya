{{-- Satu baris barang di form penjualan. $index = angka, atau "__INDEX__" di <template>. --}}
@php
    $line += ['item_id' => '', 'input_quantity' => '', 'input_unit' => 'sack', 'price_per_kg' => ''];
    $field = fn (string $key) => "items[{$index}][{$key}]";
    $error = fn (string $key) => $errors->first("items.{$index}.{$key}");
@endphp

<div class="row g-2 align-items-end border-bottom pb-3 mb-3" data-baris>
    <div class="col-12 col-md-4">
        <label class="form-label small">Barang</label>
        <select name="{{ $field('item_id') }}" class="form-select @if ($error('item_id')) is-invalid @endif" data-barang required aria-label="Barang">
            <option value="">— pilih —</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" data-karung="{{ $item->weight_per_sack_kg }}" data-harga="{{ $item->default_price_per_kg }}"
                        @selected((string) $line['item_id'] === (string) $item->id)>
                    {{ $item->name }} — stok {{ number_format((float) $item->stock_kg, 0, ',', '.') }} kg
                </option>
            @endforeach
        </select>
        @if ($error('item_id')) <div class="invalid-feedback">{{ $error('item_id') }}</div> @endif
    </div>
    <div class="col-4 col-md-2">
        <label class="form-label small">Jumlah</label>
        <input type="number" name="{{ $field('input_quantity') }}" value="{{ $line['input_quantity'] }}" step="0.01" min="0.01"
               class="form-control @if ($error('input_quantity')) is-invalid @endif" data-jumlah required aria-label="Jumlah">
    </div>
    <div class="col-4 col-md-2">
        <label class="form-label small">Satuan</label>
        <select name="{{ $field('input_unit') }}" class="form-select" data-satuan aria-label="Satuan">
            @foreach (App\Enums\QuantityUnit::cases() as $unit)
                <option value="{{ $unit->value }}" @selected($line['input_unit'] === $unit->value)>{{ $unit->getLabel() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-4 col-md-2">
        <label class="form-label small">Harga / kg</label>
        <input type="number" name="{{ $field('price_per_kg') }}" value="{{ $line['price_per_kg'] }}" step="0.01" min="0.01"
               class="form-control @if ($error('price_per_kg')) is-invalid @endif" data-harga required aria-label="Harga per kg">
    </div>
    <div class="col-12 col-md-2 d-flex align-items-center justify-content-between gap-2">
        <div class="small">
            <span class="angka" data-kg>0</span> kg<br>
            <strong class="angka">Rp <span data-total>0</span></strong>
        </div>
        <button type="button" class="btn btn-polos btn-sm" data-hapus aria-label="Hapus baris">✕</button>
    </div>
</div>
