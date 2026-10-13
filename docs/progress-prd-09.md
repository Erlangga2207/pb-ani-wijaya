# Progres PRD-09 — Persiapan Deploy Hostinger (Bagian A)

Branch: `feature/prd-09-deploy` (dari `main` @ `42bb099`, Fase 7 sudah di-merge) · Mulai: 2026-10-13
Aturan sesi: tidak menjalankan apa pun ke server, tidak menulis nilai rahasia di file mana pun.

Legenda: ✅ selesai · 🟡 sebagian · ⬜ belum

## Log langkah
| Langkah | Status | Commit |
|---|---|---|
| PRD dipindah `docs/PRD-09-deploy-hostinger.md` → `docs/prd/` (sejajar PRD lain) | ✅ | `7275a74` |
| A1. `config('app.public_path')` ← `APP_PUBLIC_PATH`; `AppServiceProvider::register()` → `usePublicPath()`. Test `PublicPathTest` | ✅ | (commit berikut) |
| A2. HTTPS & domain kanonik | ⬜ | |
| A3. `deploy/public_html/{index.php,.htaccess}` | ⬜ | |
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
