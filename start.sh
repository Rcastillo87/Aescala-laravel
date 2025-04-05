#!/bin/bash

# Asegúrate de que el archivo de base de datos exista
if [ ! -f /app/database/database.sqlite ]; then
  #echo "Creando base de datos SQLite..."
  touch /app/database/database.sqlite
fi

# Ejecuta migraciones
#php artisan migrate --force

# Inicia el servidor
php artisan serve --host=0.0.0.0 --port=8080
