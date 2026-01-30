#!/bin/bash
set -e

echo "========================================="
echo "🚀 Iniciando Laravel + Filament"
echo "========================================="

# Configurar permisos ANTES de cualquier operación
echo "🔧 Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Crear directorios necesarios si no existen
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Limpiar cache anterior
echo "🧹 Limpiando cache..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# Seed de categorías (solo si no existen)
echo "🌱 Verificando seeders..."
php artisan db:seed --class=Database\\Seeders\\ExpenseCategorySeeder --force 2>/dev/null || echo "Seeders ya ejecutados"

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimizar Filament
echo "✨ Optimizando Filament..."
php artisan filament:optimize

# Crear storage link
echo "🔗 Creando storage link..."
php artisan storage:link --force 2>/dev/null || true

# Verificar permisos finales
echo "✅ Verificando permisos finales..."
chmod -R 775 storage bootstrap/cache

# Iniciar PHP-FPM
echo "🐘 Iniciando PHP-FPM..."
php-fpm -D

# Iniciar Nginx (foreground para mantener el contenedor vivo)
echo "🌐 Iniciando Nginx..."
echo "========================================="
echo "✅ Aplicación lista!"
echo "========================================="
nginx -g "daemon off;"