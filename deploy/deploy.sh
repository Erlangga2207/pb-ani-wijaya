#!/usr/bin/env bash
# Update website aniwijaya.com di Hostinger (PRD-09). Dijalankan di server lewat SSH:
#   bash ~/domains/aniwijaya.com/pb-ani-wijaya/deploy/deploy.sh
# Deploy PERTAMA tidak memakai skrip ini — ikuti deploy/FIRST_DEPLOY.md.
set -euo pipefail

# Bisa diganti lewat environment, mis.: PHP_BIN=/opt/alt/php83/usr/bin/php bash deploy.sh
APP_DIR="${APP_DIR:-$HOME/domains/aniwijaya.com/pb-ani-wijaya}"
PUBLIC_DIR="${PUBLIC_DIR:-$HOME/domains/aniwijaya.com/public_html}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer2}"   # fallback ke "composer" kalau composer2 tidak ada

fail() { echo "GAGAL: $*" >&2; exit 1; }
step() { echo; echo "==> $*"; }

# --- Cek awal (sebelum website diturunkan) ---------------------------------
"$PHP_BIN" -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);' \
    || fail "PHP CLI harus versi 8.3 atau lebih baru (sekarang: $("$PHP_BIN" -r 'echo PHP_VERSION;')). Atur PHP_BIN ke path PHP 8.3."

if ! command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
    command -v composer >/dev/null 2>&1 || fail "composer2 / composer tidak ditemukan."
    COMPOSER_BIN=composer
fi

[ -f "$APP_DIR/.env" ] || fail "$APP_DIR/.env tidak ada. Deploy pertama belum dilakukan (lihat deploy/FIRST_DEPLOY.md)."
[ -f "$PUBLIC_DIR/index.php" ] || fail "$PUBLIC_DIR/index.php tidak ada. Salin deploy/public_html/index.php dulu (deploy/FIRST_DEPLOY.md)."

cd "$APP_DIR"

# --- Mode perawatan: website selalu dinaikkan lagi, berhasil maupun gagal ----
step "Mode perawatan (artisan down)"
"$PHP_BIN" artisan down --retry=60
trap '"$PHP_BIN" artisan up >/dev/null 2>&1 || true; [ "${DEPLOY_OK:-0}" = 1 ] || echo "Deploy GAGAL — website sudah dinaikkan lagi (artisan up). Periksa pesan di atas." >&2' EXIT

step "Ambil kode terbaru (git pull --ff-only)"
git pull --ff-only origin main

step "Install dependency PHP ($COMPOSER_BIN)"
"$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction

step "Migrasi database"
"$PHP_BIN" artisan migrate --force

# index.php & .htaccess di public_html adalah versi server (deploy/public_html/);
# public_html/storage adalah symlink ke storage/app/public — tidak pernah ditimpa atau dihapus.
step "Salin public/ ke $PUBLIC_DIR (kecuali index.php, .htaccess, storage)"
if command -v rsync >/dev/null 2>&1; then
    rsync -a --exclude='/index.php' --exclude='/.htaccess' --exclude='/storage' public/ "$PUBLIC_DIR/"
else
    shopt -s dotglob nullglob
    for item in public/*; do
        case "$(basename "$item")" in
            index.php | .htaccess | storage) continue ;;
        esac
        cp -r "$item" "$PUBLIC_DIR/"
    done
    shopt -u dotglob nullglob
fi

step "Bersihkan & bangun ulang cache (optimize)"
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan optimize

step "Website dinaikkan (artisan up)"
"$PHP_BIN" artisan up
DEPLOY_OK=1

echo
echo "Deploy selesai: $(git log -1 --format='%h %s (%cd)' --date=format:'%Y-%m-%d %H:%M')"
