<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\StockTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockOverview extends BaseWidget
{
    protected ?string $heading = 'Ringkasan Stok';

    protected function getStats(): array
    {
        $stats = [];

        // Total nilai semua transaksi bulan ini
        $totalIn = StockTransaction::where('type', 'in')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity_kg');

        $totalOut = StockTransaction::where('type', 'out')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity_kg');

        $stats[] = Stat::make('Barang Masuk Bulan Ini', number_format($totalIn, 2) . ' kg')
            ->description('Total stok masuk pada ' . now()->translatedFormat('F Y'))
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success');

        $stats[] = Stat::make('Barang Keluar Bulan Ini', number_format($totalOut, 2) . ' kg')
            ->description('Total stok keluar pada ' . now()->translatedFormat('F Y'))
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger');

        // Stok per produk
        $products = Product::where('is_active', true)->get();
        foreach ($products as $product) {
            $stockKg = $product->current_stock_kg;
            $stockKarung = $product->current_stock_karung;

            $color = match (true) {
                $stockKg <= 0 => 'danger',
                $stockKg < 500 => 'warning',
                default => 'success',
            };

            $statusText = match (true) {
                $stockKg <= 0 => '⚠️ Stok habis',
                $stockKg < 500 => '⚠️ Stok menipis',
                default => '✓ Stok aman',
            };

            $stats[] = Stat::make($product->name, number_format($stockKg, 2) . ' kg')
                ->description($statusText . ' (≈ ' . number_format($stockKarung, 1) . ' karung)')
                ->descriptionIcon('heroicon-m-cube')
                ->color($color);
        }

        return $stats;
    }
}
