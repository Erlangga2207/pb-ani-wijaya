@extends('admin.layouts.app')

@section('judul', 'Pesan dari '.$message->name)

@section('konten')
    <div class="judul-halaman">
        <div>
            <a href="{{ route('admin.contact-messages.index') }}" class="small">← Pesan &amp; calon pembeli</a>
            <h1>{{ $message->name }}</h1>
            <div class="text-ink-2">{{ $message->created_at->translatedFormat('j F Y H:i') }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if ($message->whatsappUrl())
                <a href="{{ $message->whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-success">Balas via WhatsApp</a>
            @endif
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'Pesan Anda')) }}" class="btn btn-garis">Balas via email</a>
            @if (! $message->buyer_id && $message->status !== App\Enums\LeadStatus::Spam)
                <form method="POST" action="{{ route('admin.contact-messages.convert', $message) }}"
                      onsubmit="return confirm(@js('Buat data pembeli baru dari nama, HP, email, dan negara pada pesan ini?'))">
                    @csrf
                    <button type="submit" class="btn btn-primary">Jadikan pembeli</button>
                </form>
            @endif
            @if ($message->status !== App\Enums\LeadStatus::Spam)
                <form method="POST" action="{{ route('admin.contact-messages.spam', $message) }}" onsubmit="return confirm(@js('Tandai pesan ini sebagai spam?'))">
                    @csrf
                    <button type="submit" class="btn btn-polos">Tandai spam</button>
                </form>
            @endif
        </div>
    </div>

    @if ($message->buyer)
        <x-admin::alert nada="ok" judul="Sudah jadi pembeli">
            Ditautkan ke <a href="{{ route('admin.buyers.edit', $message->buyer) }}">{{ $message->buyer->name }}</a>.
        </x-admin::alert>
    @endif

    <x-admin::panel judul="Pesan dari pengunjung">
        <dl class="rincian mb-3">
            <div><dt>Email</dt><dd>{{ $message->email }}</dd></div>
            <div><dt>Subjek</dt><dd>{{ $message->subject ?: '(tanpa subjek)' }}</dd></div>
            <div><dt>Status</dt><dd><x-admin::pill :label="$message->status->getLabel()" :warna="$message->status->getColor()" /></dd></div>
            <div><dt>Ditangani oleh</dt><dd>{{ $message->handledBy?->name ?? '-' }}</dd></div>
        </dl>
        <p class="mb-0" style="white-space:pre-line">{{ $message->message }}</p>
    </x-admin::panel>

    <form method="POST" action="{{ route('admin.contact-messages.update', $message) }}" style="max-width:720px">
        @csrf
        @method('PUT')
        <x-admin::panel judul="Tindak lanjut">
            <div class="row">
                <x-admin::field class="col-sm-6" label="Status" name="status" wajib>
                    <select id="status" name="status" class="form-select">
                        @foreach (App\Enums\LeadStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $message->status->value) === $status->value)>{{ $status->getLabel() }}</option>
                        @endforeach
                    </select>
                </x-admin::field>
                <x-admin::field class="col-sm-6" label="No. HP / WhatsApp" name="phone" type="tel" :value="$message->phone" maxlength="30" />
            </div>
            <div class="row">
                <x-admin::field class="col-sm-6" label="Perusahaan" name="company" :value="$message->company" maxlength="255" />
                <x-admin::field class="col-sm-6" label="Negara" name="country" :value="$message->country" maxlength="255" />
            </div>
            <x-admin::field label="Catatan internal" name="admin_notes" hint="Tidak terlihat oleh pengirim.">
                <textarea id="admin_notes" name="admin_notes" rows="3" maxlength="2000" class="form-control">{{ old('admin_notes', $message->admin_notes) }}</textarea>
            </x-admin::field>
        </x-admin::panel>
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button type="submit" class="btn btn-primary">Simpan tindak lanjut</button>
        </div>
    </form>

    <x-admin::delete-button :action="route('admin.contact-messages.destroy', $message)" confirm="Hapus pesan ini secara permanen?" label="Hapus pesan" />
@endsection
