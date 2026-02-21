#!/bin/bash

# Wait for MySQL to be ready
until php artisan db:show 2>/dev/null; do
  echo "Waiting for database connection..."
  sleep 3
done

echo "Database is ready! Running migrations..."

# Run migrations and seeders
php artisan migrate --force

# Uncomment the line below if you want to run seeders automatically
# php artisan db:seed --force

echo "Migrations completed! Starting FrankenPHP..."

# Start FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
