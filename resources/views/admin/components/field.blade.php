{{-- Label + input + pesan error. Isi slot untuk memakai select/textarea sendiri. --}}
@props(['label', 'name', 'hint' => null, 'type' => 'text', 'value' => null, 'wajib' => false])

<div {{ $attributes->only('class')->merge(['class' => 'mb-3']) }}>
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @unless ($wajib)
            <span class="text-ink-2 fw-normal small">(boleh kosong)</span>
        @endunless
    </label>

    @if ($slot->isEmpty())
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
               value="{{ $type === 'password' ? '' : old($name, $value) }}"
               {{ $attributes->except('class')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
               @if ($wajib) required @endif>
    @else
        {{ $slot }}
    @endif

    @if ($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
