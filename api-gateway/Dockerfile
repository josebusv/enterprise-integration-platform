FROM php:8.2-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libpq-dev \
    oniguruma-dev \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --no-dev --prefer-dist

# Expose port
EXPOSE 8000

# Run Laravel built-in server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
