FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear directorio de la app
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Asignar permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Puerto para Laravel (Render expone 8080)
EXPOSE 8080

# Comando para iniciar el servidor PHP
CMD php -S 0.0.0.0:8080 -t public
