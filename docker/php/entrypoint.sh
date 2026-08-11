#!/bin/bash
set -e

cd /var/www

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"

echo "==> Waiting for database at ${DB_HOST}:${DB_PORT}..."
until php -r "exit(@fsockopen('${DB_HOST}', ${DB_PORT}) ? 0 : 1);" >/dev/null 2>&1; do
    sleep 2
done
echo "==> Database is reachable."

if [ ! -f .env ] && [ -f .env.example ]; then
    echo "==> .env not found, copying from .env.example"
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "==> Generating APP_KEY"
    php artisan key:generate --force
fi

# Bersihkan cache lebih awal supaya Blade/controller versi lama tidak dipakai.
php artisan optimize:clear || true

if [ "${SKIP_MIGRATIONS:-false}" != "true" ]; then
    echo "==> Running migrations"
    php artisan migrate --force
fi

if [ "${DB_SEED:-false}" = "true" ]; then
    echo "==> Seeding database"
    php artisan db:seed --force
fi

# Beberapa arsip lama memiliki public/storage sebagai file kosong. Kondisi itu
# membuat link /storage rusak. Hapus file biasa dan buat symlink yang benar.
if [ -e public/storage ] && [ ! -L public/storage ]; then
    echo "==> Removing invalid public/storage file"
    rm -f public/storage
fi

if [ ! -L public/storage ]; then
    echo "==> Linking storage"
    php artisan storage:link --force
fi

php artisan optimize:clear || true

echo "==> Ready. Starting: $*"
exec "$@"
