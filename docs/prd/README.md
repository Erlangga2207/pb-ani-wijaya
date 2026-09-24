# PRD pb-ani-wijaya v2 — Cara Pakai

## Isi
| File | Untuk |
|---|---|
| `00-ANALISIS-ADMIN-PANEL.md` | Kamu baca dulu — temuan, alasan, desain baru |
| `CLAUDE.md` | Taruh di **root repo**. Aturan yang otomatis dibaca Claude Code tiap sesi |
| `PRD-01` … `PRD-07` | Taruh di `docs/prd/`. Dikerjakan **satu per satu, berurutan** |

## Setup sekali
```bash
cd C:\laragon\www\pb-ani-wijaya
git checkout -b chore/add-prd-docs
# salin CLAUDE.md ke root, PRD & analisis ke docs/prd/
git add CLAUDE.md docs/prd
git commit -m "docs: add v2 admin panel analysis and PRDs"
git checkout main && git merge chore/add-prd-docs
```

## Prompt untuk Claude Code (per PRD)
Masuk plan mode dulu (Shift+Tab), lalu:

```
Baca CLAUDE.md dan docs/prd/PRD-01-fondasi-keamanan.md.
Buat branch feature/prd-01-foundation dari main.
Tunjukkan rencana: daftar file yang dibuat/diubah dan urutan commit.
Jangan mulai coding sebelum aku setujui rencananya.
```

Setelah rencana disetujui → biarkan dia mengerjakan → cek hasil:
1. `php artisan migrate:fresh --seed` sukses
2. `php artisan test` hijau
3. Coba manual di browser pakai checklist **Acceptance criteria** di PRD
4. Baru merge ke `main`

## Tips
- Jangan gabung dua PRD dalam satu sesi — konteks jadi terlalu panjang dan kualitas turun.
- Kalau Claude Code bilang "sudah selesai" tapi test belum dijalankan, minta jalankan dulu.
- Setelah PRD-05, Claude Code akan **bertanya** sebelum menghapus tabel `stock_transactions` lama.
- Pahami tiap service yang dibuat (terutama `StockService` dan rumus HPP) — itu inti sistemnya, dan pasti ditanya kalau kamu presentasikan project ini.
