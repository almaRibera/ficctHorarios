#!/bin/bash

cd /var/www/html

echo "🔧 INICIANDO CONFIGURACIÓN..."

# Permisos básicos
chmod -R 755 storage bootstrap/cache



# Instalar dependencias
composer install --no-dev --optimize-autoloader --no-interaction

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Esperar para BD
echo "⏳ Esperando PostgreSQL..."
sleep 10

# Migraciones
php artisan migrate --force

# Cache para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 APLICACIÓN LISTA"
exec apache2-foreground