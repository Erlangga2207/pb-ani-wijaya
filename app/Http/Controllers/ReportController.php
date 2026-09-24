<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Cetak & unduh CSV laporan. Akses dicek oleh Gate "view-report" di route.
class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function print(Request $request, string $tab): View
    {
        [$from, $to] = $this->period($request);

        return view('reports.print', [
            'title' => ReportService::TABS[$tab],
            'from' => $from,
            'to' => $to,
            'sections' => $this->reports->sections($tab, $from, $to, $request->user()->isOwnerOrAbove()),
        ]);
    }

    public function csv(Request $request, string $tab): StreamedResponse
    {
        [$from, $to] = $this->period($request);
        $sections = $this->reports->sections($tab, $from, $to, $request->user()->isOwnerOrAbove());
        $filename = sprintf('laporan-%s-%s-%s.csv', $tab, $from->format('Ymd'), $to->format('Ymd'));

        return response()->streamDownload(function () use ($sections, $tab, $from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 agar Excel membaca huruf dengan benar
            fputcsv($out, ['Laporan '.ReportService::TABS[$tab], $from->format('d/m/Y').' s/d '.$to->format('d/m/Y')], ';');

            foreach ($sections as $section) {
                fputcsv($out, [], ';');
                fputcsv($out, [$section['heading']], ';');
                fputcsv($out, array_column($section['columns'], 0), ';');
                foreach ($section['rows'] as $row) {
                    fputcsv($out, array_map(
                        fn ($value, $column) => ReportService::csvValue($value, $column[1]),
                        $row,
                        $section['columns'],
                    ), ';');
                }
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function period(Request $request): array
    {
        return ReportService::period($request->query('preset', 'this_month'), $request->query('from'), $request->query('to'));
    }
}
