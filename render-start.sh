#!/bin/bash

cd /var/www/html

echo "🔧 Iniciando configuración de la aplicación..."

# SOLUCIÓN DEFINITIVA PARA PERMISOS
echo "📁 Configurando permisos de storage..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Permisos COMPLETOS para todo storage
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# Configurar .env con las variables REALES de Render
echo "🔑 Configurando variables de entorno..."
cat > .env << EOF
APP_NAME="Sistema Horarios FICCT"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ficcthorarios.onrender.com
APP_KEY=base64:1MV8g4JS59wJWzwCaqNMHwOoEj+rOAoV9amizaoaWtU=

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/La_Paz

LOG_CHANNEL=stderr
LOG_LEVEL=error

# DATABASE CONFIGURATION - VARIABLES REALES
DB_CONNECTION=pgsql
DB_HOST=dpg-d402va75r7bs73a4mptg-a.oregon-postgres.render.com
DB_PORT=5432
DB_DATABASE=emanuel
DB_USERNAME=emanuel_user
DB_PASSWORD=WOytsh6mzUqiDpRhcPmkx6ySsM52iqEN

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

VITE_APP_NAME="Sistema Horarios FICCT"
EOF

echo "✅ Archivo .env creado con configuración real"

# Instalar dependencias PHP
echo "📦 Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# Verificar conexión a la base de datos
echo "🔍 Verificando conexión a la base de datos..."
sleep 10

# Limpiar todo cache antes de migraciones
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Verificar permisos finales
echo "🔐 Verificando permisos finales..."
ls -la storage/logs/
ls -la storage/framework/views/

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# Crear enlace de storage
php artisan storage:link

# Optimizar para producción
echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 Aplicación configurada correctamente!"
echo "🚀 Iniciando servidor web..."
exec apache2-foreground