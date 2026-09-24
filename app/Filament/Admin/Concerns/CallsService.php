<?php

namespace App\Filament\Admin\Concerns;

use App\Exceptions\InsufficientStockException;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

// Untuk halaman Create: tampilkan error dari service (stok kurang, aturan bisnis) sebagai notifikasi.
trait CallsService
{
    protected function callService(Closure $callback): mixed
    {
        try {
            return $callback();
        } catch (InsufficientStockException|ValidationException $e) {
            $message = $e instanceof ValidationException
                ? collect($e->errors())->flatten()->first()
                : $e->getMessage();

            Notification::make()->danger()->title('Gagal menyimpan')->body($message)->persistent()->send();
            $this->halt();
        }
    }
}
