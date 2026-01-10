# Dockerfile

FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    unzip git curl libpq-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy project files and set ownership to www-data
COPY --chown=www-data:www-data . /var/www/symfony

# Install Symfony dependencies (run as root)
USER root
RUN composer install --optimize-autoloader

# Ensure the var directory has correct ownership and permissions
RUN chown -R www-data:www-data /var/www/symfony/var
RUN chmod -R 775 /var/www/symfony/var

# Switch back to www-data after install
USER www-data
