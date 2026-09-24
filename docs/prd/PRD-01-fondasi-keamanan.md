# PRD-01 — Fondasi & Keamanan

**Branch:** `feature/prd-01-foundation` · **Bergantung pada:** — · **Menjadi dasar untuk:** semua PRD lain

## Tujuan
Membuat panel aman untuk dideploy (bisa login di produksi, tidak ada privilege escalation), role sesuai bisnis (owner/staff), dan menyiapkan komponen bersama (enum, penomoran dokumen, branding).

## Scope
### 1. Akses panel di produksi
- `App\Models\User` implement `Filament\Models\Contracts\FilamentUser`.
- Tambah kolom `users.is_active` (boolean, default true).
- `canAccessPanel(Panel $panel)`: `true` hanya jika `is_active` dan punya salah satu role `super_admin|owner|staff`.

### 2. Role
- Role final: `super_admin`, `owner`, `staff`. Role `admin` lama **di-rename** jadi `owner` lewat migration data (update `roles.name`), lalu ganti semua `hasRole(['super_admin','admin'])` di kode → `['super_admin','owner']`. Cari dengan grep, jangan ada yang tertinggal (termasuk `routes/web.php` middleware nota dan `EditStockTransaction`).
- Buat `database/seeders/RoleSeeder.php` (idempotent, `firstOrCreate`).
- `DatabaseSeeder` memanggil: `RoleSeeder`, `SiteSettingSeeder`, lalu membuat 1 user `super_admin` dari env `ADMIN_EMAIL` / `ADMIN_PASSWORD` (jika env kosong, lewati — **jangan hardcode password**). Hapus `Test User`.
- Menu **Roles** bawaan Shield hanya untuk `super_admin` (lewat `RolePolicy`) — kalau owner bisa mengubah permission, semua pembatasan staff tidak berarti.
- Tambah helper di `User`: `isOwnerOrAbove(): bool` (super_admin atau owner). Dipakai untuk menyembunyikan data keuangan dari staff.

### 3. Menu Pengguna (UserResource)
- Role select jadi **single select** (bukan multiple). Simpan via `syncRoles()` di `CreateUser`/`EditUser` (`afterCreate`/`afterSave`), bukan relationship multiple.
- Opsi role yang tampil: `super_admin` hanya bisa dipilih oleh `super_admin`. Owner hanya bisa memilih `owner`/`staff`.
- Owner **tidak bisa** melihat tombol edit/delete pada user ber-role `super_admin`. Validasi juga di server (policy), bukan hanya disembunyikan.
- Tidak bisa menghapus diri sendiri (sudah ada) dan tidak bisa menonaktifkan diri sendiri.
- Password: `->minLength(8)`, konfirmasi password saat create.
- Tambah toggle `is_active` + kolom status di tabel. Ganti delete dengan nonaktifkan untuk user yang pernah mencatat transaksi (FK `user_id` di tabel transaksi).

### 4. Enum bersama (`app/Enums`)
Buat sekarang, dipakai PRD berikut:
- `ItemType`: `raw` (Bahan Baku), `finished` (Produk Jadi), `byproduct` (Hasil Samping)
- `DocumentStatus`: `posted` (Tercatat, hijau), `void` (Dibatalkan, abu)
- `PaymentStatus`: `unpaid` (Belum Bayar, merah), `partial` (Sebagian, kuning), `paid` (Lunas, hijau)
- `PaymentMethod`: `cash` (Tunai), `transfer` (Transfer)
- `QuantityUnit`: `kg`, `sack` (Karung)

### 5. Penomoran dokumen otomatis
- Tabel `document_sequences`: `id`, `prefix` (string 10), `period` (string 8, format `Ymd`), `last_number` (unsigned int), unique(`prefix`,`period`).
- `App\Services\DocumentNumberService::next(string $prefix, CarbonInterface $date): string` → `PB-20260924-0001`. Wajib di dalam transaksi DB + `lockForUpdate()` supaya tidak dobel.
- Prefix yang akan dipakai: `PB` (pembelian), `PR` (produksi), `PJ` (penjualan), `SO` (stock opname), `BY` (pembayaran), `BO` (biaya operasional).

### 6. Branding & locale panel
- `AdminPanelProvider`: `->brandName('PB. Ani Wijaya')`, `->brandLogo(asset('images/logo.png'))` (file sudah ada), warna primary merah Modern Heritage (ambil hex dari `tailwind.config`/CSS publik, jangan menebak), `->sidebarCollapsibleOnDesktop()`.
- Navigation groups (urutan): **Transaksi**, **Stok**, **Keuangan**, **Master Data**, **Website**, **Pengaturan**.
- Hapus `FilamentInfoWidget` dari dashboard.
- `config/app.php`: `timezone` = `Asia/Jakarta`, `locale` = `id` (via env `APP_TIMEZONE`/`APP_LOCALE` jika tersedia).

## Out of scope
Modul transaksi baru (PRD-02+). Jangan sentuh `stock_transactions` selain rename role di header action nota.

## Acceptance criteria
- [ ] Dengan `APP_ENV=production` lokal, user aktif ber-role bisa login; user `is_active=false` atau tanpa role ditolak.
- [ ] Owner tidak bisa membuat/mengedit/menghapus super_admin (dicoba lewat UI dan lewat request langsung → 403).
- [ ] `php artisan migrate:fresh --seed` menghasilkan 3 role + semua site settings + (opsional) 1 super admin dari env.
- [ ] `DocumentNumberService` menghasilkan nomor berurutan per prefix per hari, reset di hari baru.
- [ ] Panel tampil dengan brand & warna baru, locale Indonesia.

## Test (tests/Feature)
- `PanelAccessTest`: aktif+role → 200 `/admin`; nonaktif → 403; tanpa role → 403.
- `UserManagementTest`: owner tidak bisa update user super_admin (policy); owner tidak bisa assign role super_admin.
- `DocumentNumberServiceTest`: urutan `0001, 0002`, prefix berbeda independen, tanggal berbeda reset.

## Rencana commit
1. `feat(auth): implement FilamentUser with is_active flag`
2. `refactor(rbac): rename admin role to owner`
3. `feat(users): single role select and super_admin protection`
4. `feat(core): add shared enums and document number service`
5. `chore(seed): role seeder and env-based super admin`
6. `style(panel): brand, colors, navigation groups, locale`
7. `test: panel access, user management, document numbers`
