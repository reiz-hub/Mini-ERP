#!/bin/bash
set -e

echo "⏳ Waiting for MySQL to be ready..."
until mysqladmin ping --skip-ssl -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
    echo "   MySQL is not ready yet. Retrying in 3s..."
    sleep 3
done
echo "✅ MySQL is ready!"

if [ -n "$RABBITMQ_HOST" ]; then
    echo "⏳ Waiting for RabbitMQ to be ready on $RABBITMQ_HOST:$RABBITMQ_PORT..."
    # Simple wait using netcat or bash dev/tcp
    until (echo > /dev/tcp/$RABBITMQ_HOST/$RABBITMQ_PORT) >/dev/null 2>&1; do
        echo "   RabbitMQ is not ready yet. Retrying in 3s..."
        sleep 3
    done
    echo "✅ RabbitMQ is ready!"
fi

if [ -n "$REDIS_HOST" ]; then
    echo "⏳ Waiting for Redis to be ready on $REDIS_HOST:$REDIS_PORT..."
    until (echo > /dev/tcp/$REDIS_HOST/$REDIS_PORT) >/dev/null 2>&1; do
        echo "   Redis is not ready yet. Retrying in 3s..."
        sleep 3
    done
    echo "✅ Redis is ready!"
fi

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Run migrations (safe to run concurrently but idempotent)
echo "🔄 Running database migrations..."
php artisan migrate --force

# Check if migrations actually ran by checking a table we know we need
# Run seeders only if they haven't been run yet to prevent duplicates
echo "🌱 Running database seeders idempotently..."
# Using artisan command to check if DatabaseSeeder was run or if users table has records
if php artisan tinker --execute="echo App\Models\User::count();" | grep -q "^0$"; then
    echo "   Database seems empty. Running seeders..."
    php artisan db:seed --force 2>/dev/null || true
else
    echo "   Database already seeded. Skipping seeders."
fi

echo "🚀 Starting application..."
exec "$@"
