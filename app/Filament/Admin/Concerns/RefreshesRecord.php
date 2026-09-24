<?php

namespace App\Filament\Admin\Concerns;

use Livewire\Attributes\On;

// Untuk halaman View dokumen: muat ulang data setelah pembayaran / void.
trait RefreshesRecord
{
    #[On('document-updated')]
    public function refreshRecord(): void
    {
        $this->record->refresh();
    }
}
