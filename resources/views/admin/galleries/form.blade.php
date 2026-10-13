@extends('admin.layouts.app')

@section('judul', $gallery->exists ? 'Ubah galeri' : 'Tambah ke galeri')

@section('konten')
    <div class="judul-halaman">
        <h1>{{ $gallery->exists ? 'Ubah galeri' : 'Tambah ke galeri' }}</h1>
    </div>

    <form method="POST" enctype="multipart/form-data" style="max-width:720px"
          action="{{ $gallery->exists ? route('admin.galleries.update', $gallery) : route('admin.galleries.store') }}">
        @csrf
        @if ($gallery->exists) @method('PUT') @endif

        <x-admin::panel judul="Informasi">
            <x-admin::lang-tabs name="title" label="Judul" :values="$gallery->getTranslations('title')" />
            <x-admin::lang-tabs name="description" label="Deskripsi / keterangan" type="textarea" :rows="3" :maxlength="1000" :values="$gallery->getTranslations('description')" />
            <x-admin::field label="Kategori" name="category" wajib>
                <select id="category" name="category" @class(['form-select', 'is-invalid' => $errors->has('category')])>
                    @foreach (App\Models\Gallery::CATEGORIES as $value => $label)
                        <option value="{{ $value }}" @selected(old('category', $gallery->category) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </x-admin::field>
        </x-admin::panel>

        <x-admin::panel judul="File">
            @if ($gallery->exists)
                <div class="mb-3">
                    @if ($gallery->file_type === 'video')
                        <video src="{{ asset('storage/'.$gallery->file_path) }}" controls preload="metadata" style="max-width:100%;max-height:240px" class="rounded"></video>
                    @else
                        <img src="{{ asset('storage/'.$gallery->file_path) }}" alt="" style="max-width:100%;max-height:240px" class="rounded">
                    @endif
                </div>
            @endif
            <x-admin::field label="{{ $gallery->exists ? 'Ganti file' : 'File' }}" name="file" type="file" accept=".jpg,.jpeg,.png,.webp,.mp4" :wajib="! $gallery->exists"
                            hint="Foto JPG/PNG/WEBP maks. 5 MB, atau video MP4 maks. 50 MB." />
        </x-admin::panel>

        <x-admin::panel judul="Pengaturan">
            <x-admin::field label="Urutan" name="order" type="number" min="0" step="1" :value="$gallery->order" hint="Angka kecil tampil duluan." />
            <div class="form-check mb-3">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" class="form-check-input" @checked(old('is_featured', $gallery->is_featured))>
                <label for="is_featured" class="form-check-label">Foto unggulan</label>
                <div class="form-text">Tampil di bagian galeri beranda (maks. 6 foto).</div>
            </div>
            <x-admin::active-check :checked="$gallery->is_active" label="Tampil di website" />
        </x-admin::panel>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-polos">Batal</a>
        </div>
    </form>
@endsection
