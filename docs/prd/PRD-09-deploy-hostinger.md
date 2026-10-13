# PRD-09 — Persiapan Deploy ke Hostinger (aniwijaya.com)

**Branch:** `feature/prd-09-deploy` · **Bergantung pada:** PRD-08 fase 7 sudah di-merge ke `main`

Dokumen ini punya dua bagian:
- **Bagian A**: dikerjakan Claude Code di laptop (menyiapkan file & konfigurasi di repo).
- **Bagian B**: dikerjakan Erlan sendiri di hPanel dan SSH. **Claude Code tidak menjalankan apa pun di server.**

## Fakta server (dari jawaban agent Hostinger)
- Paket Business Web Hosting, user `u838281704`, PHP web 8.3, ekstensi lengkap, `.htaccess`/mod_rewrite didukung.
- Fungsi PHP `symlink()` **dimatikan** → `php artisan storage:link` gagal; symlink dibuat lewat `ln -s` di SSH.
- Ganti document root & mengganti `public_html` dengan symlink **tidak disarankan**.
- Struktur yang dipakai:
```
/home/u838281704/domains/aniwijaya.com/
├── pb-ani-wijaya/     ← git clone (kode, .env, vendor, storage)
└── public_html/       ← isi folder public/ + index.php yang sudah disesuaikan
```

---

## Bagian A — Tugas Claude Code

Commit per langkah, catat progres di `docs/progress-prd-09.md`. Jangan menyentuh desain halaman publik maupun logika bisnis.

### A1. Public path yang bisa diatur (web + artisan)
- `config/app.php`: `'public_path' => env('APP_PUBLIC_PATH'),`
- `AppServiceProvider::register()`: kalau `config('app.public_path')` terisi → `$this->app->usePublicPath(...)`.
- Alasan: artisan tidak lewat `index.php`, jadi pengaturan harus di level config. Pakai `config()`, bukan `env()`, supaya tetap jalan setelah `config:cache`.
- Test: dengan config di-set, `public_path('x')` mengarah ke path tersebut; tanpa config tetap `base_path('public')`.

### A2. HTTPS & domain kanonik
- `AppServiceProvider::boot()`: di environment `production`, `URL::forceScheme('https')`.
- Pastikan semua URL absolut (hreflang, canonical, sitemap, Open Graph, nota) memakai `config('app.url')`, bukan host request.

### A3. Template file untuk `public_html`
Buat folder `deploy/public_html/`:
- `index.php`: salin dari `public/index.php`, lalu ganti **setiap** path `__DIR__.'/../...'` menjadi `__DIR__.'/../pb-ani-wijaya/...'` (termasuk path `storage/framework/maintenance.php`, `vendor/autoload.php`, `bootstrap/app.php`).
- `.htaccess`: salin `public/.htaccess`, lalu tambahkan di atas aturan Laravel:
  - redirect `http` → `https` (301)
  - redirect `www.aniwijaya.com` → `aniwijaya.com` (301), satu domain kanonik untuk SEO
  - tolak akses file tersembunyi (`.env`, `.git*`) dan `composer.*` / `package*.json`

### A4. Asset frontend tanpa Node di server
- Hapus `/public/build` dari `.gitignore` supaya hasil `npm run build` ikut ter-commit. Server tidak perlu Node.
- Tulis di README: **setiap mengubah CSS/JS → `npm run build` → commit `public/build`**.

### A5. `.env.production.example` (tanpa rahasia)
Isi minimal:
```
APP_NAME="PB. Ani Wijaya"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://aniwijaya.com
APP_PUBLIC_PATH=/home/u838281704/domains/aniwijaya.com/public_html
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_TIMEZONE=Asia/Jakarta
LOG_CHANNEL=daily
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
ADMIN_EMAIL=
ADMIN_PASSWORD=
```
Sesuaikan dengan key yang benar-benar dipakai project (cek `.env.example`). Jangan masukkan nilai rahasia.

### A6. Seeder aman untuk produksi
- `php artisan db:seed --force` di produksi hanya boleh menjalankan seeder **wajib**: Role, SiteSetting, Item, dan super admin dari env.
- Seeder data dummy/demo (kalau ada) hanya jalan di `local`/`testing`.
- Semua seeder wajib bersifat idempotent (aman dijalankan ulang tanpa duplikat atau menimpa editan admin).

### A7. `deploy/deploy.sh` (dijalankan Erlan di server setiap update)
Bash, `set -euo pipefail`, bisa diatur lewat variabel di atas file:
```
APP_DIR=$HOME/domains/aniwijaya.com/pb-ani-wijaya
PUBLIC_DIR=$HOME/domains/aniwijaya.com/public_html
PHP_BIN=php
COMPOSER_BIN=composer2   # fallback ke composer kalau tidak ada
```
Urutan:
1. Cek `PHP_BIN -v` ≥ 8.3, kalau tidak → berhenti dengan pesan jelas.
2. `php artisan down --retry=60`, dan pastikan `php artisan up` tetap dijalankan walau skrip gagal (`trap`).
3. `git pull --ff-only origin main`
4. `composer install --no-dev --optimize-autoloader --no-interaction`
5. `php artisan migrate --force`
6. Salin isi `public/` ke `PUBLIC_DIR` **kecuali** `index.php`, `.htaccess`, dan `storage` (pakai `rsync` kalau ada, kalau tidak pakai `cp -r`). Jangan pernah menghapus `PUBLIC_DIR/storage`.
7. `php artisan optimize:clear && php artisan optimize`
8. `php artisan up`, lalu cetak ringkasan (commit yang ter-deploy).

### A8. SEO dasar untuk peluncuran
- `public/robots.txt`: izinkan semua, `Disallow: /panel`, dan sertakan `Sitemap: https://aniwijaya.com/sitemap.xml`.
- Halaman `/panel/*` diberi `<meta name="robots" content="noindex">`.

### A9. Panduan langkah server
Tulis `deploy/FIRST_DEPLOY.md` berisi Bagian B di bawah sebagai checklist, dengan perintah yang sudah disesuaikan dengan hasil A1–A8.

### Selesai jika
- [ ] `php artisan test` hijau (MySQL).
- [ ] Simulasi lokal: buat folder `simulasi/public_html` dan jalankan `APP_PUBLIC_PATH` mengarah ke sana. Website & `/panel` tampil normal dan upload gambar galeri berhasil (tanpa memakai folder `public/` asli). Hapus folder simulasi setelahnya.
- [ ] `bash -n deploy/deploy.sh` lolos (cek sintaks).
- [ ] Tidak ada rahasia di file yang di-commit.

---

## Bagian B — Langkah Manual Erlan (hPanel + SSH)

### B1. Domain & website
1. Pastikan **aniwijaya.com** sudah terdaftar. Kalau dibeli di luar Hostinger, arahkan nameserver ke Hostinger. Propagasi DNS bisa beberapa jam.
2. hPanel → **Websites → Add website** → `aniwijaya.com`. Folder `domains/aniwijaya.com/public_html` akan dibuat.
3. Pengaturan PHP website ini: versi **8.3**.
4. **SSL** → install sertifikat gratis untuk `aniwijaya.com` dan `www.aniwijaya.com`.

### B2. Database
hPanel → **Databases → MySQL** → buat database + user (password kuat, simpan di password manager). Catat nama DB, user, dan password **lengkap dengan prefix `u838281704_`**.

### B3. SSH & akses GitHub
1. hPanel → **SSH Access** → aktifkan, catat host/port/user.
2. Login SSH, buat key khusus server:
   ```bash
   ssh-keygen -t ed25519 -C "hostinger-aniwijaya" -f ~/.ssh/id_ed25519
   cat ~/.ssh/id_ed25519.pub
   ```
3. GitHub → repo `pb-ani-wijaya` → **Settings → Deploy keys → Add** → tempel public key, **jangan centang write access**. Deploy key hanya bisa membaca satu repo ini, lebih aman daripada key akun.
4. Uji: `ssh -T git@github.com`.

### B4. Di laptop sebelum deploy pertama
```bash
npm run build
git add public/build
git commit -m "build: production assets"
git push origin main
```

### B5. Deploy pertama (SSH)
```bash
cd ~/domains/aniwijaya.com
php -v                                   # harus 8.3; kalau bukan, tanya Kodee path PHP 8.3 CLI
git clone git@github.com:Erlangga2207/pb-ani-wijaya.git
cd pb-ani-wijaya
cp .env.production.example .env
nano .env                                # isi DB_*, ADMIN_EMAIL, ADMIN_PASSWORD
composer2 install --no-dev --optimize-autoloader   # atau: composer install ...
php artisan key:generate
chmod -R 775 storage bootstrap/cache

# siapkan public_html
rm -f ../public_html/default.php ../public_html/index.html
cp -r public/. ../public_html/                                          # salin isi public/ dulu
cp deploy/public_html/index.php deploy/public_html/.htaccess ../public_html/   # lalu timpa dengan versi server
ln -s ~/domains/aniwijaya.com/pb-ani-wijaya/storage/app/public ~/domains/aniwijaya.com/public_html/storage

php artisan migrate --force
php artisan db:seed --force
php artisan optimize
```
Setelah berhasil login ke `/panel`: **hapus baris `ADMIN_PASSWORD` dari `.env`**, lalu `php artisan optimize`.

### B6. Isi konten
Data lokal **tidak ikut** ke server. Upload ulang foto galeri, cek Pengaturan Website, dan buat akun owner/staff lewat `/panel`.

### B7. Cek setelah live
- [ ] `http://aniwijaya.com` dan `https://www.aniwijaya.com` → diarahkan ke `https://aniwijaya.com`
- [ ] `/`, `/en`, `/zh`, `/ja`, `/ko` tampil, termasuk di HP
- [ ] Upload gambar di `/panel` → tampil di admin & website
- [ ] Form kontak masuk ke "Pesan & Calon Pembeli"
- [ ] Login staff → halaman keuangan 403
- [ ] `https://aniwijaya.com/.env` → 403/404 (**bukan** isi file)
- [ ] Buka URL ngawur (mis. `/abc123`) → halaman 404 biasa, **tanpa** pesan error teknis (bukti `APP_DEBUG=false`)
- [ ] `https://aniwijaya.com/sitemap.xml` dan `/robots.txt` bisa dibuka

### B8. Update berikutnya
Di laptop: commit → `npm run build` (kalau CSS/JS berubah) → push. Di server:
```bash
bash ~/domains/aniwijaya.com/pb-ani-wijaya/deploy/deploy.sh
```

### B9. Setelah live
- Google Search Console → verifikasi domain → submit `sitemap.xml`.
- Google Business Profile untuk lokasi pabrik di Indramayu.
- Aktifkan backup otomatis di hPanel. Sebelum update besar, backup database dulu.
