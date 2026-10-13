# Progres PRD-09 — Persiapan Deploy Hostinger (Bagian A)

Branch: `feature/prd-09-deploy` (dari `main` @ `42bb099`, Fase 7 sudah di-merge) · Mulai: 2026-10-13
Aturan sesi: tidak menjalankan apa pun ke server, tidak menulis nilai rahasia di file mana pun.

Legenda: ✅ selesai · 🟡 sebagian · ⬜ belum

## Log langkah
| Langkah | Status | Commit |
|---|---|---|
| PRD dipindah `docs/PRD-09-deploy-hostinger.md` → `docs/prd/` (sejajar PRD lain) | ✅ | `7275a74` |
| A1. `config('app.public_path')` ← `APP_PUBLIC_PATH`; `AppServiceProvider::register()` → `usePublicPath()`. Test `PublicPathTest` | ✅ | `5be298c` |
| A2. Produksi: `URL::forceRootUrl(config('app.url'))` + `forceScheme('https')`. Semua URL absolut (canonical, hreflang, sitemap, nota) lewat URL generator → otomatis `APP_URL`. Tag Open Graph belum ada di layout publik (tidak ditambah: desain publik tidak boleh diubah). Test `CanonicalUrlTest` (request via `http://www.` → `https://aniwijaya.com`) | ✅ | `5e3211e` |
| A3. `deploy/public_html/index.php` (3 path → `../pb-ani-wijaya/`) + `.htaccess` (tolak file tersembunyi kecuali `.well-known`, tolak `composer.*`/`package*.json`, 1× redirect 301 `http`/`www` → `https://aniwijaya.com` kecuali `.well-known`). **Diuji di Apache 2.4.54 sementara** (port 8098, config di scratchpad, Laragon tidak disentuh): `/.env`, `/.git/config`, `/composer.json`, `/COMPOSER.LOCK`, `/package-lock.json` → 403; `/.well-known/acme-challenge/x` → 200; `http://aniwijaya.com/produk?x=1` & `http://www.aniwijaya.com/en/produk` → 301 `https://aniwijaya.com/...` (path + query terbawa). Jalur https asli hanya bisa dicek di server | ✅ | `2fb4bf8` |
| A4. `/public/build` dihapus dari `.gitignore`; README: bagian "Asset frontend & deploy" (`npm run build` → commit `public/build`). Catatan: saat ini **tidak ada halaman yang memakai `@vite`** (publik = Tailwind CDN, admin = Bootstrap CDN + `admin.css`; `@vite` hanya di `welcome.blade.php` yang tidak punya route) → `public/build` belum perlu di-commit. Sekalian: 3 sisa teks Filament di README diperbaiki | ✅ | `5ade6dc` |
| A5. `.env.production.example`: isi PRD + key yang dipakai project. Tambahan di luar daftar PRD: **`APP_KEY=`** (wajib ada agar `key:generate` bisa mengisi), `LOG_DAILY_DAYS=14`, `MAIL_MAILER=log` (aplikasi tidak mengirim email), `SESSION_*` bawaan, `PRODUCTION_YIELD_*` (dikomentari). Semua nilai rahasia kosong; file tidak di-ignore | ✅ | `ab933c7` |
| A6. `DatabaseSeeder` hanya berisi seeder wajib (Role, SiteSetting, Item, super admin dari env); **tidak ada seeder dummy**. Ditemukan & diperbaiki: `SiteSettingSeeder` memakai `updateOrCreate` → `db:seed` ulang **menimpa Pengaturan Website yang diedit admin**. Kini key baru dibuat lengkap, key lama hanya diperbarui label/grup/tipe/deskripsi/urutan. `RoleSeeder`/`ItemSeeder`/super admin sudah `firstOrCreate` (tidak menimpa password/nama). Test `ProductionSeederTest` (seed 2×: jumlah sama, editan admin & password tetap). 178/178 hijau | ✅ | `df0ec09` |
| A7. `deploy/deploy.sh`: variabel bisa di-override lewat env; cek PHP ≥ 8.3, composer2→composer, `.env` & `public_html/index.php` ada (sebelum down); `artisan down --retry=60` + `trap EXIT` → selalu `artisan up`; `git pull --ff-only`; `composer install --no-dev`; `migrate --force`; salin `public/` (rsync, fallback `cp -r`) **tanpa** `index.php`, `.htaccess`, `storage`, tanpa `--delete`; `optimize:clear && optimize`; ringkasan commit. `bash -n` lolos; mode git 755; LF | ✅ | `83d485a` |
| A8. `public/robots.txt`: `User-agent: *`, `Disallow: /panel`, `Sitemap: https://aniwijaya.com/sitemap.xml`. Meta `noindex` di `/panel` (termasuk login) **sudah ada** sejak Fase 1 lewat `admin/layouts/aset.blade.php` — dikunci dengan test `SeoLaunchTest`. Halaman cetak (nota, bukti, laporan) wajib login → tidak bisa dirayapi | ✅ | (commit berikut) |
| A9. `deploy/FIRST_DEPLOY.md` | ⬜ | |
| Verifikasi: test MySQL, simulasi `public_html`, `bash -n`, cek rahasia | ⬜ | |

## Catatan
- Pemakaian `public_path()` saat runtime hanya `filemtime(public_path('css/admin.css'))` di `admin/layouts/aset.blade.php` → di server file itu harus ada di `public_html/css/` (disalin `deploy.sh`).
- `config/filesystems.php` `links` dihitung saat config dimuat (sebelum provider) → tetap `base_path('public/storage')`. Hanya dipakai `php artisan storage:link`, yang memang tidak bisa dipakai di Hostinger (`symlink()` dimatikan); symlink dibuat manual dengan `ln -s`.
