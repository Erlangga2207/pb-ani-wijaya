#!/usr/bin/env bash
# Langkah setelah kode terbaru ditarik oleh fitur Git hPanel (PRD-09). Dijalankan di server lewat SSH:
#   bash ~/domains/aniwijaya.com/public_html/deploy/deploy.sh
# Tidak ada git pull & tidak ada salin asset: hPanel → Git sudah menaruh seluruh repo di public_html.
# Deploy PERTAMA: ikuti deploy/FIRST_DEPLOY.md.
set -euo pipefail

# Bisa diganti lewat environment, mis.: PHP_BIN=/opt/alt/php83/usr/bin/php bash deploy.sh
APP_DIR="${APP_DIR:-$HOME/domains/aniwijaya.com/public_html}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer2}"   # fallback ke "composer" kalau composer2 tidak ada
ENV_FILE="$(dirname "$APP_DIR")/.env.pb-ani-wijaya" # di luar public_html (dibaca bootstrap/app.php)

fail() { echo "GAGAL: $*" >&2; exit 1; }
step() { echo; echo "==> $*"; }

# --- Cek awal (sebelum website diturunkan) ---------------------------------
"$PHP_BIN" -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);' \
    || fail "PHP CLI harus versi 8.3 atau lebih baru (sekarang: $("$PHP_BIN" -r 'echo PHP_VERSION;')). Atur PHP_BIN ke path PHP 8.3."

if ! command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
    command -v composer >/dev/null 2>&1 || fail "composer2 / composer tidak ditemukan."
    COMPOSER_BIN=composer
fi

[ -f "$APP_DIR/artisan" ] || fail "$APP_DIR/artisan tidak ada. Repo belum di-clone lewat hPanel → Git (lihat deploy/FIRST_DEPLOY.md)."
[ -f "$ENV_FILE" ] || fail "$ENV_FILE tidak ada. Buat dari .env.production.example (lihat deploy/FIRST_DEPLOY.md)."

cd "$APP_DIR"

# --- Install dependency dulu: artisan butuh vendor/ (deploy pertama / composer.lock berubah) ---
step "Install dependency PHP ($COMPOSER_BIN)"
"$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction

# --- Mode perawatan: website selalu dinaikkan lagi, berhasil maupun gagal ----
step "Mode perawatan (artisan down)"
"$PHP_BIN" artisan down --retry=60
trap '"$PHP_BIN" artisan up >/dev/null 2>&1 || true; [ "${DEPLOY_OK:-0}" = 1 ] || echo "Deploy GAGAL — website sudah dinaikkan lagi (artisan up). Periksa pesan di atas." >&2' EXIT

step "Migrasi database"
# Buang config cache lama dulu, supaya migrasi memakai config & .env terbaru.
"$PHP_BIN" artisan config:clear
"$PHP_BIN" artisan migrate --force

step "Bersihkan & bangun ulang cache (optimize)"
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan optimize

step "Website dinaikkan (artisan up)"
"$PHP_BIN" artisan up
DEPLOY_OK=1

echo
if command -v git >/dev/null 2>&1 && [ -d .git ]; then
    echo "Deploy selesai: $(git log -1 --format='%h %s (%cd)' --date=format:'%Y-%m-%d %H:%M')"
else
    echo "Deploy selesai."
fi
