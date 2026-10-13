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
| A8. `public/robots.txt`: `User-agent: *`, `Disallow: /panel`, `Sitemap: https://aniwijaya.com/sitemap.xml`. Meta `noindex` di `/panel` (termasuk login) **sudah ada** sejak Fase 1 lewat `admin/layouts/aset.blade.php` — dikunci dengan test `SeoLaunchTest`. Halaman cetak (nota, bukti, laporan) wajib login → tidak bisa dirayapi | ✅ | `06a1dfb` |
| Perbaikan dari simulasi: `deploy.sh` menjalankan `artisan config:clear` sebelum `migrate` (sebelumnya migrasi memakai config cache rilis lama) | ✅ | `975d0c1` |
| A9. `deploy/FIRST_DEPLOY.md`: checklist B1–B9 + tabel "Kalau ada masalah". Disesuaikan dari simulasi: `key:generate --force` (di production artisan minta konfirmasi), cek `echo $HOME` untuk `APP_PUBLIC_PATH`, cek symlink & `public_path()` setelah deploy, B4 build Vite saat ini tidak diperlukan, cara memakai `PHP_BIN` bila `php` bukan 8.3, salin manual bila template `deploy/public_html` berubah, cek `/composer.json` 403 & canonical di B7 | ✅ | `43f0370` |
| Verifikasi akhir (tabel di bawah) + Pint `config/app.php` | ✅ | `a60f0b1` |

## Catatan
- Pemakaian `public_path()` saat runtime hanya `filemtime(public_path('css/admin.css'))` di `admin/layouts/aset.blade.php` → di server file itu harus ada di `public_html/css/` (disalin `deploy.sh`).
- `config/filesystems.php` `links` dihitung saat config dimuat (sebelum provider) → tetap `base_path('public/storage')`. Hanya dipakai `php artisan storage:link`, yang memang tidak bisa dipakai di Hostinger (`symlink()` dimatikan); symlink dibuat manual dengan `ln -s`.

## Simulasi lokal (2026-10-13)
Struktur meniru server di scratchpad (bukan di repo, supaya tidak mungkin ter-commit): `sim/pb-ani-wijaya` = `git clone` branch ini, `sim/public_html` = folder web. MySQL 8.0.30 database sementara `pb_ani_wijaya_prd09_tmp`. Web server: `php -S` dari `public_html` dengan router Laravel (`server.php` → `public_html/index.php` versi server). Semua dihapus setelahnya; `pb_ani_wijaya` dan repo tidak disentuh.

| Langkah (mengikuti B5) | Hasil |
|---|---|
| `composer install --no-dev --optimize-autoloader` | 80 paket |
| `cp .env.production.example .env` → `key:generate --force` | `APP_KEY` terisi (baris `APP_KEY=` di template berfungsi). Diubah untuk laptop saja: `APP_URL`, `APP_PUBLIC_PATH`, DB sementara, `SESSION_SECURE_COOKIE=false` (http), akun admin dummy. `APP_ENV=production` tetap |
| `cp -r public/. ../public_html/` + `cp deploy/public_html/{index.php,.htaccess}` + link `storage` | Windows tanpa hak admin tidak bisa `ln -s` → dipakai **junction** (`mklink /J`), setara untuk tes ini. Di server tetap `ln -s` |
| `migrate --force`, `db:seed --force`, `optimize` | 29 migration, 3 seeder + super admin; `public_path()` tetap `public_html` **setelah `config:cache`** |
| Folder `public/` milik clone di-rename selama uji web | membuktikan website tidak memakai `public/` asli |
| `/`, `/en`, `/zh/produk`, `/ja/galeri`, `/ko/kontak`, `/tentang-kami`, `/sitemap.xml`, `/robots.txt`, `/css/admin.css`, `/panel/login` | semua 200; `admin.css?v=<filemtime>` terbaca dari `public_html` |
| `/abc123` | 404 tanpa detail error (`APP_DEBUG=false`) |
| `/admin` | 301 → `/panel` (Location relatif, jadi di server mengikuti https) |
| Login `/panel` (curl + CSRF) → upload foto galeri (PNG 237.879 byte) | 302 sukses; file di `pb-ani-wijaya/storage/app/public/gallery/…`; `GET /storage/gallery/…` lewat `public_html/storage` → 200, ukuran sama; tampil di `/panel/galeri` dan `/galeri` |
| `deploy.sh` jalur sukses (rsync & composer2 tidak ada → jalur fallback `cp -r` & `composer` ikut teruji) | exit 0; `index.php` & `.htaccess` server tidak tertimpa (sha1 sama); file baru di `public/` tersalin; junction `storage` & foto tetap ada; website live |
| `deploy.sh` jalur gagal (migration rusak sementara) | exit 1, pesan "Deploy GAGAL — website sudah dinaikkan lagi", tidak tertinggal di mode perawatan, beranda 200 |
| `deploy.sh` cek awal: `public_html/index.php` tidak ada; PHP 8.1 | berhenti dengan pesan jelas **sebelum** `artisan down` |
| `.htaccess` | diuji terpisah di Apache sementara (lihat A3) |

## Verifikasi akhir — "Selesai jika" PRD-09
| Kriteria | Hasil |
|---|---|
| `php artisan test` hijau (MySQL) | **180/180** di MySQL 8.0.30 (DB sementara `pb_ani_wijaya_prd09_test`: `migrate:fresh --seed` → `optimize:clear` → test → drop) dan 180/180 di SQLite |
| Simulasi lokal `public_html` + `APP_PUBLIC_PATH` | lolos — website 5 bahasa & `/panel` tampil, upload galeri berhasil tanpa `public/` asli (lihat "Simulasi lokal"); folder simulasi dihapus |
| `bash -n deploy/deploy.sh` | lolos (plus dijalankan sungguhan di simulasi: jalur sukses, gagal, cek awal) |
| Tidak ada rahasia di file yang di-commit | dipindai di seluruh diff `main..HEAD`: tidak ada `APP_KEY`, password, token, atau kunci privat. Satu-satunya temuan = nilai dummy di `ProductionSeederTest` (user `@aniwijaya.test` di DB test) |
| Pint (file PHP yang berubah) | lolos |

Catatan: `optimize:clear` pada DB MySQL kosong gagal karena `.env` lokal memakai `CACHE_STORE=database` (tabel `cache` belum ada) — jalankan setelah migrasi. Di server `CACHE_STORE=file`, jadi tidak terpengaruh.

---

# Revisi: deploy lewat fitur Git Hostinger (2026-10-13)

Branch: `feature/prd-09-git-deploy` (dari `main` @ `c608622`).
Keputusan Erlan: seluruh repo di-clone oleh fitur Git hPanel ke `/home/u838281704/domains/aniwijaya.com/public_html`.
`public/` tetap di repo seperti biasa; root `.htaccess` meneruskan request ke `public/`.
Keputusan tambahan: root `.htaccess` **tidak** memblokir seluruh `/storage` (foto galeri memakai URL `/storage/gallery/...` lewat symlink
`public/storage`), melainkan isi internal `storage/app|framework|logs` + `storage/.htaccess` "Require all denied".

| Langkah | Status | Commit |
|---|---|---|
| R1. Batalkan A1 (`APP_PUBLIC_PATH`, `usePublicPath`, `PublicPathTest`) & A3 (`deploy/public_html/`) | ✅ | `e16606e` |
| R2. Root `.htaccess`: `.well-known` dilayani langsung; 403 untuk file tersembunyi (`.git*`, `.env*`, ...), `composer.*`, `package*.json`, `artisan`, `phpunit.xml`, `vite.config.js`, `*.md`, folder `.git|app|bootstrap|config|database|lang|resources|routes|tests|vendor|deploy|docs|node_modules`, dan `storage/app|framework|logs`; sisanya → `public/$1`; `Options -Indexes`; tanpa mod_rewrite → semua ditolak; **tanpa redirect HTTPS/www**. Lapis kedua: `storage/.htaccess` & `bootstrap/.htaccess` `Require all denied` | ✅ | `2d96e29` |
| R3. `bootstrap/app.php`: bila `dirname(__DIR__, 2)/.env.pb-ani-wijaya` ada → `useEnvironmentPath(dirname(__DIR__, 2))` + `loadEnvironmentFrom('.env.pb-ani-wijaya')`. `key:generate` menulis ke `environmentFilePath()` (= file ini). Di laptop: `C:/laragon/www/.env.pb-ani-wijaya` tidak ada → `.env` biasa (dicek: `environmentFilePath()` = `.env` repo; 178/178 hijau) | ✅ | `3526f9f` |
| R4. `deploy/deploy.sh` baru: `APP_DIR` = `public_html`, tanpa `git pull` & tanpa salin asset. Isi: `composer install --no-dev -o` → `artisan down` (+`trap` selalu `up`) → `config:clear` + `migrate --force` → `optimize:clear` + `optimize` → `up`. Cek awal: PHP ≥ 8.3, composer2→composer, `artisan` ada, `../.env.pb-ani-wijaya` ada. `composer install` sengaja **sebelum** `down`: kode baru sudah live begitu hPanel menarik repo, dan `artisan` butuh `vendor/` yang cocok (deploy pertama: belum ada `vendor/`). `bash -n` lolos | ✅ | (commit berikut) |
