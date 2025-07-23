# Etapa base: PHP con extensiones necesarias
FROM php:8.2

# Instala dependencias necesarias para Laravel + Vite
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    mariadb-client \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Copia Composer desde imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define el directorio de trabajo
WORKDIR /var/www

# Copia todos los archivos del proyecto
COPY . .

# Instala dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Instala y compila assets con Vite
RUN npm install && npm run build

# Da permisos a Laravel
RUN chmod -R 775 storage bootstrap/cache

# Expone el puerto que usará PHP (Render espera que escuchemos en 8080)
EXPOSE 8080

# Comando para iniciar Laravel
CMD php -S 0.0.0.0:8080 -t public
