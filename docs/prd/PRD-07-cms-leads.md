# PRD-07 — Konten Website (CMS) & Lead Pembeli

**Branch:** `feature/prd-07-cms-leads` · **Bergantung pada:** PRD-01 · Bisa dikerjakan paralel dengan PRD-02–06

> ⚠️ **Desain halaman publik tidak boleh berubah.** PRD ini hanya menyentuh admin panel, route, controller, dan (satu kasus) binding data di Blade — bukan markup/class/style.

## Tujuan
Mengelola konten website dengan aman (tidak bisa merusak halaman publik secara tidak sengaja) dan mengubah pesan dari form kontak menjadi **calon pembeli** yang bisa ditindaklanjuti — karena tujuan website adalah menarik pembeli.

## 1. Halaman Pengaturan Website (ganti `SiteSettingResource`)
- Buat Filament custom page **Website › Pengaturan Website** (owner+).
- Form dengan **Tabs** sesuai `group` di seeder: Hero, Umum, Tentang, Kontak, Sosial Media. Field dibangkitkan dari baris `site_settings` (label, type → komponen: text→TextInput, textarea→Textarea, url→TextInput url, email, phone→TextInput tel, image→FileUpload ke disk `public` folder `settings`). `description` jadi helper text.
- Tombol **Simpan** → update `value` per key (event `saved` model tetap jalan agar cache ter-reset). Validasi sesuai type (url, email).
- **Tidak ada** create/delete key dari UI. Key baru hanya lewat `SiteSettingSeeder` (idempotent).
- Sembunyikan `SiteSettingResource` dari navigasi (atau hapus) setelah page baru jalan.
- Tambah tombol "Lihat Website" di header page (link ke `/`, tab baru).

## 2. Lead dari Form Kontak
Migration `contact_messages`: tambah `phone` nullable, `company` nullable, `country` nullable, `status` string default `new` (`LeadStatus`: `new` Baru, `contacted` Dihubungi, `won` Deal, `lost` Tidak Lanjut, `spam` Spam), `buyer_id` FK nullable, `handled_by` FK users nullable, `ip_address` nullable.
Kolom `is_read`/`read_at` tetap dipertahankan.

UI **Website › Pesan & Calon Pembeli** (owner+):
- List: tanggal, nama, email, HP, subjek, status (badge). Filter status. Badge jumlah `new` di navigasi (`getNavigationBadge`).
- View: isi pesan, `markAsRead()` otomatis (sudah ada), catatan admin, ubah status.
- Actions: **Balas via WhatsApp** (jika ada HP), **Balas via Email** (`mailto:`), **Jadikan Pembeli** (buat `Buyer` dari data pesan, isi `buyer_id`, status → `contacted`), **Tandai Spam**.
- Tidak boleh create dari admin (hapus `CreateContactMessage`).

## 3. Anti-spam form kontak (tanpa ubah desain)
- Route `POST /kontak`: tambah middleware `throttle:5,1` (5 kiriman/menit/IP).
- Honeypot: tambahkan satu `<input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">` di dalam form kontak — **ini satu-satunya perubahan Blade publik yang diizinkan**, tidak terlihat oleh pengunjung. Controller: jika `website` terisi → pura-pura sukses, simpan dengan status `spam` (atau abaikan).
- Simpan `ip_address`. Form publik saat ini hanya punya `name`, `email`, `subject`, `message` — kolom `phone`/`company` diisi manual oleh owner di admin setelah menghubungi lead. **Jangan menambah field baru ke tampilan** — kalau Erlan ingin field HP di form, itu keputusan desain terpisah (tanya dulu).

## 4. Perbaikan Galeri
- Root cause `file_path` tersimpan sebagai JSON: pastikan `FileUpload` di `GalleryForm` **tidak** `->multiple()` dan state berupa string. Buat command sekali jalan `gallery:normalize-paths` untuk membersihkan data lama; setelah itu accessor `getFilePathAttribute` bisa dihapus.
- `getFileUrlAttribute` pakai `asset('storage/'.$path)` (sesuai temuan sebelumnya bahwa ini lebih andal di setup ini).
- `galeri.blade.php` memakai `$gallery->caption` yang tidak ada di tabel. Solusi **tanpa ubah Blade**: tambahkan accessor `getCaptionAttribute()` di model `Gallery` yang mengembalikan `description`.
- Validasi upload: `image()` / `acceptedFileTypes(['image/jpeg','image/png','image/webp','video/mp4'])`, `maxSize` (gambar 5 MB, video 50 MB).
- Tabel galeri: `reorderable('order')` agar urutan bisa di-drag.

## 5. Katalog Produk (ringan)
- `ProductForm`: slug otomatis dari nama (`afterStateUpdated` + `Str::slug`, unique ignoreRecord), label Bahasa Indonesia.
- Tidak menambah field baru ke tampilan publik di PRD ini.

## Acceptance criteria
- [ ] Mengubah "Hero Title" di Pengaturan Website langsung terlihat di beranda (cache ter-reset). Tidak ada tombol hapus/tambah key.
- [ ] 6 kiriman form kontak dalam 1 menit → kiriman ke-6 kena 429. Honeypot terisi → tidak masuk sebagai lead `new`.
- [ ] "Jadikan Pembeli" membuat Buyer dan menautkan pesan.
- [ ] Halaman publik terlihat identik sebelum/sesudah (bandingkan screenshot desktop & mobile semua 5 halaman).
- [ ] Staff tidak melihat menu grup Website.

## Test
`ContactFormTest` (throttle, honeypot, validasi), `SiteSettingsPageTest` (update value + cache), `LeadConversionTest`, `GalleryCaptionTest`.

## Rencana commit
`feat(cms): settings page replaces key-value resource` → `feat(leads): lead status and buyer conversion` → `fix(contact): throttle and honeypot` → `fix(gallery): normalize file paths and caption accessor` → `feat(products): auto slug` → `test(cms): ...`
