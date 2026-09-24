<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Sekali jalan: data lama file_path tersimpan sebagai JSON (["gallery/x.jpg"]) -> string biasa.
class NormalizeGalleryPaths extends Command
{
    protected $signature = 'gallery:normalize-paths';

    protected $description = 'Ubah file_path galeri yang tersimpan sebagai JSON menjadi path string biasa';

    public function handle(): int
    {
        $fixed = 0;

        foreach (DB::table('galleries')->select('id', 'file_path')->get() as $row) {
            $path = $row->file_path;
            if (! is_string($path) || ! (str_starts_with($path, '[') || str_starts_with($path, '{'))) {
                continue;
            }

            $decoded = json_decode($path, true);
            $clean = is_array($decoded) ? (array_values($decoded)[0] ?? null) : null;

            if (is_string($clean) && $clean !== '') {
                DB::table('galleries')->where('id', $row->id)->update(['file_path' => $clean]);
                $fixed++;
            } else {
                $this->warn("Galeri #{$row->id}: file_path tidak bisa dibaca ({$path}), perbaiki manual.");
            }
        }

        $this->info("{$fixed} path galeri dinormalisasi.");

        return self::SUCCESS;
    }
}
