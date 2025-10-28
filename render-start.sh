#!/bin/bash

cd /var/www/html

echo "🔧 INICIANDO CONFIGURACIÓN EN RENDER..."

# Configurar permisos de manera segura
echo "📁 Configurando permisos..."
sudo chown -R www-user:www-user /var/www/html
find /var/www/html -type f -exec chmod 644 {} \;
find /var/www/html -type d -exec chmod 755 {} \;

# Permisos específicos para storage y bootstrap
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Verificar que somos el usuario correcto
echo "👤 Usuario actual: $(whoami)"
echo "📋 Permisos de storage:"
ls -la storage/

# Crear .env definitivo
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

# SOLUCIÓN CRÍTICA: Usar stderr para evitar problemas de permisos
LOG_CHANNEL=stderr
LOG_LEVEL=error

# CONFIGURACIÓN REAL DE BASE DE DATOS
DB_CONNECTION=pgsql
DB_HOST=dpg-d402va75r7bs73a4mptg-a
DB_PORT=5432
DB_DATABASE=emanuel
DB_USERNAME=emanuel_user
DB_PASSWORD=WOytsh6mzUqiDpRhcPmkx6ySsM52iqEN

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_DRIVER=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

VITE_APP_NAME="Sistema Horarios FICCT"
EOF

echo "✅ Archivo .env creado"

# Instalar dependencias
echo "📦 Instalando dependencias..."
composer install --no-dev --optimize-autoloader --no-interaction

# Limpiar cachés completamente
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Verificar conexión a la base de datos
echo "🔍 Verificando conexión a PostgreSQL..."
for i in {1..30}; do
    if php -r "
    try {
        \$pdo = new PDO('pgsql:host=dpg-d402va75r7bs73a4mptg-a;port=5432;dbname=emanuel', 'emanuel_user', 'WOytsh6mzUqiDpRhcPmkx6ySsM52iqEN');
        echo '✅ Conexión a PostgreSQL exitosa';
        exit(0);
    } catch (Exception \$e) {
        echo '⏳ Intento $i: Esperando PostgreSQL... ' . \$e->getMessage() . PHP_EOL;
        exit(1);
    }
    "; then
        break
    else
        sleep 2
    fi
done

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# Optimizar aplicación
echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎯 CONFIGURACIÓN COMPLETADA"
echo "🌐 Iniciando servidor web..."

# Ejecutar Apache en primer plano
exec sudo apache2-foreground