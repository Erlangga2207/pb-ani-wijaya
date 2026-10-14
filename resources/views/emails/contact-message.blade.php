<p>Pesan baru dari form kontak website.</p>
<p>
    <strong>Nama:</strong> {{ $contactMessage->name }}<br>
    <strong>Email:</strong> {{ $contactMessage->email }}<br>
    <strong>Subjek:</strong> {{ $contactMessage->subject ?: '-' }}
</p>
<p style="white-space: pre-line">{{ $contactMessage->message }}</p>
<p><a href="{{ route('admin.contact-messages.show', $contactMessage) }}">Lihat di panel admin</a></p>
