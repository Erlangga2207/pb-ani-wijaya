{{-- Dipakai halaman Laporan (admin) dan versi cetak. --}}
@foreach ($sections as $section)
    <h3 style="margin: 18px 0 6px; font-size: 14px;">{{ $section['heading'] }}</h3>
    <table class="lines" style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
            <tr>
                @foreach ($section['columns'] as [$label, $type])
                    <th style="padding: 6px 4px; border-bottom: 1px solid #999; text-align: {{ in_array($type, ['text', 'date']) ? 'left' : 'right' }};">{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($section['rows'] as $row)
                @php $strong = in_array($row[0] ?? '', ['LABA KOTOR', 'LABA BERSIH', 'TOTAL'], true) || ($row[1] ?? '') === 'TOTAL'; @endphp
                <tr style="{{ $strong ? 'font-weight: bold;' : '' }}">
                    @foreach ($section['columns'] as $i => [$label, $type])
                        <td style="padding: 5px 4px; border-bottom: 1px solid #e5e5e5; text-align: {{ in_array($type, ['text', 'date']) ? 'left' : 'right' }};">{{ \App\Services\ReportService::format($row[$i] ?? null, $type) }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($section['columns']) }}" style="padding: 8px 4px; color: #888;">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
@endforeach
