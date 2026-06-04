#!/bin/bash
set -e

echo "☁️ Using Neon cloud database — skipping local readiness check."

# Ensure storage directories exist and have correct permissions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

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
