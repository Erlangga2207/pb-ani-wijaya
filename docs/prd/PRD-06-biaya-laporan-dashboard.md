# PRD-06 — Biaya Operasional, Laporan & Dashboard

**Branch:** `feature/prd-06-reports` · **Bergantung pada:** PRD-03, 04, 05

## Tujuan
Pemilik bisa melihat omzet dan **laba rugi kapan saja** (bukan tunggu 6 bulan), memantau rendemen, stok, serta utang/piutang dari satu dashboard.

## 1. Biaya Operasional

### `expenses`
`id, number (BO-...), expense_date, category (string, enum ExpenseCategory), description, amount decimal(15,2), method (PaymentMethod), status (DocumentStatus), void_reason, voided_at, voided_by, user_id, timestamps`

`ExpenseCategory`: `wages` (Gaji/Upah), `electricity` (Listrik), `fuel` (Solar/BBM), `machine_maintenance` (Perawatan Mesin), `transport` (Transportasi), `packaging` (Karung & Kemasan), `other` (Lainnya).

UI **Keuangan › Biaya Operasional**: hanya owner+. Create + view + void (tanpa edit/delete). Filter tanggal & kategori, footer Σ.

## 2. `App\Services\ReportService`
Semua method menerima `CarbonInterface $from, $to`, hanya menghitung dokumen `status = posted`, query agregat di DB (`SUM`, `GROUP BY`) — **bukan** loop model di PHP.

| Method | Output |
|---|---|
| `profitAndLoss()` | Penjualan barang (Σ subtotal − discount), HPP (Σ cogs_total), **Laba kotor**, biaya per kategori, total biaya, **Laba bersih**, margin % |
| `salesSummary()` | omzet per hari/bulan, per item (kg & Rp), per pembeli (top 10), per kota |
| `purchaseSummary()` | gabah kg & Rp per petani dan **per desa**, harga rata-rata/kg per bulan |
| `yieldSummary()` | per batch + rata-rata tertimbang rendemen, Σ susut, Σ downtime mesin |
| `stockSummary()` | saldo per item per tanggal `to` (dari ledger), nilai persediaan (owner+) |
| `agingSummary()` | piutang & utang: belum jatuh tempo / 1–30 / 31–60 / > 60 hari |

Catatan: ongkir penjualan **tidak** masuk omzet barang (ditampilkan terpisah).

## 3. Halaman Laporan (Filament custom Page)
**Keuangan › Laporan** — satu page dengan tab: *Laba Rugi · Penjualan · Pembelian · Rendemen · Stok · Utang/Piutang*.
- Filter periode di atas: preset (Hari ini, Minggu ini, Bulan ini, Bulan lalu, **Musim ini (6 bln)**, Tahun ini, Custom).
- Tab Laba Rugi, nilai persediaan, dan HPP: **owner+ saja**. Staff hanya boleh tab Stok & Rendemen (tanpa biaya).
- Tiap tab: tombol **Cetak** (Blade print-friendly, route dengan policy) dan **Unduh CSV** (`response()->streamDownload`, tanpa package tambahan).

## 4. Dashboard (ganti widget lama)
Widget dengan `canView()` sesuai role:

| Widget | Isi | Role |
|---|---|---|
| `StockStatsWidget` | stok Gabah, Beras (kg + ≈karung), menir/dedak/sekam; warna merah jika ≤ `min_stock_kg` | semua |
| `LowStockAlertWidget` | daftar item di bawah minimum + tombol "Catat Pembelian" | semua |
| `TodayActivityWidget` | hari ini: gabah dibeli, digiling, beras terjual (kg) | semua |
| `FinanceStatsWidget` | bulan ini: omzet, laba kotor, laba bersih, piutang total, utang total | owner+ |
| `YieldTrendChart` | line chart rendemen per batch 30 hari + garis 50% & 60% | semua |
| `SalesTrendChart` | bar chart omzet 12 bulan terakhir | owner+ |
| `OverdueWidget` | piutang & utang lewat jatuh tempo (5 teratas) | owner+ |

Query widget memakai agregat; tidak boleh ada N+1 (cek dengan Debugbar/`DB::listen` saat develop).

## Acceptance criteria
- [ ] Menggunakan data skenario PRD-03→05 + biaya listrik Rp500.000: Laba kotor = 5.350.000 − 3.392.857,50 = **Rp1.957.142,50**; laba bersih **Rp1.457.142,50**. (Diskon sudah mengurangi penjualan.)
- [ ] Dokumen void tidak ikut terhitung di laporan mana pun.
- [ ] Staff login: tidak ada angka rupiah keuangan di dashboard & laporan.
- [ ] CSV terbuka rapi di Excel (separator `;`, header Bahasa Indonesia, BOM UTF-8).

## Test
`ReportServiceTest` (laba rugi angka di atas, void dikecualikan, rata-rata tertimbang rendemen, aging bucket), `ExpenseTest`, `ReportAccessTest` (staff 403 untuk tab keuangan & export).

## Rencana commit
`feat(expenses): operational expenses` → `feat(reports): report service` → `feat(reports): report page, print and csv` → `feat(dashboard): role-aware widgets` → `test(reports): ...`
