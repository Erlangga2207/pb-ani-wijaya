{{-- Info pembatalan, tampil hanya bila dokumen sudah di-void. --}}
@props(['document'])

@if ($document->isVoid())
    <div class="peringatan peringatan-bad">
        <strong>Dokumen ini dibatalkan</strong>
        {{ $document->void_reason }}
        <div class="small text-ink-2 mt-1">
            oleh {{ $document->voidedBy?->name ?? '-' }} · {{ $document->voided_at?->translatedFormat('j M Y H:i') }}
        </div>
    </div>
@endif
