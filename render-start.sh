#!/bin/bash
set -e

cd /var/www/html

echo "Iniciando Laravel en Render..."

# NO copies .env → usa solo variables de entorno
# rm -f .env  # Asegúrate de que no haya .env local

# Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Composer (solo si no existe vendor)
if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Migraciones
php artisan migrate --force

# Cachear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "¡Listo! Iniciando Apache..."
exec apache2-foreground