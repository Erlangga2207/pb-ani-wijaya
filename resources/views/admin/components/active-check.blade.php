{{-- Checkbox "Aktif" yang tetap terkirim (0) saat tidak dicentang. --}}
@props(['checked' => true, 'label' => 'Aktif', 'hint' => null, 'disabled' => false])

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
           @checked(old('is_active', $checked)) @disabled($disabled)>
    <label for="is_active" class="form-check-label">{{ $label }}</label>
    @if ($hint) <div class="form-text">{{ $hint }}</div> @endif
</div>
