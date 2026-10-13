# Deploy Pertama ke Hostinger — aniwijaya.com

Checklist Bagian B PRD-09, dikerjakan **Erlan sendiri** di hPanel dan SSH. Perintah di bawah sudah disesuaikan dengan isi repo
(`deploy/public_html/`, `deploy/deploy.sh`, `.env.production.example`) dan sudah dicoba pada simulasi lokal (lihat `docs/progress-prd-09.md`).

**Jangan pernah** menempel password/DB/kunci ke chat, commit, atau file di repo. Nilai rahasia hanya di `.env` server dan password manager.

Struktur akhir di server:
```
/home/u838281704/domains/aniwijaya.com/
├── pb-ani-wijaya/     ← git clone (kode, .env, vendor, storage)
└── public_html/       ← isi public/ + index.php & .htaccess versi server + symlink storage
```

---

## B1. Domain & website (hPanel)
- [ ] **aniwijaya.com** sudah terdaftar. Kalau dibeli di luar Hostinger, arahkan nameserver ke Hostinger (propagasi bisa beberapa jam).
- [ ] **Websites → Add website** → `aniwijaya.com` (folder `domains/aniwijaya.com/public_html` dibuat otomatis).
- [ ] Pengaturan PHP website: versi **8.3**.
- [ ] **SSL** → pasang sertifikat gratis untuk `aniwijaya.com` **dan** `www.aniwijaya.com`.

## B2. Database (hPanel → Databases → MySQL)
- [ ] Buat database + user dengan password kuat, simpan di password manager.
- [ ] Catat nama DB, user, password — **lengkap dengan prefix `u838281704_`**.

## B3. SSH & akses GitHub
- [ ] hPanel → **SSH Access** → aktifkan, catat host/port/user.
- [ ] Login SSH, buat key khusus server:
  ```bash
  ssh-keygen -t ed25519 -C "hostinger-aniwijaya" -f ~/.ssh/id_ed25519
  cat ~/.ssh/id_ed25519.pub
  ```
- [ ] GitHub → repo `pb-ani-wijaya` → **Settings → Deploy keys → Add** → tempel public key, **jangan centang write access**.
- [ ] Uji: `ssh -T git@github.com` (harus menyebut nama repo / "successfully authenticated").

## B4. Di laptop sebelum deploy pertama
- [ ] Branch `feature/prd-09-deploy` sudah di-merge ke `main` dan `git push origin main`.
- [ ] Asset Vite: **saat ini tidak diperlukan** — tidak ada halaman yang memakai `@vite` (publik = Tailwind CDN, admin = Bootstrap CDN + `public/css/admin.css`). Kalau nanti ada CSS/JS Vite:
  ```bash
  npm run build
  git add public/build
  git commit -m "build: production assets"
  git push origin main
  ```

## B5. Deploy pertama (SSH)
```bash
cd ~/domains/aniwijaya.com
echo $HOME                               # harus /home/u838281704 — dipakai di APP_PUBLIC_PATH
php -v                                   # harus 8.3. Kalau bukan, tanya Kodee path PHP 8.3 CLI dan pakai path itu
                                         # sebagai pengganti "php" di semua perintah di bawah
git clone git@github.com:Erlangga2207/pb-ani-wijaya.git
cd pb-ani-wijaya

cp .env.production.example .env
nano .env
```
Di `nano`, isi: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (dengan prefix `u838281704_`), `ADMIN_EMAIL`, `ADMIN_PASSWORD`.
Pastikan `APP_PUBLIC_PATH=/home/u838281704/domains/aniwijaya.com/public_html` sesuai hasil `echo $HOME`. Simpan: `Ctrl+O`, `Enter`, `Ctrl+X`.

```bash
composer2 install --no-dev --optimize-autoloader --no-interaction   # kalau composer2 tidak ada: composer install ...
php artisan key:generate --force         # --force wajib: di APP_ENV=production artisan meminta konfirmasi
chmod -R 775 storage bootstrap/cache

# siapkan public_html
rm -f ../public_html/default.php ../public_html/index.html
cp -r public/. ../public_html/                                               # 1) salin isi public/
cp deploy/public_html/index.php deploy/public_html/.htaccess ../public_html/ # 2) timpa dengan versi server
ln -s ~/domains/aniwijaya.com/pb-ani-wijaya/storage/app/public ~/domains/aniwijaya.com/public_html/storage
ls -la ../public_html/storage            # harus tampil "storage -> .../pb-ani-wijaya/storage/app/public"

php artisan migrate --force
php artisan db:seed --force              # role, pengaturan website, 5 barang, super admin dari ADMIN_EMAIL/PASSWORD
php artisan optimize

php artisan tinker --execute="echo public_path(), PHP_EOL;"   # harus .../domains/aniwijaya.com/public_html
```

- [ ] Buka `https://aniwijaya.com/panel/login`, login dengan `ADMIN_EMAIL`.
- [ ] Setelah berhasil login: **hapus baris `ADMIN_PASSWORD`** dari `.env` (`nano .env`), lalu `php artisan optimize`.
  `db:seed` aman dijalankan ulang kapan saja: tidak membuat duplikat dan tidak menimpa editan admin.

## B6. Isi konten (lewat `/panel`)
Data lokal **tidak ikut** ke server.
- [ ] Upload ulang foto galeri (Website › Galeri).
- [ ] Cek Website › Pengaturan Website (tiap tab bahasa) dan Katalog Produk.
- [ ] Buat akun owner/staff (Pengaturan › Pengguna).

## B7. Cek setelah live
- [ ] `http://aniwijaya.com` dan `https://www.aniwijaya.com` → diarahkan ke `https://aniwijaya.com` (301)
- [ ] `/`, `/en`, `/zh`, `/ja`, `/ko` tampil, termasuk di HP
- [ ] Upload gambar di `/panel` → tampil di admin & website (`/galeri`)
- [ ] Form kontak masuk ke Website › Pesan & Calon Pembeli
- [ ] Login staff → halaman keuangan (mis. `/panel/biaya`) 403
- [ ] `https://aniwijaya.com/.env` dan `https://aniwijaya.com/composer.json` → **403** (bukan isi file)
- [ ] URL ngawur (mis. `/abc123`) → halaman 404 biasa, **tanpa** pesan error teknis (bukti `APP_DEBUG=false`)
- [ ] `https://aniwijaya.com/sitemap.xml` dan `/robots.txt` bisa dibuka; `/admin` → pindah ke `/panel`
- [ ] Lihat sumber halaman (`Ctrl+U`) di `/produk`: `<link rel="canonical" href="https://aniwijaya.com/produk">`

## B8. Update berikutnya
Di laptop: commit → (`npm run build` + commit `public/build` bila CSS/JS Vite berubah) → `git push origin main`. Di server:
```bash
bash ~/domains/aniwijaya.com/pb-ani-wijaya/deploy/deploy.sh
# kalau "php" bukan 8.3:
PHP_BIN=/path/ke/php83 bash ~/domains/aniwijaya.com/pb-ani-wijaya/deploy/deploy.sh
```
Skrip: cek PHP ≥ 8.3 → mode perawatan → `git pull` → `composer install` → migrasi → salin `public/` ke `public_html`
(tidak menyentuh `index.php`, `.htaccess`, `storage`) → `optimize` → website live. Kalau gagal di tengah, website tetap dinaikkan
lagi dan pesan errornya tampil — perbaiki, lalu jalankan ulang.

Kalau `deploy/public_html/index.php` atau `.htaccess` berubah di repo, salin manual (skrip sengaja tidak menimpanya):
```bash
cp ~/domains/aniwijaya.com/pb-ani-wijaya/deploy/public_html/{index.php,.htaccess} ~/domains/aniwijaya.com/public_html/
```

## B9. Setelah live
- [ ] Google Search Console → verifikasi domain → submit `https://aniwijaya.com/sitemap.xml`.
- [ ] Google Business Profile untuk lokasi pabrik di Indramayu.
- [ ] Aktifkan backup otomatis di hPanel. Sebelum update besar, backup database dulu.
- [ ] Sebelum go-live penuh: terjemahan en/zh/ja/ko di `docs/translation-review.md` dicek penutur asli.

---

## Kalau ada masalah
| Gejala | Cek |
|---|---|
| Error 500 / halaman putih | `tail -n 50 ~/domains/aniwijaya.com/pb-ani-wijaya/storage/logs/laravel-$(date +%F).log` |
| "No application encryption key" | `php artisan key:generate --force` lalu `php artisan optimize` |
| Foto galeri tidak tampil (404) | `ls -la ~/domains/aniwijaya.com/public_html/storage` — symlink harus ada; buat ulang dengan perintah `ln -s` di B5 |
| CSS admin hilang / error `filemtime` | `public_html/css/admin.css` harus ada → `cp -r public/. ../public_html/` lalu salin ulang `index.php` & `.htaccess` versi server |
| Website tertahan "mode perawatan" | `cd ~/domains/aniwijaya.com/pb-ani-wijaya && php artisan up` |
| Perubahan `.env` tidak berlaku | `php artisan optimize` (config di-cache) |
| Permission denied di storage | `chmod -R 775 storage bootstrap/cache` |
