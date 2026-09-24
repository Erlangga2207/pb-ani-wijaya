# PRD-04 — Produksi (Penggilingan) & Rendemen

**Branch:** `feature/prd-04-production` · **Bergantung pada:** PRD-02 (PRD-03 disarankan agar ada stok gabah)

## Tujuan
Mencatat setiap batch penggilingan: gabah yang masuk mesin dan semua hasilnya (beras ketan, menir, dedak, sekam). Sistem menghitung **rendemen otomatis** dan **HPP beras** — fitur yang secara langsung diminta pemilik.

## Data model

### `productions`
| kolom | tipe | catatan |
|---|---|---|
| id | PK | |
| number | string unique | `PR-YYYYMMDD-0001` |
| production_date | date | tidak boleh masa depan |
| input_item_id | FK items | hanya `type=raw` (gabah) |
| input_kg | decimal(12,2) | |
| input_cost_per_kg | decimal(15,2) | snapshot avg cost gabah saat produksi |
| input_total_cost | decimal(15,2) | |
| main_output_kg | decimal(12,2) | cache kg beras (item `finished`) |
| yield_percent | decimal(5,2) | **rendemen** = main_output_kg ÷ input_kg × 100 |
| shrinkage_kg | decimal(12,2) | susut = input − Σ output |
| machine_downtime_minutes | unsigned int nullable | log mesin macet |
| notes | text nullable | |
| status, void_reason, voided_at, voided_by, user_id, timestamps | | |

### `production_outputs`
`id, production_id (FK cascadeOnDelete), item_id (FK items, type finished|byproduct), quantity_kg decimal(12,2), unit_cost decimal(15,2)`
Unique (`production_id`, `item_id`).

## Rumus & aturan (`App\Services\ProductionService`)
1. Validasi:
   - Tepat **satu** output bertipe `finished` (beras) dengan kg > 0; byproduct opsional, kg ≥ 0 (baris 0 tidak disimpan).
   - `Σ output_kg ≤ input_kg` (hasil tidak boleh lebih berat dari gabah).
   - Stok gabah cukup (ditangani `StockService`).
2. Dalam satu `DB::transaction`:
   - `input_cost_per_kg` = `avg_cost_per_kg` gabah (setelah `lockForUpdate`).
   - `StockService::decrease(gabah, input_kg, ...)`.
   - HPP beras/kg = `input_total_cost ÷ beras_kg` → `StockService::increase(beras, beras_kg, hpp, ...)`.
   - Byproduct: `StockService::increase(item, kg, unitCost: 0, ...)` — **metode by-product**: seluruh biaya gabah dibebankan ke beras; penjualan hasil samping tetap tercatat sebagai omzet. Tulis alasan ini sebagai komentar singkat di service.
   - Simpan `yield_percent` (round 2), `shrinkage_kg`.
3. **Peringatan rendemen** (bukan blokir): jika `yield_percent` < 50 atau > 60 (angka dari wawancara pemilik), tampilkan notifikasi warning saat simpan & badge kuning di tabel. Batas ini disimpan sebagai konstanta/config `production.yield_min` & `yield_max` supaya mudah diubah.
4. `void(Production, reason)`: owner+. Tolak jika stok salah satu output saat ini < kg output tersebut (sudah terjual). `StockService::reverse()`.

## UI Filament — **Transaksi › Produksi / Giling**
- **Form create**:
  1. Tanggal · Gabah (default GBH) · Input kg (tampilkan "Stok gabah tersedia: X kg" di helper text).
  2. **Hasil giling** — field tetap (bukan repeater) untuk setiap item aktif `finished` & `byproduct`: Beras Ketan (wajib), Menir, Dedak, Sekam. Lebih cepat untuk operator dibanding repeater.
  3. Panel ringkasan live: **Rendemen X%** (warna hijau 50–60, kuning di luar), Susut Y kg.
  4. Downtime mesin (menit) · Catatan.
- **Tidak ada edit.** Halaman View: detail, tabel output (kolom unit_cost hanya owner+), action `Batalkan` (owner+).
- **Tabel list**: nomor, tanggal, gabah kg, beras kg, **rendemen %** (badge warna), susut kg, downtime, status. Filter tanggal. Footer: Σ gabah, Σ beras, **rata-rata tertimbang rendemen** (`Σ beras ÷ Σ gabah`, bukan rata-rata persen biasa).

## Hak akses
Staff: create & view (tanpa kolom biaya). Owner+: semua + void.

## Acceptance criteria
- [ ] Stok gabah 1.000 kg @Rp7.600. Giling 1.000 kg → beras 560, menir 60, dedak 80, sekam 200 → rendemen **56,00%**, susut **100 kg**, HPP beras **Rp13.571,43/kg**, stok gabah 0, stok beras +560.
- [ ] Output total > input → ditolak. Tanpa beras → ditolak. Gabah kurang → ditolak.
- [ ] Rendemen 45% → tetap tersimpan, muncul warning.
- [ ] Void produksi yang berasnya sudah terjual sebagian → ditolak.

## Test
`ProductionServiceTest` (skenario angka di atas, validasi, warning flag, void), `ProductionPolicyTest`.

## Rencana commit
`feat(production): migrations and models` → `feat(production): production service with yield and cost` → `feat(production): filament resource` → `test(production): ...`
