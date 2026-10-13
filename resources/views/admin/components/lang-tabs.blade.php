{{-- Field yang bisa diterjemahkan: tab ID | EN | 中文 | 日本語 | 한국어. ID wajib, bahasa lain boleh kosong (tampil versi ID).
     $name memakai notasi titik (mis. settings.hero_title) → input settings[hero_title][id], settings[hero_title][en], ... --}}
@props(['name', 'label', 'values' => [], 'type' => 'text', 'maxlength' => 255, 'rows' => 4, 'hint' => null])

@php
    $locales = config('locales');
    $default = array_key_first($locales);
    $parts = explode('.', $name);
    $inputName = array_shift($parts).implode('', array_map(fn ($p) => "[{$p}]", $parts));
    $uid = 'lt-'.Str::slug($name).'-'.Str::random(4);
    $hasError = fn ($code) => $errors->has("{$name}.{$code}");
    $firstError = collect(array_keys($locales))->first($hasError);
    $active = $firstError ?? $default;
@endphp

<div {{ $attributes->merge(['class' => 'mb-3']) }}>
    <div class="form-label">
        {{ $label }}
        <span class="text-ink-2 fw-normal small">(ID wajib, bahasa lain boleh kosong → tampil versi ID)</span>
    </div>

    <ul class="nav nav-tabs tab-bahasa" role="tablist">
        @foreach ($locales as $code => $locale)
            @php $filled = filled(old("{$name}.{$code}", $values[$code] ?? null)); @endphp
            <li class="nav-item" role="presentation">
                <button type="button" role="tab" id="{{ $uid }}-{{ $code }}-tab"
                        @class(['nav-link', 'active' => $code === $active, 'text-danger' => $hasError($code)])
                        data-bs-toggle="tab" data-bs-target="#{{ $uid }}-{{ $code }}"
                        aria-controls="{{ $uid }}-{{ $code }}" aria-selected="{{ $code === $active ? 'true' : 'false' }}">
                    {{ $locale['tab'] }}
                    @if ($filled)
                        <span class="titik-terisi" aria-label="terisi">●</span>
                    @endif
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content border border-top-0 rounded-bottom p-2 bg-white">
        @foreach ($locales as $code => $locale)
            <div @class(['tab-pane', 'show active' => $code === $active]) id="{{ $uid }}-{{ $code }}" role="tabpanel" aria-labelledby="{{ $uid }}-{{ $code }}-tab">
                @if ($type === 'textarea')
                    <textarea name="{{ $inputName }}[{{ $code }}]" rows="{{ $rows }}" maxlength="{{ $maxlength }}" lang="{{ $locale['hreflang'] }}"
                              aria-label="{{ $label }} ({{ $locale['tab'] }})"
                              @class(['form-control', 'is-invalid' => $hasError($code)]) @if ($code === $default) required @endif>{{ old("{$name}.{$code}", $values[$code] ?? '') }}</textarea>
                @else
                    <input type="text" name="{{ $inputName }}[{{ $code }}]" value="{{ old("{$name}.{$code}", $values[$code] ?? '') }}" maxlength="{{ $maxlength }}"
                           lang="{{ $locale['hreflang'] }}" aria-label="{{ $label }} ({{ $locale['tab'] }})"
                           @class(['form-control', 'is-invalid' => $hasError($code)]) @if ($code === $default) required @endif>
                @endif
                @error("{$name}.{$code}")
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>

    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
