<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Nomor dokumen berikutnya, mis. PB-20260924-0001.
     * Urutan per prefix per hari; lockForUpdate mencegah nomor dobel saat input bersamaan.
     */
    public function next(string $prefix, CarbonInterface $date): string
    {
        $period = $date->format('Ymd');

        return DB::transaction(function () use ($prefix, $period) {
            $row = DB::table('document_sequences')
                ->where('prefix', $prefix)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if ($row) {
                $number = $row->last_number + 1;
                DB::table('document_sequences')->where('id', $row->id)
                    ->update(['last_number' => $number, 'updated_at' => now()]);
            } else {
                $number = 1;
                DB::table('document_sequences')->insert([
                    'prefix' => $prefix,
                    'period' => $period,
                    'last_number' => $number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return sprintf('%s-%s-%04d', $prefix, $period, $number);
        });
    }
}
