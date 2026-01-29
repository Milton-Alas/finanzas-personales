#!/bin/bash
set -e

# Run migrations (force since it's production)
echo "Running migrations..."
php artisan migrate --force

# Seed default expense categories (idempotent)
php artisan db:seed --class='Database\\Seeders\\ExpenseCategorySeeder' --force

# Cache optimization
echo "Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize

# Start Supervisor or processes directly
echo "Starting Nginx and PHP-FPM..."
php-fpm -D
nginx -g "daemon off;"
