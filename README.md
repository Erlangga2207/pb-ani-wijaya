# PB. Ani Wijaya

Website company profile + sistem internal pabrik penggilingan beras ketan PB. Ani Wijaya (Losarang, Indramayu).
Laravel 13 · Filament v5 (panel `/admin`) · Filament Shield / spatie-permission.

Rencana & keputusan desain ada di [`docs/prd/`](docs/prd). Aturan kerja untuk Claude Code ada di [`CLAUDE.md`](CLAUDE.md).

## Setup

```bash
composer install
cp .env.example .env         # isi DB_*, ADMIN_EMAIL, ADMIN_PASSWORD
php artisan key:generate
php artisan migrate --seed   # role, site settings, 5 barang, super admin dari .env
php artisan storage:link
php artisan test
```

`ADMIN_EMAIL` / `ADMIN_PASSWORD` kosong = super admin tidak dibuat (password tidak pernah di-hardcode).

## Alur bisnis

```
Pembelian gabah (PB) ─▶ Produksi / giling (PR) ─▶ Penjualan (PJ)
        │ +gabah          -gabah, +beras/menir/dedak/sekam   │ -beras/menir
        └──────────────▶ stock_movements (buku besar stok) ◀─┘ ◀── Stock opname (SO)
```

- Semua perubahan stok lewat `App\Services\StockService` (transaksi DB + `lockForUpdate`, stok tidak bisa minus).
- Dokumen transaksi **tidak bisa diedit/dihapus**. Koreksi = **Batalkan (void)** dengan alasan, yang membuat mutasi balik.
- Nomor dokumen otomatis `PREFIX-YYYYMMDD-0001` (`PB`, `PR`, `PJ`, `SO`, `BY` pembayaran, `BO` biaya).

## Rumus

| | |
|---|---|
| Biaya gabah / kg | (subtotal + ongkos angkut) ÷ kg, lalu **moving average**: `(stok × avg + kg × biaya) ÷ (stok + kg)` |
| Rendemen | beras ÷ gabah × 100% (normal 50–60%, ubah di `config/production.php`) |
| Susut | gabah − Σ semua hasil giling |
| HPP beras / kg | total biaya gabah ÷ kg beras. Hasil samping HPP = 0 (metode *by-product*) |
| Laba kotor | (subtotal − diskon) − HPP terjual. Ongkir tidak termasuk omzet |
| Laba bersih | laba kotor − biaya operasional |

**Keterbatasan yang diterima:** saat dokumen di-void, biaya rata-rata tidak dihitung ulang mundur; mutasi balik memakai biaya dari mutasi aslinya. Nilai persediaan di laporan memakai biaya rata-rata saat ini. Cukup untuk skala UMKM.

## Peran

| Role | Akses |
|---|---|
| `super_admin` | Semua, termasuk menu Roles |
| `owner` | Semua data, laporan keuangan, void, kelola staff. Tidak bisa menyentuh akun super_admin |
| `staff` | Input pembelian, produksi, penjualan, pembayaran; lihat stok & rendemen. Tanpa HPP/laba/biaya, laporan keuangan, pengguna, menu Website |

Policy ditulis manual berbasis role di `app/Policies` (`config/filament-shield.php` → `policies.generate = false`), jadi `shield:generate` tidak akan menimpanya.

## Perintah berguna

```bash
php artisan stock:verify              # cocokkan cache stok item dengan buku besar
php artisan gallery:normalize-paths   # sekali jalan: bersihkan file_path galeri lama yang berbentuk JSON
./vendor/bin/pint                     # format kode
```
