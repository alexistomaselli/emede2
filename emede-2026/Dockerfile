# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos dependencias del sistema y extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite gd

# Habilitamos el módulo rewrite de Apache
RUN a2enmod rewrite

# Copiamos todos los archivos del proyecto al directorio de trabajo de Apache
COPY . /var/www/html/

# Aseguramos que el servidor web tenga permisos de escritura en las carpetas necesarias
# Esto es CRUCIAL para que SQLite y la subida de imágenes funcionen
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/db \
    && chmod -R 775 /var/www/html/uploads \
    && chmod -R 775 /var/www/html/media

# Definimos el puerto por defecto
EXPOSE 80
