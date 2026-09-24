# PRD-05 — Penjualan, Nota & Piutang

**Branch:** `feature/prd-05-sales` · **Bergantung pada:** PRD-02, PRD-03 (tabel `payments`)

## Tujuan
Mencatat penjualan beras ketan & hasil samping (per karung atau eceran), mencetak nota bernomor otomatis, menghitung HPP terjual untuk laba rugi, dan melacak piutang pembeli.

## Data model

### `sales`
| kolom | tipe | catatan |
|---|---|---|
| id | PK | |
| number | string unique | `PJ-YYYYMMDD-0001` |
| sale_date | date | tidak boleh masa depan |
| buyer_id | FK buyers restrictOnDelete | |
| subtotal | decimal(15,2) | Σ item |
| discount | decimal(15,2) default 0 | nominal |
| shipping_cost | decimal(15,2) default 0 | ongkir ditagihkan ke pembeli (bukan omzet barang) |
| total | decimal(15,2) | subtotal − discount + shipping_cost |
| cogs_total | decimal(15,2) | Σ HPP item (hanya owner+ yang melihat) |
| paid_amount, payment_status, due_date | | sama seperti purchases |
| notes | text nullable | |
| status, void_reason, voided_at, voided_by, user_id, timestamps | | |

### `sale_items`
`id, sale_id (FK cascadeOnDelete), item_id (FK items, type finished|byproduct), input_quantity, input_unit, quantity_kg, price_per_kg, line_total, cogs_per_kg`

## Aturan bisnis (`App\Services\SaleService`)
1. Minimal 1 item; item tidak boleh dobel dalam satu nota; `price_per_kg` > 0. Gabah (`raw`) tidak bisa dijual lewat sini.
2. Harga **mengikuti pasar** dan sama untuk semua pembeli (sesuai wawancara) → prefill dari `items.default_price_per_kg`, boleh diubah per transaksi. Setelah simpan, update `default_price_per_kg` item dengan harga terakhir.
3. Dalam satu `DB::transaction`: hitung kg di server, `StockService::decrease()` per item, simpan `cogs_per_kg` = `unit_cost` dari movement, hitung `cogs_total`, nomor `PJ`, payment awal opsional via `PaymentService`.
4. `discount` ≤ subtotal. Jika bayar < total → `due_date` wajib.
5. `void(Sale, reason)`: owner+, void semua payment, `StockService::reverse()`.

## Nota
- Ganti `NotaController` lama → `SaleInvoiceController@show(Sale $sale)`, route `/nota/{sale}` (name `sales.invoice`) dengan middleware `auth` + `can:view,sale` (policy, bukan cek role manual).
- Isi nota: logo & identitas dari `SiteSetting` (company_name, contact_address, contact_phone, company_nib), nomor, tanggal, pembeli, tabel item (jumlah input + satuan, kg, harga/kg, total), diskon, ongkir, total, dibayar, sisa, status. **Tanpa HPP.**
- Tampilan print-friendly (`@media print`), ukuran A5/struk; tombol "Cetak" (window.print) & "Kirim via WhatsApp" (link `wa.me/{hp_pembeli}?text=` berisi ringkasan nota + nomor).
- Nota void tampil watermark "DIBATALKAN".
- Boleh memakai ulang gaya `resources/views/nota/show.blade.php` lama sebagai dasar.

## UI Filament — **Transaksi › Penjualan**
- **Form create**:
  1. Pembeli (select searchable + create option: nama, HP, kota, tipe) · Tanggal.
  2. **Repeater** item: Barang (hanya yang stoknya > 0, label "Beras Ketan Putih — stok 1.250 kg"), Jumlah, Satuan, Harga/kg, Total baris (live). `->distinct()` pada item.
  3. Diskon · Ongkir · **Total** (live).
  4. Bayar sekarang (default = total) · Metode · Jatuh tempo.
- Setelah create → redirect ke View dengan notifikasi + tombol "Cetak Nota".
- **View**: detail, item, pembayaran; actions: `Cetak Nota`, `Catat Pembayaran`, `Batalkan` (owner+). Kolom HPP & laba kotor per nota hanya owner+.
- **List**: nomor, tanggal, pembeli, kota, total kg, total Rp, status bayar, status. Filter tanggal, pembeli, status bayar. Footer Σ total.
- **Keuangan › Piutang Pembeli**: read-only, sisa tagihan, jatuh tempo, badge lewat tempo, tombol WA pengingat.

## Pembersihan tabel lama (langkah terakhir PRD ini)
**Tanya Erlan dulu.** Jika disetujui: hapus `StockTransactionResource`, model, policy, `NotaController`, view nota lama, dan buat migration drop `stock_transactions`. Hapus widget `StockOverview` lama (diganti di PRD-06).

## Acceptance criteria
- [ ] Stok beras 560 kg @Rp13.571,43. Jual 10 karung (250 kg) @Rp20.000 + menir 50 kg @Rp8.000, diskon Rp50.000 → subtotal Rp5.400.000, total Rp5.350.000, cogs_total Rp3.392.857,50 (menir HPP 0), stok beras 310 kg.
- [ ] Jual melebihi stok → ditolak, tidak ada data tersimpan.
- [ ] Nota bisa dibuka pembuat & owner; user lain tanpa izin → 403; tamu → redirect login.
- [ ] Staff tidak melihat HPP di mana pun (list, view, nota).

## Test
`SaleServiceTest` (skenario angka, stok kurang, diskon > subtotal ditolak, void mengembalikan stok), `SaleInvoiceTest` (akses nota), `SalePolicyTest`.

## Rencana commit
`feat(sales): migrations and models` → `feat(sales): sale service with cogs` → `feat(sales): filament resource` → `feat(sales): printable invoice and whatsapp share` → `feat(finance): receivables list` → `test(sales): ...` → `chore: remove legacy stock_transactions` (setelah konfirmasi)
