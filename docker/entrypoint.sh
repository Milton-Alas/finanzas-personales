#!/bin/bash
set -e

echo "🚀 Iniciando aplicación..."

# Directorios
mkdir -p storage/{framework/{sessions,views,cache},logs} bootstrap/cache /var/run

# Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Cache
php artisan optimize:clear 2>/dev/null || true

# Base de datos
php artisan migrate --force --no-interaction
php artisan db:seed --class=Database\\Seeders\\ExpenseCategorySeeder --force 2>/dev/null || true

# Optimización
php artisan optimize
php artisan filament:optimize
php artisan storage:link --force 2>/dev/null || true

# Iniciar servicios
echo "🐘 PHP-FPM iniciando..."
php-fpm -D

echo "🌐 Nginx iniciando..."
echo "✅ ¡Listo!"

# Mantener contenedor vivo
exec nginx -g "daemon off;"