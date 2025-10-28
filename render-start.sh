#!/bin/bash
set -e

cd /var/www/html

echo "Iniciando aplicación Laravel en Render..."

# SOLO si NO existe .env (evita sobrescribir en producción)
if [ ! -f .env ]; then
    echo "Creando .env de respaldo..."
    cp .env.example .env
else
    echo ".env ya existe, saltando creación."
fi

# PERMISOS: SOLO storage y bootstrap/cache
echo "Aplicando permisos correctos..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Instalar dependencias (solo si no están)
if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Migraciones
php artisan migrate --force

# Cachear (producción)
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Iniciando Apache..."
exec apache2-foreground