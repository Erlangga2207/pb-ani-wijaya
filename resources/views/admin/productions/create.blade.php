@extends('admin.layouts.app')

@section('judul', 'Catat produksi')

@section('konten')
    <div class="judul-halaman">
        <h1>Catat produksi / giling</h1>
    </div>

    <form method="POST" action="{{ route('admin.productions.store') }}" id="formProduksi" style="max-width:720px"
          data-min="{{ config('production.yield_min') }}" data-max="{{ config('production.yield_max') }}">
        @csrf

        <x-admin::panel judul="1. Gabah yang digiling">
            <div class="row">
                <x-admin::field class="col-sm-4" label="Tanggal" name="production_date" type="date" :value="today()->toDateString()" :max="today()->toDateString()" wajib />
                <x-admin::field class="col-sm-4" label="Gabah" name="input_item_id" wajib>
                    <select id="input_item_id" name="input_item_id" class="form-select">
                        @foreach ($inputs as $input)
                            <option value="{{ $input->id }}" data-stok="{{ $input->stock_kg }}" @selected((string) old('input_item_id') === (string) $input->id)>{{ $input->name }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
                <x-admin::field class="col-sm-4" label="Berat gabah (kg)" name="input_kg" type="number" step="0.01" min="0.01" wajib />
            </div>
            <div class="small text-ink-2">Stok gabah tersedia: <strong class="angka" id="stokGabah">-</strong> kg</div>
        </x-admin::panel>

        {{-- Field tetap per barang (bukan baris dinamis) supaya operator cepat mengisi. --}}
        <x-admin::panel judul="2. Hasil giling (kg)">
            <div class="row">
                @foreach ($outputs as $output)
                    @php $isMain = $output->type === App\Enums\ItemType::Finished; @endphp
                    <x-admin::field class="col-6 col-sm-3" :label="$output->name" :name="'outputs['.$output->id.']'" type="number" step="0.01"
                                    :min="$isMain ? '0.01' : '0'" :value="old('outputs.'.$output->id)" :data-utama="$isMain ? '1' : '0'" :wajib="$isMain" />
                @endforeach
            </div>
            @error('outputs') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </x-admin::panel>

        <x-admin::panel judul="3. Ringkasan">
            <div class="d-flex flex-wrap gap-3">
                <span class="pill" id="pillRendemen">Rendemen: <span class="angka ms-1" id="rendemen">0</span>%</span>
                <span class="pill" id="pillSusut">Susut: <span class="angka ms-1" id="susut">0</span> kg</span>
            </div>
        </x-admin::panel>

        <x-admin::panel judul="4. Lain-lain">
            <x-admin::field label="Mesin macet (menit)" name="machine_downtime_minutes" type="number" step="1" min="0" />
            <x-admin::field label="Catatan" name="notes">
                <textarea id="notes" name="notes" rows="2" maxlength="1000" class="form-control">{{ old('notes') }}</textarea>
            </x-admin::field>
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan produksi</button>
            <a href="{{ route('admin.productions.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Pratinjau saja; rendemen & susut final dihitung di ProductionService.
        (() => {
            const f = document.getElementById('formProduksi');
            const angka = (v) => parseFloat(v) || 0;
            const format = (v) => v.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const hasil = [...f.querySelectorAll('input[name^="outputs["]')];

            function hitung() {
                const gabah = angka(f.input_kg.value);
                const beras = hasil.filter(i => i.dataset.utama === '1').reduce((s, i) => s + angka(i.value), 0);
                const semua = hasil.reduce((s, i) => s + angka(i.value), 0);
                const rendemen = gabah > 0 ? beras / gabah * 100 : 0;
                const susut = gabah - semua;

                document.getElementById('stokGabah').textContent = format(angka(f.input_item_id.selectedOptions[0]?.dataset.stok));
                document.getElementById('rendemen').textContent = format(rendemen);
                document.getElementById('susut').textContent = format(susut);
                const normal = rendemen >= angka(f.dataset.min) && rendemen <= angka(f.dataset.max);
                document.getElementById('pillRendemen').className = 'pill ' + (gabah > 0 ? (normal ? 'pill-hijau' : 'pill-amber') : '');
                document.getElementById('pillSusut').className = 'pill ' + (susut < 0 ? 'pill-merah' : '');
            }

            f.addEventListener('input', hitung);
            f.addEventListener('change', hitung);
            hitung();
        })();
    </script>
@endpush
