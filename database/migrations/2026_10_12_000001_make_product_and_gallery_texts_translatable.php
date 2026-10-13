<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// PRD-08 Fase 6: teks produk & galeri jadi JSON per bahasa (spatie/laravel-translatable).
// Nilai lama dipindah ke key "id" (bahasa default). down() mengembalikan nilai "id" ke kolom teks biasa.
return new class extends Migration
{
    private const COLUMNS = [
        'products' => ['name' => 'string', 'description' => 'text'],
        'galleries' => ['title' => 'string', 'description' => 'text'],
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            // 1) text dulu (JSON bisa lebih panjang dari varchar 255), 2) isi JSON, 3) ubah tipe ke json.
            Schema::table($table, function (Blueprint $t) use ($columns) {
                foreach (array_keys($columns) as $column) {
                    $t->text($column)->nullable()->change();
                }
            });

            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns) {
                $update = [];
                foreach (array_keys($columns) as $column) {
                    $update[$column] = $row->{$column} === null ? null : json_encode(['id' => $row->{$column}], JSON_UNESCAPED_UNICODE);
                }
                DB::table($table)->where('id', $row->id)->update($update);
            });

            Schema::table($table, function (Blueprint $t) use ($columns) {
                foreach ($columns as $column => $original) {
                    $t->json($column)->nullable($original === 'text')->change();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            Schema::table($table, function (Blueprint $t) use ($columns) {
                foreach (array_keys($columns) as $column) {
                    $t->text($column)->nullable()->change();
                }
            });

            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns) {
                $update = [];
                foreach (array_keys($columns) as $column) {
                    $translations = json_decode((string) $row->{$column}, true);
                    $update[$column] = is_array($translations)
                        ? ($translations['id'] ?? reset($translations) ?: null)
                        : $row->{$column};
                }
                DB::table($table)->where('id', $row->id)->update($update);
            });

            Schema::table($table, function (Blueprint $t) use ($columns) {
                foreach ($columns as $column => $original) {
                    $original === 'string'
                        ? $t->string($column)->nullable(false)->change()
                        : $t->text($column)->nullable()->change();
                }
            });
        }
    }
};
