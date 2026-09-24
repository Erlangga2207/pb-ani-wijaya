# Analisis Admin Panel — PB. Ani Wijaya (v2)

> Sumber: repo `Erlangga2207/pb-ani-wijaya` (commit `a99e770`) + Laporan Analisis Mitra (wawancara pemilik).
> Keputusan dari Erlan (24 Sep 2026): pengguna = Owner + 1–2 staff · perlu catat piutang/utang · hasil samping (menir, dedak, sekam) dicatat & dijual · lokasi = Indramayu.
> **Desain company profile (halaman publik) TIDAK diubah.**

---

## 1. Ringkasan

Admin panel yang sekarang adalah **aplikasi stok generik** (barang masuk / barang keluar). Kodenya rapi untuk level itu, tapi **model bisnisnya belum cocok** dengan pabrik penggilingan:

| Kebutuhan pemilik (PDF) | Kondisi sekarang | Status |
|---|---|---|
| Catat pembelian gabah dari petani | Bisa lewat "Barang Masuk", tapi gabah & beras dianggap produk yang sama jenisnya | ⚠️ sebagian |
| **Hitung rendemen** (gabah → beras) | Tidak ada konsep produksi sama sekali | ❌ |
| Stok gabah & beras terpisah + batas minimum | Stok per produk ada, batas minimum di-hardcode `500` kg | ⚠️ sebagian |
| Penjualan per karung/eceran + nota | Ada, tapi 1 transaksi = 1 produk, nomor nota manual | ⚠️ sebagian |
| Laporan omzet & laba rugi kapan saja | Tidak ada (widget cuma total kg bulan ini) | ❌ |
| Menelusuri **selisih stok** | Transaksi bisa diedit/dihapus bebas → jejak hilang | ❌ (malah memperparah) |
| Piutang pembeli / utang ke petani | Tidak ada | ❌ |

Kesimpulan: **bukan tambal-sulam, tapi re-desain modul transaksi**. Bagian lain (User, Supplier, Buyer, Gallery, Site Settings, Contact) cukup diperbaiki.

---

## 2. Temuan per Prioritas

### 🔴 Critical — harus diperbaiki

**C1. Model data tidak mengenal proses produksi.**
Tabel `stock_transactions` cuma punya `in/out`. Tidak ada tempat mencatat "hari ini giling 12 ton gabah → jadi 6,6 ton beras + 1 ton menir + ...". Akibatnya rendemen (nilai pembeda utama project ini) tidak bisa dihitung.
→ Solusi: pisah menjadi dokumen bisnis **Pembelian**, **Produksi**, **Penjualan**, **Penyesuaian Stok**, yang semuanya menulis ke satu **buku besar stok (`stock_movements`)**. *(PRD-02 s/d PRD-05)*

**C2. Transaksi bisa diedit & dihapus (termasuk bulk delete).**
Ini persis penyebab masalah "selisih stok sulit ditelusuri" di PDF. Kalau staff salah input lalu menghapus, jejaknya hilang.
→ Solusi: dokumen yang sudah disimpan **tidak bisa diedit/dihapus**, hanya bisa **dibatalkan (void)** dengan alasan, dan pembatalan membuat mutasi balik. Selisih fisik dicatat lewat **Stock Opname**.

**C3. Stok boleh minus.**
Tidak ada validasi `jumlah keluar ≤ stok`. Dua input bersamaan juga bisa lolos (race condition).
→ Solusi: semua mutasi lewat `StockService` di dalam `DB::transaction()` + `lockForUpdate()`.

**C4. `User` belum implement `FilamentUser`.**
Dokumentasi Filament: di environment **non-local**, user wajib implement `FilamentUser::canAccessPanel()`. Tanpa ini, setelah deploy ke Hostinger **tidak ada yang bisa login** (403).

**C5. Privilege escalation di menu Pengguna.**
Role `admin` bisa memberi role `super_admin` ke siapa saja, dan bisa mengedit/ganti password akun `super_admin`. Select role juga `multiple()` sehingga satu user bisa punya role bertabrakan.

### 🟠 Important — sebaiknya diperbaiki

- **I1. Tidak ada HPP → laba rugi mustahil dihitung.** Perlu biaya rata-rata (moving average) gabah dan beras. *(PRD-02, 04)*
- **I2. Nomor nota manual** (`reference_number` opsional) → bisa dobel/kosong. Perlu penomoran otomatis `PJ-20260924-0001`. *(PRD-01)*
- **I3. Penjualan hanya 1 produk per nota.** Pembeli bisa beli beras ketan + menir sekaligus → perlu header + item. *(PRD-05)*
- **I4. Tidak ada piutang/utang.** *(PRD-03, 05)*
- **I5. Site Settings berupa CRUD key-value bebas.** Admin bisa hapus/rename key yang dipakai frontend → halaman publik rusak diam-diam. Ganti jadi **halaman Pengaturan** (form tetap, tanpa create/delete). *(PRD-07)*
- **I6. Form kontak tanpa rate-limit/anti-spam.** Website promosi publik pasti kena spam bot. *(PRD-07)*
- **I7. Seeder tidak membuat role & tidak memanggil `SiteSettingSeeder`.** Setup di server baru jadi manual & rawan lupa. *(PRD-01)*
- **I8. Tidak ada test sama sekali.** Logika uang & stok wajib punya test. *(tiap PRD)*

### 🟡 Improvement

- Batas stok minimum per item (bukan hardcode 500).
- Supplier (petani) tambah **desa/kecamatan** → bisa lihat desa mana pemasok terbesar (membantu masalah "cari gabah ke desa jauh").
- Buyer tambah **kota/provinsi/negara** → mendukung target pasar nasional & global.
- Pesan kontak dijadikan **lead** (status: baru → dihubungi → deal / spam) + tombol "Jadikan Pembeli".
- Log mesin macet (menit downtime) per batch produksi.
- Branding panel: warna Amber default → palet Modern Heritage (merah/emas), locale `id`, timezone `Asia/Jakarta`.
- Bug kecil: `galeri.blade.php` memanggil `$gallery->caption` padahal kolomnya `description`; accessor `file_path` di `Gallery` menambal data JSON — akar masalahnya di `FileUpload` config.
- `UserForm` helper bilang "minimal 8 karakter" tapi tidak ada `->minLength(8)`.
- Git: repo cuma 1 commit "first commit". Mulai sekarang 1 PRD = 1 branch = beberapa commit bermakna.

### 🟢 Sudah baik (pertahankan)

- Satuan disimpan kanonik dalam **kg** + simpan input asli (`input_quantity`, `input_unit`) — keputusan bagus, dipakai lagi di v2.
- FK pakai `restrictOnDelete` → master data tidak bisa dihapus kalau sudah dipakai transaksi.
- `SiteSetting::get()` dengan cache + auto-forget saat disimpan.
- Struktur Filament v5 terpisah (`Schemas/`, `Tables/`, `Pages/`) — konsisten.
- Nota diproteksi `auth` + role, eager load untuk hindari N+1.
- `user_id` pencatat transaksi sudah ada (audit dasar).

---

## 3. Desain Baru (gambaran besar)

```
             ┌────────────┐     ┌─────────────┐     ┌────────────┐
 Petani ───▶ │ PEMBELIAN  │ ──▶ │  PRODUKSI   │ ──▶ │ PENJUALAN  │ ───▶ Pembeli
 (Supplier)  │  gabah     │     │ (giling)    │     │ beras/menir│      (Buyer)
             └─────┬──────┘     └──────┬──────┘     └─────┬──────┘
                   │ +gabah     -gabah │ +beras,+menir,   │ -beras / -menir
                   │                   │  +dedak,+sekam   │
                   ▼                   ▼                  ▼
             ┌──────────────────────────────────────────────────┐
             │   stock_movements  (buku besar stok, append-only) │ ◀── Stock Opname (selisih)
             └──────────────────────────────────────────────────┘
                   │                                      │
             Utang ke petani (payments)          Piutang pembeli (payments)
                                    ▼
                  Laporan: Stok · Rendemen · Omzet · Laba Rugi · Utang/Piutang
```

**Rumus inti**

- Rendemen beras = `beras_kg ÷ gabah_kg × 100%` (target pemilik 50–60%).
- Susut = `gabah_kg − Σ semua output_kg`.
- HPP beras/kg = `(gabah_kg × biaya rata-rata gabah) ÷ beras_kg`. Hasil samping dianggap **by-product** (HPP = 0, penjualannya tetap masuk omzet). Ini penyederhanaan akuntansi yang wajar untuk UMKM — lebih akurat daripada tidak ada HPP sama sekali.
- Laba kotor = Penjualan − HPP terjual. Laba bersih = Laba kotor − Biaya operasional (gaji, listrik, solar, servis mesin, dll).

**Peran**

| Role | Bisa |
|---|---|
| `super_admin` | Semua (akun developer / Erlan) |
| `owner` | Semua data + laporan keuangan + void + kelola staff. **Tidak** bisa menyentuh akun super_admin |
| `staff` | Input pembelian, produksi, penjualan, pembayaran. Lihat stok. **Tidak** bisa void, lihat HPP/laba, laporan keuangan, biaya, pengguna, CMS |

---

## 4. Urutan Pengerjaan

| # | File | Isi | Estimasi |
|---|---|---|---|
| 1 | `PRD-01-fondasi-keamanan.md` | FilamentUser, role owner/staff, seeder, penomoran dokumen, enum, branding panel | 1–2 hari |
| 2 | `PRD-02-master-data-stok.md` | Items (gabah, beras, menir, dedak, sekam), perbaikan Supplier/Buyer, `StockService`, stock opname | 2–3 hari |
| 3 | `PRD-03-pembelian-gabah.md` | Pembelian + utang petani + pembayaran | 2 hari |
| 4 | `PRD-04-produksi-rendemen.md` | Batch giling, output multi-item, rendemen, HPP | 2–3 hari |
| 5 | `PRD-05-penjualan-nota-piutang.md` | Penjualan multi-item, nota baru, piutang | 2–3 hari |
| 6 | `PRD-06-biaya-laporan-dashboard.md` | Biaya operasional, laporan, dashboard | 3 hari |
| 7 | `PRD-07-cms-leads.md` | Halaman Pengaturan, lead dari form kontak, anti-spam, fix galeri | 1–2 hari |

PRD-07 tidak bergantung pada yang lain — bisa dikerjakan kapan saja.

---

## 5. Hal yang Perlu Dikonfirmasi Nanti

1. **Data lama di `stock_transactions`** — asumsi saya data dummy (web belum live). PRD-02 menyimpan tabel lama sebagai read-only dulu, baru di-drop setelah kamu konfirmasi.
2. **Satu jenis beras ketan saja?** Asumsi: 1 item "Beras Ketan Putih" (Grade A semua, sesuai PDF). Kalau nanti ada ketan hitam, tinggal tambah item.
3. **Harga beli dari petani per kg gabah kering atau basah?** Asumsi: per kg timbangan saat beli; kadar air dicatat opsional.
