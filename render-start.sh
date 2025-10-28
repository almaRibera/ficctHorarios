#!/bin/bash

cd /var/www/html

echo "🔧 Configurando aplicación Laravel..."

# Configurar permisos
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Configurar .env si no existe
if [ ! -f .env ]; then
    echo "📝 Creando .env desde .env.example..."
    cp .env.example .env
fi

# Instalar dependencias PHP
echo "📦 Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# Generar APP_KEY si no existe
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔐 Generando APP_KEY..."
    php artisan key:generate --force
fi

# Instalar y compilar assets si existen
if [ -f package.json ]; then
    echo "📦 Instalando dependencias Node..."
    npm ci --silent
    
    echo "🔨 Compilando assets..."
    npm run build --silent
fi

# Esperar un poco para la base de datos
echo "⏳ Esperando configuración de base de datos..."
sleep 3

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# Optimizar aplicación
echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🚀 Iniciando servidor web..."
exec apache2-foreground