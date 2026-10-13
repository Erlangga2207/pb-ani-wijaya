# Progres PRD-08 Fase 7 — Hapus Filament

Branch: `feature/prd-08-fase7-hapus-filament` (dari `main` @ `0374f08`, Fase 6 sudah di-merge) · Mulai: 2026-10-13

Legenda: ✅ selesai · 🟡 sebagian · ⬜ belum · ⛔ menunggu keputusan

## Log langkah
| Langkah | Status | Commit |
|---|---|---|
| 1. Perbandingan fitur Filament ↔ `/panel` | ⛔ | 1 celah kecil — lihat "Pertanyaan" |
| 2. Cari kode aplikasi yang menulis teks biasa ke kolom translatable | ✅ | tidak ada yang perlu diperbaiki |

## 1. Perbandingan fitur

Sumber: semua file di `app/Filament` (resource, page, widget, action, relation manager) vs `routes/admin.php`, controller & view `/panel`.
Filter `/panel` = query string di controller.

### Resource & halaman
| Filament | Fitur Filament | `/panel` | Status |
|---|---|---|---|
| `PurchaseResource` | list, create, view; filter tanggal, status, status bayar, petani, desa; cari | `pembelian` I C S; filter `from/until`, `status`, `payment_status`, `supplier`, `village`, `q` | ✅ |
| └ `PurchaseForm` `createOptionForm` (petani baru) | tambah petani dari form | field "Nama/Desa petani baru" (`new_supplier_*`) | ✅ |
| └ `ViewPurchase` aksi `receipt` | cetak bukti | tombol ke `purchases.receipt` di show | ✅ |
| `SaleResource` | list, create, view; filter tanggal, pembeli, status, status bayar; cari | `penjualan` I C S; filter `from/until`, `buyer`, `status`, `payment_status`, `q` | ✅ |
| └ `SaleForm` `createOptionForm` (pembeli baru) | tambah pembeli dari form | field "Nama/Kota pembeli baru" | ✅ |
| └ `CreateSale` aksi `print`, `ViewSale` aksi `invoice` | cetak nota | redirect ke show + tombol "Cetak nota" (`sales.invoice`) | ✅ |
| `ProductionResource` | list, create, view; filter tanggal, status | `produksi` I C S; filter `from/until`, `status`, `q` | ✅ |
| `StockMovementResource` (Kartu Stok) | list read-only; filter tanggal, arah, barang | `kartu-stok`; filter `from/until`, `direction`, `item`, `q` | ✅ |
| `StockAdjustmentResource` (Opname) | list, create, view; filter barang, status | `opname` I C S; filter `item`, `status`, `q` | ✅ |
| `ExpenseResource` | list, create, view; filter tanggal, kategori, status | `biaya` I C S; filter `from/until`, `category`, `status`, `q` | ✅ |
| `ItemResource` | list, create, view, edit; filter jenis | `barang` I C S E D; filter `type`, `q` | ✅ |
| └ `StockMovementsRelationManager` | mutasi di detail barang | tabel mutasi di `items/show` | ✅ |
| `SupplierResource` | list, create, edit; filter desa, aktif | `petani` I C E D; filter `village`, `status`, `q` | ✅ |
| `BuyerResource` | list, create, edit; filter jenis, aktif | `pembeli` I C E D; filter `type`, `status`, `q` | ✅ |
| `UserResource` | list, create, edit; filter aktif | `pengguna` I C E D; filter `status`, `q` | ✅ |
| Page `Payables` (+ aksi `open`) | utang belum lunas, buka pembelian | `utang` (link ke pembelian, modal bayar); filter `overdue`, `q` | ✅ |
| Page `Receivables` (+ aksi `remind`) | piutang, ingatkan via WA | `piutang` + tombol WA (`Sale::whatsappUrl`, teks sama) | ✅ |
| Page `Reports` | tab laporan, preset tanggal, cetak, CSV | `laporan?tab=&preset=&from=&to=` + `reports.print` / `reports.csv` | ✅ |
| Gallery / Product / ContactMessage / WebsiteSettings | — | sudah dipindah & dihapus di Fase 6 | ✅ |
| Shield `RoleResource` | kelola role | **dihapus sesuai PRD** (role dari `RoleSeeder`) | ✅ (disengaja) |

### Aksi bersama
| Filament | `/panel` | Status |
|---|---|---|
| `VoidAction` (alasan wajib) | `x-admin::void-modal` + route `*.void` (`can:void`) | ✅ |
| `RecordPaymentAction` + `PaymentsRelationManager` | `x-admin::payment-modal`, `x-admin::payments-table`, route `*.payments.store`, `payments.void` | ✅ |

### Widget dashboard
| Widget | `/panel` dashboard | Status |
|---|---|---|
| `StockStatsWidget` | tile stok per barang (kg + karung) | ✅ |
| `LowStockAlertWidget` | tile merah "di bawah minimum" | 🟡 **tanpa tombol "Catat Pembelian"** (aksi `buy`, hanya barang jenis bahan baku → form pembelian) |
| `TodayActivityWidget` | tile gabah dibeli/digiling, beras terjual hari ini | ✅ |
| `YieldTrendChart` | Chart.js `grafikRendemen` | ✅ |
| `FinanceStatsWidget` (owner+) | tile omzet, laba kotor/bersih, piutang, utang | ✅ |
| `SalesTrendChart` (owner+) | Chart.js `grafikOmzet` | ✅ |
| `OverdueWidget` (owner+) | panel "Lewat Jatuh Tempo" | ✅ |

### Fitur panel
| Filament panel | `/panel` | Status |
|---|---|---|
| `->login()` | `/panel/login` (`throttle:5,1`, regenerate session, logout POST) | ✅ |
| brand name/logo | layout admin | ✅ |
| profil / reset password / registrasi / notifikasi | tidak diaktifkan di Filament | — |

## 2. Kode yang menulis teks biasa ke kolom translatable

| Tempat | Temuan | Tindakan |
|---|---|---|
| `DB::table('galleries'/'products'/'site_settings')` di `app`, `routes`, `database/seeders`, `database/factories` | hanya `NormalizeGalleryPaths` → menulis `file_path` saja (bukan kolom translatable) | tidak perlu |
| Seeder | `SiteSettingSeeder`: key translatable di-`json_encode`; key lain teks biasa ke `site_settings.value` = kolom **text** (bukan JSON) | tidak perlu |
| | `ItemSeeder`: hanya membaca `Product::query()->value('id')` | tidak perlu |
| Factory | hanya `UserFactory` | tidak perlu |
| Command | `NormalizeGalleryPaths` (file_path), `VerifyStock` (tidak menyentuh) | tidak perlu |
| `create/update` model translatable di `app` | lewat model (`HasTranslations`) di controller/service Fase 6 | tidak perlu |

## Temuan untuk rencana penghapusan (belum dikerjakan)
Referensi Filament di luar `app/Filament`:
- `app/Enums/*` (8 enum) implement `Filament\Support\Contracts\HasLabel/HasColor` → hapus `implements`/`use`, **method `getLabel()`/`getColor()` tetap** (dipakai `x-admin::pill` & view).
- `app/Models/User.php` implements `FilamentUser` (`canAccessPanel`) → hapus interface & method.
- `bootstrap/providers.php`: `AdminPanelProvider` + `YesPanelProvider` (class **tidak pernah ada**, sisa scaffolding sejak commit pertama) → hapus keduanya.
- `bootstrap/app.php`: komentar redirect login Filament.
- `resources/views/admin/dashboard.blade.php`: fallback `route("filament.admin.resources...")` di `$docUrl` (tidak pernah terpakai lagi) → hapus fallback.
- `resources/views/filament/**` (3 view), `config/filament-shield.php`, `public/js/filament`, `public/css/filament`, script `filament:upgrade` di `composer.json`.
- Komentar yang menyebut Filament: `PurchaseController`, `SaleController`, `DashboardService`, `x-admin::pill`.
- `composer why`: `livewire/livewire` & `blade-ui-kit/blade-heroicons` hanya dibutuhkan `filament/support` → ikut terhapus.
- Test: `tests/TestCase.php` (`Filament::setCurrentPanel`) + 14 file memanggil `/admin/...`/`Livewire::test` (termasuk test service: `PurchaseServiceTest`, `SaleServiceTest`, `ProductionServiceTest`, `StockAdjustmentTest`, `ExpenseTest`) → bagian Filament diganti `/panel` dengan assertion sama, atau dihapus bila murni smoke test Filament; semua dicatat di `admin-migration-plan.md` bagian 6.

## Rencana langkah (setelah disetujui)
3. Tutup celah dashboard (bila disetujui).
4. Test: ganti/hapus bagian Filament, catat di log.
5. Lepas ketergantungan kode ke Filament (enum, User, dashboard, providers, komentar).
6. `composer remove filament/filament bezhansalleh/filament-shield` + hapus `app/Filament`, provider, config, view, asset public.
7. Migration hapus permission gaya Shield (role tetap).
8. Redirect 301 `/admin` & `/admin/*` → `/panel` + test.
9. `CLAUDE.md`: aturan admin Blade + i18n.
10. Verifikasi: `route:list`, `composer install`, `optimize:clear`, test di MySQL (DB sementara) + SQLite, grep "filament".

## Pertanyaan
1. **Tombol "Catat Pembelian" di stok menipis** belum ada di dashboard `/panel`. Usul: tambahkan link kecil "Catat pembelian →" di tile barang jenis bahan baku yang di bawah minimum (ke `admin.purchases.create`, hanya tampil bila user boleh membuat pembelian). Tambahkan, atau tidak perlu?
