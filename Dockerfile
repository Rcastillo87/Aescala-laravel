FROM php:8.2

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libpq-dev mariadb-client nodejs npm

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . /var/www
WORKDIR /var/www

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Instalar Node y compilar Vite
RUN npm install && npm run build

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# Servidor embebido
CMD php -S 0.0.0.0:8080 -t public
