@extends('admin.layouts.app')

@section('judul', $buyer->exists ? 'Ubah pembeli' : 'Tambah pembeli')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $buyer->exists ? 'Ubah pembeli' : 'Tambah pembeli' }}</h1>
    </div>

    <form method="POST" style="max-width:640px"
          action="{{ $buyer->exists ? route('admin.buyers.update', $buyer) : route('admin.buyers.store') }}">
        @csrf
        @if ($buyer->exists) @method('PUT') @endif

        <x-admin::panel>
            <x-admin::field label="Nama pembeli" name="name" :value="$buyer->name" maxlength="255" wajib />
            <x-admin::field label="Tipe" name="type" wajib>
                <select id="type" name="type" @class(['form-select', 'is-invalid' => $errors->has('type')])>
                    @foreach (App\Enums\BuyerType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('type', $buyer->type?->value ?? 'retail') === $type->value)>{{ $type->getLabel() }}</option>
                    @endforeach
                </select>
            </x-admin::field>
            <div class="row">
                <x-admin::field class="col-sm-6" label="No. HP / WhatsApp" name="phone" type="tel" :value="$buyer->phone" maxlength="30" />
                <x-admin::field class="col-sm-6" label="Email" name="email" type="email" :value="$buyer->email" maxlength="255" />
            </div>
            <div class="row">
                <x-admin::field class="col-sm-4" label="Kota / Kabupaten" name="city" :value="$buyer->city" maxlength="255" />
                <x-admin::field class="col-sm-4" label="Provinsi" name="province" :value="$buyer->province" maxlength="255" />
                <x-admin::field class="col-sm-4" label="Negara" name="country" :value="$buyer->country ?? 'Indonesia'" maxlength="255" />
            </div>
            <x-admin::field label="Alamat lengkap" name="address">
                <textarea id="address" name="address" rows="2" maxlength="1000" @class(['form-control', 'is-invalid' => $errors->has('address')])>{{ old('address', $buyer->address) }}</textarea>
            </x-admin::field>
            <x-admin::field label="Catatan" name="notes">
                <textarea id="notes" name="notes" rows="2" maxlength="1000" @class(['form-control', 'is-invalid' => $errors->has('notes')])>{{ old('notes', $buyer->notes) }}</textarea>
            </x-admin::field>
            <x-admin::active-check :checked="$buyer->exists ? $buyer->is_active : true" hint="Pembeli nonaktif tidak muncul di pilihan penjualan." />
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.buyers.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection
