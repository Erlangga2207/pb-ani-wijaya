@extends('admin.layouts.app')

@section('judul', 'Galeri')

@section('konten')
    <div class="judul-halaman">
        <h1>Galeri</h1>
        <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">Tambah foto/video</a>
    </div>

    <x-admin::search placeholder="Cari judul">
        <select name="category" class="form-select" style="max-width:180px" onchange="this.form.submit()" aria-label="Kategori">
            <option value="">Semua kategori</option>
            @foreach (App\Models\Gallery::CATEGORIES as $value => $label)
                <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-admin::status-filter />
        <div class="form-check align-self-center">
            <input type="checkbox" id="featured" name="featured" value="1" class="form-check-input" @checked(request()->boolean('featured')) onchange="this.form.submit()">
            <label for="featured" class="form-check-label">Unggulan saja</label>
        </div>
    </x-admin::search>

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th></th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th class="angka">Urutan</th>
                    <th>Terjemahan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($galleries as $gallery)
                    <tr class="baris-link">
                        <td data-label="">
                            @if ($gallery->file_type === 'video')
                                <span class="thumb d-inline-flex align-items-center justify-content-center small text-ink-2">Video</span>
                            @else
                                <img src="{{ asset('storage/'.$gallery->file_path) }}" alt="" class="thumb" loading="lazy">
                            @endif
                        </td>
                        <td class="kartu-judul">
                            <a href="{{ route('admin.galleries.edit', $gallery) }}" class="stretched-link text-reset text-decoration-none">{{ $gallery->getTranslation('title', 'id') }}</a>
                            @if ($gallery->is_featured) <span class="small text-ink-2 fw-normal">★ unggulan</span> @endif
                        </td>
                        <td data-label="Kategori">{{ App\Models\Gallery::CATEGORIES[$gallery->category] ?? $gallery->category }}</td>
                        <td data-label="Urutan" class="angka">{{ $gallery->order }}</td>
                        <td data-label="Bahasa">
                            <x-admin::lang-status :fields="['title' => $gallery->getTranslations('title'), 'description' => $gallery->getTranslations('description')]" />
                        </td>
                        <td class="kartu-status"><x-admin::pill :label="$gallery->is_active ? 'Tampil' : 'Disembunyikan'" :warna="$gallery->is_active ? 'success' : 'gray'" /></td>
                        <td class="text-end text-nowrap kartu-aksi">
                            <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-garis btn-sm">Ubah</a>
                            <x-admin::delete-button :action="route('admin.galleries.destroy', $gallery)" :confirm="'Hapus '.$gallery->getTranslation('title', 'id').' dari galeri? File-nya ikut terhapus.'" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada foto/video.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $galleries->links() }}
@endsection
