@extends('admin.layouts.app')

@section('judul', 'Katalog Produk')

@section('konten')
    <div class="judul-halaman">
        <h1>Katalog Produk</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Tambah produk</a>
    </div>

    <x-admin::alert nada="warn">
        Nama &amp; deskripsi produk saat ini tampil di bagian <strong class="d-inline">Produk Unggulan</strong> beranda.
        Isi halaman <em>/produk</em> diterjemahkan lewat file bahasa, bukan dari menu ini.
    </x-admin::alert>

    <x-admin::search placeholder="Cari nama atau slug" />

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th class="angka">Berat/karung</th>
                    <th>Terjemahan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    <tr class="baris-link">
                        <td class="kartu-judul">
                            <a href="{{ route('admin.products.edit', $product) }}" class="stretched-link text-reset text-decoration-none">{{ $product->getTranslation('name', 'id') }}</a>
                        </td>
                        <td data-label="Slug">{{ $product->slug }}</td>
                        <td data-label="Berat" class="angka text-nowrap">@angka($product->weight_per_karung) kg</td>
                        <td data-label="Bahasa">
                            <x-admin::lang-status :fields="['name' => $product->getTranslations('name'), 'description' => $product->getTranslations('description')]" />
                        </td>
                        <td class="kartu-status"><x-admin::pill :label="$product->is_active ? 'Tampil' : 'Disembunyikan'" :warna="$product->is_active ? 'success' : 'gray'" /></td>
                        <td class="text-end text-nowrap kartu-aksi">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-garis btn-sm">Ubah</a>
                            <x-admin::delete-button :action="route('admin.products.destroy', $product)" :confirm="'Hapus produk '.$product->getTranslation('name', 'id').'?'" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada produk.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $products->links() }}
@endsection
