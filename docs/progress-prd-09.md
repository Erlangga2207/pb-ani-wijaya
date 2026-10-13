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
| A3. `deploy/public_html/index.php` (3 path → `../pb-ani-wijaya/`) + `.htaccess` (tolak file tersembunyi kecuali `.well-known`, tolak `composer.*`/`package*.json`, 1× redirect 301 `http`/`www` → `https://aniwijaya.com` kecuali `.well-known`). **Diuji di Apache 2.4.54 sementara** (port 8098, config di scratchpad, Laragon tidak disentuh): `/.env`, `/.git/config`, `/composer.json`, `/COMPOSER.LOCK`, `/package-lock.json` → 403; `/.well-known/acme-challenge/x` → 200; `http://aniwijaya.com/produk?x=1` & `http://www.aniwijaya.com/en/produk` → 301 `https://aniwijaya.com/...` (path + query terbawa). Jalur https asli hanya bisa dicek di server | ✅ | (commit berikut) |
| A4. `public/build` ikut di-commit + README | ⬜ | |
| A5. `.env.production.example` | ⬜ | |
| A6. Seeder aman untuk produksi | ⬜ | |
| A7. `deploy/deploy.sh` | ⬜ | |
| A8. robots.txt + noindex `/panel` | ⬜ | |
| A9. `deploy/FIRST_DEPLOY.md` | ⬜ | |
| Verifikasi: test MySQL, simulasi `public_html`, `bash -n`, cek rahasia | ⬜ | |

## Catatan
- Pemakaian `public_path()` saat runtime hanya `filemtime(public_path('css/admin.css'))` di `admin/layouts/aset.blade.php` → di server file itu harus ada di `public_html/css/` (disalin `deploy.sh`).
- `config/filesystems.php` `links` dihitung saat config dimuat (sebelum provider) → tetap `base_path('public/storage')`. Hanya dipakai `php artisan storage:link`, yang memang tidak bisa dipakai di Hostinger (`symlink()` dimatikan); symlink dibuat manual dengan `ln -s`.
