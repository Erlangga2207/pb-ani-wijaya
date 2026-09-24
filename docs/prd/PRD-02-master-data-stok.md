# PRD-02 — Master Data & Buku Besar Stok

**Branch:** `feature/prd-02-inventory-core` · **Bergantung pada:** PRD-01

## Tujuan
Memisahkan **katalog website** (`products`, tetap dipakai halaman publik) dari **barang inventori** (`items`), dan membangun buku besar stok yang bisa ditelusuri — jawaban langsung atas masalah "selisih stok" di laporan mitra.

## Kenapa `items` terpisah dari `products`?
`products` adalah konten promosi (nama, deskripsi untuk pembeli). Inventori butuh gabah, menir, dedak, sekam yang **tidak** tampil di website. Mencampur keduanya membuat halaman publik harus menyaring data internal. Relasi: `items.product_id` (nullable) untuk menghubungkan "Beras Ketan Putih" ke produk katalognya.

## Data model

### `items`
| kolom | tipe | catatan |
|---|---|---|
| id | bigint PK | |
| code | string(10) unique | `GBH`, `BKT`, `MNR`, `DDK`, `SKM` |
| name | string | |
| type | string | `ItemType` |
| product_id | FK products nullable, nullOnDelete | link ke katalog |
| weight_per_sack_kg | decimal(8,2) default 25 | konversi karung |
| stock_kg | decimal(12,2) default 0 | **cache** stok, hanya diubah `StockService` |
| avg_cost_per_kg | decimal(15,2) default 0 | biaya rata-rata bergerak (moving average) |
| default_price_per_kg | decimal(15,2) nullable | harga pasar terbaru (beli utk gabah, jual utk lainnya) — prefill form |
| min_stock_kg | decimal(12,2) nullable | batas peringatan |
| is_active | boolean default true | |
| timestamps | | |

Seeder `ItemSeeder`: GBH Gabah Ketan (raw), BKT Beras Ketan Putih (finished, link ke product pertama bila ada), MNR Menir, DDK Dedak/Bekatul, SKM Sekam (byproduct).

### `stock_movements` (append-only — tidak pernah di-update/delete)
| kolom | tipe | catatan |
|---|---|---|
| id | bigint PK | |
| item_id | FK items restrictOnDelete | |
| movement_date | date | tanggal dokumen |
| direction | string | `in` / `out` |
| quantity_kg | decimal(12,2) | selalu positif |
| unit_cost | decimal(15,2) nullable | biaya per kg saat mutasi |
| balance_after_kg | decimal(12,2) | saldo setelah mutasi → kartu stok langsung terbaca |
| source_type, source_id | morphs | Purchase / Production / Sale / StockAdjustment |
| description | string nullable | mis. "Void PJ-20260924-0003" |
| user_id | FK users nullOnDelete | |
| created_at | timestamp | |
Index: (`item_id`, `movement_date`), (`source_type`, `source_id`).

### `stock_adjustments` (Stock Opname)
`id, number (SO-...), adjustment_date, item_id, system_kg, physical_kg, difference_kg, reason (text, wajib), status (DocumentStatus), void_reason, voided_at, voided_by, user_id, timestamps`

### Perubahan master lain
- `suppliers` (= petani): tambah `village` (desa), `district` (kecamatan), `city` nullable. Label menu → **Petani / Pemasok**.
- `buyers`: tambah `city`, `province`, `country` (default `Indonesia`), `email` nullable. Ganti kolom enum MySQL `type` jadi string + PHP enum `BuyerType` (retail/wholesale/distributor/export).
- `products` **tidak diubah** (dipakai frontend).

## `App\Services\StockService`
```php
public function increase(Item $item, float $kg, ?float $unitCost, Model $source, CarbonInterface $date, ?string $desc = null): StockMovement
public function decrease(Item $item, float $kg, Model $source, CarbonInterface $date, ?string $desc = null): StockMovement
public function reverse(Model $source, string $reason): void   // untuk void: buat mutasi kebalikan semua movement milik $source
```
Aturan:
1. Selalu dipanggil **di dalam** `DB::transaction` milik service pemanggil; di awal ambil ulang item dengan `Item::lockForUpdate()->find()`.
2. `decrease` melempar `InsufficientStockException` (pesan: "Stok {nama} tidak cukup. Tersedia {x} kg, diminta {y} kg") jika `stock_kg < kg`.
3. `increase` dengan `unitCost` memperbarui `avg_cost_per_kg`:
   `avg_baru = (stok_lama × avg_lama + kg × unitCost) ÷ (stok_lama + kg)` (jika stok_lama ≤ 0 → avg_baru = unitCost).
4. `decrease` mencatat `unit_cost = avg_cost_per_kg` saat itu (dipakai untuk HPP penjualan).
5. `reverse` memakai `unit_cost` dari movement asli. Keterbatasan yang diterima: avg cost tidak dihitung ulang mundur (cukup untuk skala UMKM, dicatat di README).
6. Artisan command `stock:verify` → bandingkan `items.stock_kg` dengan `Σ in − Σ out` di ledger, tampilkan selisih (untuk jaga integritas cache).

## UI Filament
- **Master Data › Barang** (`ItemResource`): list (kode, nama, tipe badge, stok kg + ≈karung, min stok, status stok badge). Form tanpa field `stock_kg` & `avg_cost_per_kg` (read-only di view). Kolom `avg_cost_per_kg` hanya untuk owner+. Tidak ada delete jika sudah punya movement.
- **Stok › Kartu Stok** (`StockMovementResource`, read-only, tanpa create/edit/delete): filter item, rentang tanggal, arah. Kolom sumber dokumen berupa link ke dokumennya.
- **Stok › Stock Opname** (`StockAdjustmentResource`): form pilih item → tampilkan `system_kg` (read-only, ambil dari item) → input `physical_kg` → selisih otomatis → alasan wajib. Simpan lewat `StockAdjustmentService` (increase/decrease sesuai tanda selisih, unit_cost = avg saat itu). **Hanya owner+** yang boleh membuat & void opname.
- Relation manager "Riwayat Stok" di halaman view Item.

## Migrasi data lama
- **Jangan drop `stock_transactions` di PRD ini.** Sembunyikan `StockTransactionResource` dari navigasi (`shouldRegisterNavigation = false`) dan jadikan read-only.
- Drop tabel + resource + `NotaController` lama dilakukan di akhir PRD-05, setelah dikonfirmasi Erlan.

## Acceptance criteria
- [ ] 5 item ter-seed; menu Barang, Kartu Stok, Stock Opname tampil sesuai role.
- [ ] Opname dengan fisik < sistem menghasilkan movement `out`, stok item = fisik.
- [ ] `decrease` melebihi stok → exception, tidak ada data tersimpan (transaksi rollback).
- [ ] `stock:verify` melaporkan 0 selisih setelah skenario test.
- [ ] Staff tidak melihat biaya rata-rata.

## Test
- `StockServiceTest`: increase/decrease/saldo, avg cost moving average (contoh angka: 1000 kg @7.000 + 500 kg @8.000 → avg 7.333,33), exception stok kurang, reverse mengembalikan saldo.
- `StockAdjustmentTest`: selisih plus & minus, alasan wajib, staff 403.

## Rencana commit
`feat(inventory): items table and seeder` → `feat(inventory): stock ledger and StockService` → `feat(inventory): stock opname` → `feat(master): enrich suppliers and buyers` → `feat(inventory): filament resources` → `feat(inventory): stock:verify command` → `test(inventory): ...`
