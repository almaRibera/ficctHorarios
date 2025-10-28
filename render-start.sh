#!/bin/bash

cd /var/www/html

echo "🔧 Configurando aplicación Laravel..."

# SOLUCIÓN CRÍTICA: Configurar permisos CORRECTAMENTE
echo "📁 Configurando permisos de storage..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Permisos COMPLETOS para storage (esto resuelve el error)
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# Cambiar propietario si es posible
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

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

# Verificar permisos de storage después de composer install
echo "🔍 Verificando permisos..."
ls -la storage/
ls -la storage/framework/

# Instalar y compilar assets si existen
if [ -f package.json ]; then
    echo "📦 Instalando dependencias Node..."
    npm ci --silent
    
    echo "🔨 Compilando assets..."
    npm run build --silent
fi

# Limpiar cache ANTES de migraciones
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Esperar un poco para la base de datos
echo "⏳ Esperando configuración de base de datos..."
sleep 5

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# Optimizar aplicación DESPUÉS de migraciones
echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Aplicación configurada correctamente"
echo "🚀 Iniciando servidor web..."
exec apache2-foreground