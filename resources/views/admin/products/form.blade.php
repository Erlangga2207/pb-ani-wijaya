@extends('admin.layouts.app')

@section('judul', $product->exists ? 'Ubah produk' : 'Tambah produk')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $product->exists ? 'Ubah produk' : 'Tambah produk' }}</h1>
    </div>

    <form method="POST" style="max-width:720px"
          action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <x-admin::panel>
            <x-admin::lang-tabs name="name" label="Nama produk" :values="$product->getTranslations('name')" />
            <x-admin::field label="Slug (URL)" name="slug" :value="$product->slug" maxlength="255" pattern="[A-Za-z0-9_-]+"
                            :hint="$product->exists ? 'Tidak diterjemahkan. Ubah hanya bila perlu — link lama ke produk ini ikut berubah.' : 'Kosongkan untuk dibuat otomatis dari nama (ID).'" />
            <x-admin::lang-tabs name="description" label="Deskripsi" type="textarea" :maxlength="5000" :values="$product->getTranslations('description')" />
            <x-admin::field label="Berat per karung (kg)" name="weight_per_karung" type="number" step="0.01" min="0.01" :value="$product->weight_per_karung" wajib />
            <x-admin::active-check :checked="$product->is_active" label="Tampil di website" />
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection
