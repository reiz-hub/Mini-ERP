#!/bin/bash
set -e

echo "⏳ Waiting for PostgreSQL to be ready..."
until pg_isready -h "$DB_HOST" -U "$DB_USERNAME" -q 2>/dev/null; do
    echo "   PostgreSQL is not ready yet. Retrying in 3s..."
    sleep 3
done
echo "✅ PostgreSQL is ready!"

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
