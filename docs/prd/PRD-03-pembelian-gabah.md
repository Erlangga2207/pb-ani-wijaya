# PRD-03 — Pembelian Gabah & Utang ke Petani

**Branch:** `feature/prd-03-purchases` · **Bergantung pada:** PRD-02

## Tujuan
Mencatat setiap gabah yang dibeli dari petani (siapa, dari desa mana, berapa kg, harga berapa), menambah stok gabah dengan biaya yang benar, dan melacak utang pabrik ke petani.

## Data model

### `purchases`
| kolom | tipe | catatan |
|---|---|---|
| id | PK | |
| number | string unique | `PB-YYYYMMDD-0001` |
| purchase_date | date | boleh mundur, **tidak boleh** di masa depan |
| supplier_id | FK suppliers restrictOnDelete | |
| item_id | FK items | default GBH; hanya item `type=raw` |
| input_quantity | decimal(12,2) | |
| input_unit | string | `QuantityUnit` |
| quantity_kg | decimal(12,2) | dihitung server |
| moisture_percent | decimal(5,2) nullable | kadar air (opsional, untuk analisis rendemen) |
| price_per_kg | decimal(15,2) | |
| subtotal | decimal(15,2) | kg × harga |
| other_cost | decimal(15,2) default 0 | ongkos angkut/bongkar — masuk biaya gabah |
| total | decimal(15,2) | subtotal + other_cost |
| paid_amount | decimal(15,2) default 0 | cache Σ payments |
| payment_status | string | `PaymentStatus` |
| due_date | date nullable | jatuh tempo bila tidak lunas |
| notes | text nullable | |
| status | string | `DocumentStatus` |
| void_reason, voided_at, voided_by | | |
| user_id | FK users | pencatat |
| timestamps | | |
Index: `purchase_date`, `supplier_id`, `payment_status`.

### `payments` (dipakai juga oleh penjualan di PRD-05)
`id, number (BY-...), payable_type, payable_id (morphs), payment_date, amount decimal(15,2), method (PaymentMethod), notes, status (DocumentStatus), void_reason, voided_at, voided_by, user_id, timestamps`

## Aturan bisnis (`App\Services\PurchaseService`)
1. `create(array $data): Purchase` dalam satu `DB::transaction`:
   - hitung `quantity_kg` (karung × `weight_per_sack_kg` item) — **jangan percaya nilai kg dari form**, hitung ulang di server.
   - `total = subtotal + other_cost`; biaya per kg untuk stok = `total ÷ quantity_kg` (ongkos angkut ikut jadi biaya gabah).
   - nomor dari `DocumentNumberService('PB')`.
   - `StockService::increase(gabah, kg, biaya_per_kg, $purchase, date)`.
   - jika form mengisi "Bayar sekarang" (> 0) → buat payment pertama via `PaymentService`.
   - update `items.default_price_per_kg` gabah = `price_per_kg` (harga pasar terbaru).
2. `void(Purchase, reason)`: hanya owner+. Tolak jika stok gabah saat ini < kg pembelian (gabah sudah terpakai produksi) dengan pesan jelas. Void juga mem-void semua payment-nya. `StockService::reverse()`.
3. `App\Services\PaymentService::record(Model $payable, array $data)`: amount > 0 dan ≤ sisa tagihan; update `paid_amount` & `payment_status` (0 → unpaid, < total → partial, = total → paid). `void(Payment, reason)` menghitung ulang status.

## UI Filament — **Transaksi › Pembelian Gabah**
- **Form create** (satu halaman, section):
  1. Petani (select searchable + tombol *create option* untuk petani baru: nama, HP, desa) · Tanggal (default hari ini).
  2. Jumlah + Satuan (kg/karung) → tampil "= X kg" live · Kadar air (opsional).
  3. Harga/kg (prefill `default_price_per_kg` gabah) · Ongkos lain · **Total** (live, read-only).
  4. Bayar sekarang (default = total) · Metode · Jatuh tempo (wajib jika bayar < total).
- **Tidak ada halaman edit.** Halaman **View** (infolist) berisi detail + tabel pembayaran + header actions: `Catat Pembayaran` (jika belum lunas), `Cetak Bukti` (Blade print sederhana untuk petani), `Batalkan` (owner+, modal alasan wajib).
- **Tabel list**: nomor, tanggal, petani, desa, kg, harga/kg, total, status bayar (badge), status dokumen. Filter: rentang tanggal, petani, desa, status bayar. Summary di footer kolom kg & total (`Summarizer Sum`).
- Resource terpisah (read-only list) **Keuangan › Utang ke Petani**: pembelian dengan `payment_status != paid`, urut jatuh tempo, badge merah jika lewat jatuh tempo.

## Hak akses
| Aksi | staff | owner+ |
|---|---|---|
| Buat pembelian, catat pembayaran | ✅ | ✅ |
| Lihat daftar & detail | ✅ | ✅ |
| Void pembelian / pembayaran | ❌ | ✅ |

## Acceptance criteria
- [ ] Beli 40 karung (@25) × Rp7.500 + ongkos Rp100.000 → kg 1.000, total Rp7.600.000, stok gabah +1.000, biaya stok Rp7.600/kg.
- [ ] Bayar Rp5.000.000 → status Sebagian; bayar sisa → Lunas; bayar melebihi sisa → ditolak.
- [ ] Void pembelian yang gabahnya sudah digiling → ditolak dengan pesan jelas.
- [ ] Tanggal masa depan ditolak (validasi server, bukan hanya UI).

## Test
`PurchaseServiceTest` (skenario di atas), `PaymentServiceTest` (status transisi, overpay ditolak, void payment menghitung ulang), `PurchasePolicyTest` (staff tidak bisa void).

## Rencana commit
`feat(purchases): migrations and models` → `feat(payments): payment service` → `feat(purchases): purchase service` → `feat(purchases): filament resource and receipt` → `feat(finance): payables list` → `test(purchases): ...`
