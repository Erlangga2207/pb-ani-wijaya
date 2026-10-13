@extends('admin.layouts.app')

@section('judul', 'Catat biaya operasional')

@section('konten')
    <div class="judul-halaman">
        <h1>Catat biaya operasional</h1>
    </div>

    <form method="POST" action="{{ route('admin.expenses.store') }}" style="max-width:560px">
        @csrf

        <x-admin::panel>
            <div class="row">
                <x-admin::field class="col-sm-6" label="Tanggal" name="expense_date" type="date" :value="today()->toDateString()" :max="today()->toDateString()" wajib />
                <x-admin::field class="col-sm-6" label="Kategori" name="category" wajib>
                    <select id="category" name="category" @class(['form-select', 'is-invalid' => $errors->has('category')])>
                        @foreach (App\Enums\ExpenseCategory::cases() as $category)
                            <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->getLabel() }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
            </div>
            <x-admin::field label="Keterangan" name="description" maxlength="255" placeholder="mis. Solar mesin giling 20 liter" wajib />
            <div class="row">
                <x-admin::field class="col-sm-6" label="Jumlah (Rp)" name="amount" type="number" step="0.01" min="1" wajib />
                <x-admin::field class="col-sm-6" label="Metode" name="method" wajib>
                    <select id="method" name="method" class="form-select">
                        @foreach (App\Enums\PaymentMethod::cases() as $method)
                            <option value="{{ $method->value }}" @selected(old('method', 'cash') === $method->value)>{{ $method->getLabel() }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
            </div>
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection
