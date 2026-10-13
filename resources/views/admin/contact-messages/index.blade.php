@extends('admin.layouts.app')

@section('judul', 'Pesan & Calon Pembeli')

@section('konten')
    <div class="judul-halaman">
        <h1>Pesan &amp; Calon Pembeli</h1>
    </div>

    <x-admin::search placeholder="Cari nama, email, subjek">
        <select name="status" class="form-select" style="max-width:180px" onchange="this.form.submit()" aria-label="Status">
            <option value="">Semua status</option>
            @foreach (App\Enums\LeadStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->getLabel() }}</option>
            @endforeach
        </select>
    </x-admin::search>

    <x-admin::panel>
        <div class="table-responsive">
            <table class="table tabel-kartu">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Subjek</th>
                    <th>Email / HP</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($messages as $message)
                    <tr class="baris-link">
                        <td data-label="Tanggal" class="text-nowrap">{{ $message->created_at->translatedFormat('j M Y H:i') }}</td>
                        <td @class(['kartu-judul', 'fw-bold' => ! $message->is_read])>
                            <a href="{{ route('admin.contact-messages.show', $message) }}" class="stretched-link text-reset text-decoration-none">{{ $message->name }}</a>
                            @unless ($message->is_read) <span class="small text-ink-2 fw-normal">(belum dibaca)</span> @endunless
                        </td>
                        <td data-label="Subjek">{{ Str::limit($message->subject ?: '(tanpa subjek)', 40) }}</td>
                        <td data-label="Kontak">
                            {{ $message->email }}
                            @if ($message->phone) <div class="small text-ink-2">{{ $message->phone }}</div> @endif
                        </td>
                        <td class="kartu-status"><x-admin::pill :label="$message->status->getLabel()" :warna="$message->status->getColor()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-ink-2 py-4 kartu-kosong">Belum ada pesan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-admin::panel>

    {{ $messages->links() }}
@endsection
