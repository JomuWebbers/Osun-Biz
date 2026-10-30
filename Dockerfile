FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY . /var/www/html

# Install composer dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Expose port 8000 for PHP built-in server (or Apache will serve from public/)
EXPOSE 8000

# Start Apache
CMD ["apache2-foreground"]