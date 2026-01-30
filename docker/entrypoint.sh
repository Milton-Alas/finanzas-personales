#!/bin/bash
set -e

echo "========================================="
echo "🚀 Iniciando Laravel + Filament"
echo "========================================="

# Esperar un momento para que el sistema se estabilice
sleep 2

# Verificar directorio actual
echo "📂 Directorio actual: $(pwd)"
ls -la

# Crear directorios necesarios si no existen
echo "📁 Creando directorios..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Configurar permisos (ejecutar como root antes de cambiar a www-data)
echo "🔧 Configurando permisos..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Verificar permisos
echo "✅ Verificando permisos..."
ls -la storage/
ls -la bootstrap/cache/

# Limpiar cache anterior
echo "🧹 Limpiando cache..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# Seed de categorías
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
echo "✅ Permisos finales..."
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Crear socket directory para PHP-FPM
mkdir -p /var/run
chown www-data:www-data /var/run

# Test de escritura
echo "🧪 Test de escritura en storage..."
su -s /bin/sh www-data -c "touch /var/www/html/storage/test.txt && rm /var/www/html/storage/test.txt" || echo "⚠️ No se puede escribir en storage"

# Iniciar PHP-FPM
echo "🐘 Iniciando PHP-FPM..."
php-fpm -D

# Esperar a que PHP-FPM inicie
sleep 2

# Verificar que PHP-FPM está corriendo
if ! pgrep -x "php-fpm" > /dev/null; then
    echo "❌ Error: PHP-FPM no inició correctamente"
    exit 1
fi

echo "✅ PHP-FPM iniciado correctamente"

# Iniciar Nginx (foreground)
echo "🌐 Iniciando Nginx..."
echo "========================================="
echo "✅ Aplicación lista en puerto 80!"
echo "========================================="

# Ejecutar Nginx en foreground
exec nginx -g "daemon off;"