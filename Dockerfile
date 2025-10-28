FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip git curl \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip mbstring exif pcntl bcmath gd \
    && a2enmod rewrite

# Configurar Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Crear usuario y grupo correctos para Render
RUN groupadd -g 1000 www-user && \
    useradd -u 1000 -ms /bin/bash -g www-user www-user

# Crear estructura de directorios con usuario correcto
RUN mkdir -p /var/www/html && chown -R www-user:www-user /var/www/html

# Cambiar a usuario no-root
USER www-user

WORKDIR /var/www/html

# Copiar aplicación con permisos correctos
COPY --chown=www-user:www-user . .

# Cambiar temporalmente a root para instalar Composer
USER root

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Script de inicio
COPY render-start.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/render-start.sh

# Volver al usuario no-root
USER www-user

CMD ["/usr/local/bin/render-start.sh"]