# Use the official PHP image as the base image
FROM php:8.2-fpm

# Set environment variable for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd mbstring pdo pdo_mysql zip exif pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files into container
COPY . /var/www

# Set permissions for storage and cache directories of Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Install PHP dependencies
RUN composer install

# Expose port 9000 for PHP-FPM connection
EXPOSE 9000

# Entry point
CMD ["php-fpm"]
