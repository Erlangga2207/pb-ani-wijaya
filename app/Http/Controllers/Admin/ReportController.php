<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Keuangan › Laporan. Angka dari ReportService yang sama dengan versi cetak & CSV (route reports.print / reports.csv).
// Staff hanya tab Stok & Rendemen (Gate view-report), tab keuangan lewat URL langsung → 403.
class ReportController extends Controller
{
    public function __invoke(Request $request, ReportService $reports): View
    {
        $tabs = array_filter(ReportService::TABS, fn ($tab) => Gate::allows('view-report', $tab), ARRAY_FILTER_USE_KEY);
        abort_if($tabs === [], 403);

        $tab = (string) $request->query('tab', array_key_first($tabs));
        Gate::authorize('view-report', $tab);

        $preset = array_key_exists($request->query('preset'), ReportService::PRESETS) ? $request->query('preset') : 'this_month';
        [$from, $to] = ReportService::period($preset, $request->query('from'), $request->query('to'));
        $period = ['preset' => $preset, 'from' => $from->toDateString(), 'to' => $to->toDateString()];

        return view('admin.reports.index', [
            'tabs' => $tabs,
            'tab' => $tab,
            'preset' => $preset,
            'from' => $from,
            'to' => $to,
            'period' => $period,
            'sections' => $reports->sections($tab, $from, $to, $request->user()->isOwnerOrAbove()),
            'printUrl' => route('reports.print', ['tab' => $tab] + $period),
            'csvUrl' => route('reports.csv', ['tab' => $tab] + $period),
        ]);
    }
}
