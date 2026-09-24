<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\DocumentStatus;
use App\Enums\PaymentStatus;
use App\Services\ReportService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FinanceStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Keuangan Bulan Ini';

    public static function canView(): bool
    {
        return auth()->user()->isOwnerOrAbove();
    }

    protected function getStats(): array
    {
        $pnl = app(ReportService::class)->profitAndLoss(today()->startOfMonth(), today());
        $outstanding = fn (string $table) => (float) DB::table($table)
            ->where('status', DocumentStatus::Posted->value)
            ->where('payment_status', '!=', PaymentStatus::Paid->value)
            ->sum(DB::raw('total - paid_amount'));

        $rp = fn ($v) => 'Rp '.number_format($v, 0, ',', '.');

        return [
            Stat::make('Omzet', $rp($pnl['revenue'])),
            Stat::make('Laba Kotor', $rp($pnl['gross_profit'])),
            Stat::make('Laba Bersih', $rp($pnl['net_profit']))->color($pnl['net_profit'] < 0 ? 'danger' : 'success'),
            Stat::make('Piutang Pembeli', $rp($outstanding('sales')))->color('warning'),
            Stat::make('Utang ke Petani', $rp($outstanding('purchases')))->color('warning'),
        ];
    }
}
