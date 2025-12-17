FROM php:8.2-apache

# Install dependencies for SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html/

# Create necessary directories if they don't exist and set permissions
# We need to ensure 'uploads' and 'data' are writable
RUN mkdir -p /var/www/html/uploads /var/www/html/data \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads /var/www/html/data

# Expose port 80
EXPOSE 80
