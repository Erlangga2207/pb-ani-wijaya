<?php

namespace App\Filament\Admin\Pages;

use App\Services\ReportService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use UnitEnum;

// Keuangan › Laporan: satu halaman dengan tab. Staff hanya tab Stok & Rendemen.
class Reports extends Page
{
    protected string $view = 'filament.admin.pages.reports';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $title = 'Laporan';

    protected static ?int $navigationSort = 4;

    #[Url]
    public string $tab = '';

    #[Url]
    public string $preset = 'this_month';

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $to = null;

    public function mount(): void
    {
        if (! Gate::allows('view-report', $this->tab)) {
            $this->tab = array_key_first($this->allowedTabs());
        }
    }

    public function updatedTab(): void
    {
        abort_unless(Gate::allows('view-report', $this->tab), 403);
    }

    public function allowedTabs(): array
    {
        return array_filter(ReportService::TABS, fn ($tab) => Gate::allows('view-report', $tab), ARRAY_FILTER_USE_KEY);
    }

    protected function getViewData(): array
    {
        abort_unless(Gate::allows('view-report', $this->tab), 403);

        [$from, $to] = ReportService::period($this->preset, $this->from, $this->to);
        $query = ['preset' => $this->preset, 'from' => $from->toDateString(), 'to' => $to->toDateString()];

        return [
            'tabs' => $this->allowedTabs(),
            'presets' => ReportService::PRESETS,
            'periodLabel' => $from->translatedFormat('d M Y').' – '.$to->translatedFormat('d M Y'),
            'sections' => app(ReportService::class)->sections($this->tab, $from, $to, auth()->user()->isOwnerOrAbove()),
            'printUrl' => route('reports.print', ['tab' => $this->tab] + $query),
            'csvUrl' => route('reports.csv', ['tab' => $this->tab] + $query),
        ];
    }
}
