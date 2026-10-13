@extends('admin.layouts.app')

@section('judul', $supplier->exists ? 'Ubah petani' : 'Tambah petani')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $supplier->exists ? 'Ubah petani' : 'Tambah petani' }}</h1>
    </div>

    <form method="POST" style="max-width:640px"
          action="{{ $supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}">
        @csrf
        @if ($supplier->exists) @method('PUT') @endif

        <x-admin::panel>
            <x-admin::field label="Nama petani / pemasok" name="name" :value="$supplier->name" maxlength="255" wajib />
            <x-admin::field label="No. HP / WhatsApp" name="phone" type="tel" :value="$supplier->phone" maxlength="30" />
            <div class="row">
                <x-admin::field class="col-sm-4" label="Desa" name="village" :value="$supplier->village" maxlength="255" />
                <x-admin::field class="col-sm-4" label="Kecamatan" name="district" :value="$supplier->district" maxlength="255" />
                <x-admin::field class="col-sm-4" label="Kota / Kabupaten" name="city" :value="$supplier->city" maxlength="255" />
            </div>
            <x-admin::field label="Alamat lengkap" name="address">
                <textarea id="address" name="address" rows="2" maxlength="1000" @class(['form-control', 'is-invalid' => $errors->has('address')])>{{ old('address', $supplier->address) }}</textarea>
            </x-admin::field>
            <x-admin::field label="Catatan" name="notes">
                <textarea id="notes" name="notes" rows="2" maxlength="1000" @class(['form-control', 'is-invalid' => $errors->has('notes')])>{{ old('notes', $supplier->notes) }}</textarea>
            </x-admin::field>
            <x-admin::active-check :checked="$supplier->exists ? $supplier->is_active : true" hint="Petani nonaktif tidak muncul di pilihan pembelian." />
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection
