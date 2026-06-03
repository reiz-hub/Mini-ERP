#!/bin/bash
set -e

echo "☁️ Using Neon cloud database — skipping local readiness check."

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
