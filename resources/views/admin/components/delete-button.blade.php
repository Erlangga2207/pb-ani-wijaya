{{-- Tombol hapus dengan konfirmasi browser. Hanya tampil bila user punya hak hapus berdasarkan role;
     penolakan karena data sudah dipakai dijelaskan lewat pesan setelah submit. --}}
@props(['action', 'confirm', 'label' => 'Hapus'])

<form method="POST" action="{{ $action }}" class="d-inline" onsubmit="return confirm(@js($confirm))">
    @csrf @method('DELETE')
    <button type="submit" {{ $attributes->class('btn btn-polos btn-sm') }}>{{ $label }}</button>
</form>
