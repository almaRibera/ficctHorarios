#!/bin/bash

cd /var/www/html

echo "🔧 SOLUCIÓN DEFINITIVA PARA PERMISOS EN RENDER..."

# SOLUCIÓN RADICAL: Recrear toda la estructura de storage con permisos completos
echo "🗂️ Recreando estructura de storage..."
rm -rf storage/*
rm -rf bootstrap/cache/*

mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p bootstrap/cache

# PERMISOS MÁXIMOS - esto es clave para Render
chmod -R 777 storage
chmod -R 777 bootstrap/cache

# Verificar que los permisos se aplicaron
echo "🔍 Verificando permisos..."
ls -la storage/
ls -la storage/logs/
touch storage/logs/laravel.log
ls -la storage/logs/laravel.log

# Crear .env con configuración REAL
echo "🔑 Configurando entorno..."
cat > .env << EOF
APP_NAME="Sistema Horarios FICCT"
APP_ENV=production
APP_DEBUG=true
APP_URL=https://ficcthorarios.onrender.com
APP_KEY=base64:1MV8g4JS59wJWzwCaqNMHwOoEj+rOAoV9amizaoaWtU=

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/La_Paz

# SOLUCIÓN: Usar stderr para logs y evitar permisos
LOG_CHANNEL=stderr
LOG_LEVEL=debug

# DATABASE CONFIGURATION REAL
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

echo "✅ Archivo .env creado"

# Instalar dependencias
echo "📦 Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# SOLUCIÓN: Limpiar TODO el cache antes de cualquier cosa
echo "🧹 Limpiando cache profundamente..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Verificar que podemos escribir en logs
echo "📝 Probando escritura en logs..."
php -r "file_put_contents('/var/www/html/storage/logs/laravel.log', 'Test log entry\n', FILE_APPEND);"
echo "✅ Escritura en logs verificada"

# Esperar para la base de datos
echo "⏳ Esperando conexión a base de datos..."
sleep 10

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# Optimizar
echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 CONFIGURACIÓN COMPLETADA EXITOSAMENTE"
echo "🚀 Iniciando servidor web..."
exec apache2-foreground