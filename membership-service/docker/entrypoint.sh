#!/bin/bash
set -e

echo "⏳ Waiting for MySQL to be ready..."
until mysqladmin ping --skip-ssl -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
    echo "   MySQL is not ready yet. Retrying in 3s..."
    sleep 3
done
echo "✅ MySQL is ready!"

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Run seeders (only if the database is freshly created)
echo "🌱 Running database seeders..."
php artisan db:seed --force 2>/dev/null || true

echo "🚀 Starting application..."
exec "$@"
