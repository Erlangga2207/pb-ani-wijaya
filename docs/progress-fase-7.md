# Progres PRD-08 Fase 7 — Hapus Filament

Branch: `feature/prd-08-fase7-hapus-filament` (dari `main` @ `0374f08`, Fase 6 sudah di-merge) · Mulai: 2026-10-13

Legenda: ✅ selesai · 🟡 sebagian · ⬜ belum · ⛔ menunggu keputusan

## Log langkah
| Langkah | Status | Commit |
|---|---|---|
| 1. Perbandingan fitur Filament ↔ `/panel` | ✅ | `29e1822` (celah ditutup di langkah 3) |
| 2. Cari kode aplikasi yang menulis teks biasa ke kolom translatable | ✅ | tidak ada yang perlu diperbaiki |
| 3. Dashboard: link "Catat pembelian →" di tile bahan baku yang di bawah minimum (`@can('create', Purchase)`) + test | ✅ | `6e3b5ce` |
| 4. Test: semua pemakaian Filament/Livewire di test dialihkan ke `/panel` (assertion sama; label disesuaikan supaya `assertDontSee` tetap bermakna), 3 test akses duplikat dihapus, `TestCase` bebas Filament. Log di `admin-migration-plan.md` bagian 6. 171/171 hijau | ✅ | `da3b565` |
| 5. Lepas ketergantungan kode: 8 enum tanpa interface Filament (method `getLabel/getColor` tetap), `User` tanpa `FilamentUser`/`canAccessPanel`, link "Panel lama" di layout dihapus, fallback route Filament di dashboard dihapus, komentar disesuaikan. 171/171 hijau | ✅ | `609a93f` |
| 6. `composer remove filament/filament bezhansalleh/filament-shield --no-update-with-dependencies --minimal-changes` → **34 paket dihapus, 0 paket lain berubah versi** (percobaan pertama tanpa flag ikut meng-upgrade 23 paket Symfony dll. → dibatalkan dengan mengembalikan `composer.json`/`composer.lock` dari HEAD via `git show`). `livewire/livewire` & `blade-heroicons` ikut terhapus (hanya dibutuhkan Filament); `spatie/laravel-permission` tetap. Dihapus: `app/Filament`, `app/Providers/Filament`, `resources/views/filament`, `config/filament-shield.php`, `public/{js,css,fonts}/filament`, script `filament:upgrade`, provider di `bootstrap/providers.php` (termasuk `YesPanelProvider` yang tidak pernah ada). `composer validate` OK, `composer install` "Nothing to install", 171/171 hijau | ✅ | `37c0170` |
| 7. Migration `2026_10_13_000001_remove_shield_permissions`: hapus permission pola Shield (`Aksi:Model` + format lama `view_any_x`/`page_`/`widget_`) beserta pivot `role_has_permissions`/`model_has_permissions`; role tetap; `down()` sengaja kosong. Seeder tidak pernah membuat permission (DB baru: tidak ada yang dihapus). Pola dicek terhadap 97 permission asli di MySQL `pb_ani_wijaya` (hanya baca): 97/97 cocok. Test `RemoveShieldPermissionsTest` | ✅ | `87f11b3` |
| 8. `routes/web.php`: `Route::permanentRedirect` `/admin` & `/admin/{any}` → `/panel` (301). Test `LegacyAdminRedirectTest` | ✅ | `8dd634b` |
| 9. `CLAUDE.md`: stack & struktur admin Blade; aturan 3 (Filament v5) → aturan admin Blade (/panel, Form Request, Policy + `can:` di route, Bootstrap + `admin.css`, komponen `x-admin::`); aturan 4, 6, 8, 9 disesuaikan (tanpa Filament/Shield); aturan 13 baru: i18n (`config/locales.php`, `lang/*.json`, field translatable, larangan teks biasa ke kolom JSON, `lang-tabs`); perintah `shield:generate` dihapus | ✅ | `0cef5ba` |
| 10. Verifikasi akhir (lihat tabel di bawah) + 2 komentar terakhir yang menyebut "Filament" diganti, Pint `bootstrap/providers.php` | ✅ | (commit berikut) |

## Verifikasi akhir (2026-10-13)
| Cek | Hasil |
|---|---|
| `php artisan route:list` | 99 route, **0** berisi filament/livewire/shield; `/admin` & `/admin/{any}` hanya redirect |
| `grep -ri "filament" app config routes resources database` | **0** baris |
| `composer validate` | valid |
| `composer install` di folder kosong (`--dry-run`) | 113 paket, tanpa Filament/Livewire |
| `composer install` di proyek | "Nothing to install, update or remove" |
| `php artisan optimize:clear` lalu `php artisan test` — **MySQL 8.0.30** (DB sementara `pb_ani_wijaya_fase7_tmp`, dibuat & dihapus; `pb_ani_wijaya` tidak disentuh) | **173/173** hijau (bukti DB dipakai: `site_settings` berubah) |
| `migrate:fresh --seed` di MySQL sementara | sukses, 28 migration termasuk `remove_shield_permissions` |
| `php artisan test` — SQLite in-memory | **173/173** hijau |
| Pint (file PHP yang berubah di Fase 7) | lolos |

## Catatan deploy (Hostinger)
- Setelah `git pull`: `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force` (menjalankan penghapusan permission Shield), `php artisan optimize:clear`.
- Hapus folder `public/js/filament`, `public/css/filament`, `public/fonts/filament` di server bila tidak ikut terhapus oleh deploy.
- Bila `bootstrap/cache/packages.php`/`services.php` di server masih menyebut Filament: `php artisan optimize:clear` (atau hapus kedua file itu) — keduanya dibuat ulang otomatis.

## Cek manual di browser (Erlan)
Persiapan: `composer install`, `php artisan migrate`, `php artisan optimize:clear`. Login owner di `/panel`.

1. Buka `/admin`, `/admin/login`, `/admin/items` → semua langsung pindah ke `/panel` (tab Network: status **301**).
2. Topbar & menu HP `/panel`: link **"Panel lama"** sudah tidak ada.
3. Dashboard: set "Stok minimum" Gabah Ketan (Master Data › Barang › Edit) di atas stok saat ini → tile gabah merah + link **"Catat pembelian →"** membuka form pembelian. Barang jadi (beras) yang di bawah minimum **tidak** punya link itu.
4. Login **staff**: link "Catat pembelian →" tetap tampil (staff boleh mencatat pembelian), angka keuangan tidak tampil.
5. Badge status (pill) di daftar Pembelian, Penjualan, Barang, Pesan masih berwarna & berlabel Indonesia (enum tanpa Filament).
6. Alur utama tetap jalan: catat pembelian → produksi → penjualan → cetak nota → catat pembayaran → batalkan (alasan wajib).
7. Pengguna: owner membuat staff baru; owner tidak bisa membuka edit super admin.
8. Laporan: tab, cetak, CSV; login staff → hanya tab Stok & Rendemen.
9. Website publik `/`, `/en`, `/zh` tetap normal (tidak tersentuh Fase 7).
10. Browser console di `/panel` tidak ada error 404 untuk file `filament/*` atau `livewire`.

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
| `LowStockAlertWidget` (+ aksi `buy`) | tile merah "di bawah minimum" + link "Catat pembelian →" untuk bahan baku (langkah 3) | ✅ |
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

## Temuan untuk rencana penghapusan (sudah dikerjakan di langkah 4–6)
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

## Rencana langkah (disetujui 2026-10-13 — semua selesai)
3. Tutup celah dashboard (bila disetujui).
4. Test: ganti/hapus bagian Filament, catat di log.
5. Lepas ketergantungan kode ke Filament (enum, User, dashboard, providers, komentar).
6. `composer remove filament/filament bezhansalleh/filament-shield` + hapus `app/Filament`, provider, config, view, asset public.
7. Migration hapus permission gaya Shield (role tetap).
8. Redirect 301 `/admin` & `/admin/*` → `/panel` + test.
9. `CLAUDE.md`: aturan admin Blade + i18n.
10. Verifikasi: `route:list`, `composer install`, `optimize:clear`, test di MySQL (DB sementara) + SQLite, grep "filament".

## Pertanyaan (dijawab)
1. Tombol "Catat Pembelian" → **ditambahkan** (Erlan, 2026-10-13).
