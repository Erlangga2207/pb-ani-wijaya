@extends('admin.layouts.app')

@section('judul', 'Pengaturan Website')

@section('konten')
    <div class="judul-halaman">
        <h1>Pengaturan Website</h1>
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-garis">Lihat website</a>
    </div>

    {{-- Kelengkapan terjemahan semua teks yang bisa diterjemahkan --}}
    <x-admin::panel judul="Kelengkapan terjemahan">
        <x-admin::lang-status :fields="$translatable->mapWithKeys(fn ($s) => [$s->key => $s->translations()])->all()" />
        <p class="small text-ink-2 mb-0 mt-2">Teks yang belum diterjemahkan tampil dalam Bahasa Indonesia di versi bahasa tersebut.</p>
    </x-admin::panel>

    <form method="POST" action="{{ route('admin.site-settings.update') }}" style="max-width:820px">
        @csrf
        @method('PUT')

        @foreach ($groups as $group)
            <x-admin::panel :judul="$group['label']">
                @foreach ($group['settings'] as $setting)
                    @php $name = "settings[{$setting->key}]"; $dot = "settings.{$setting->key}"; @endphp
                    @if ($setting->is_translatable)
                        <x-admin::lang-tabs :name="$dot" :label="$setting->label" :values="$setting->translations()"
                                            :type="$setting->type === 'textarea' ? 'textarea' : 'text'" :maxlength="$setting->type === 'textarea' ? 5000 : 255"
                                            :hint="$setting->description" />
                    @else
                        <div class="mb-3">
                            <label for="setting-{{ $setting->key }}" class="form-label">{{ $setting->label }}</label>
                            @if ($setting->type === 'textarea')
                                <textarea id="setting-{{ $setting->key }}" name="{{ $name }}" rows="3" maxlength="5000"
                                          @class(['form-control', 'is-invalid' => $errors->has($dot)])>{{ old($dot, $setting->value) }}</textarea>
                            @else
                                <input id="setting-{{ $setting->key }}" name="{{ $name }}" value="{{ old($dot, $setting->value) }}"
                                       type="{{ ['url' => 'url', 'email' => 'email', 'phone' => 'tel'][$setting->type] ?? 'text' }}"
                                       maxlength="{{ $setting->type === 'url' ? 2000 : 255 }}"
                                       @class(['form-control', 'is-invalid' => $errors->has($dot)])>
                            @endif
                            @if ($setting->description) <div class="form-text">{{ $setting->description }}</div> @endif
                            @error($dot) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    @endif
                @endforeach
            </x-admin::panel>
        @endforeach

        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">Simpan pengaturan</button>
        </div>
    </form>
@endsection
