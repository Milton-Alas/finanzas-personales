FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    postgresql-dev \
    icu-dev \
    supervisor \
    bash

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql bcmath intl zip opcache gd exif

# Configure Nginx
COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf
RUN mkdir -p /run/nginx

# Configure PHP
COPY ./docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN npm install
RUN npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Start command
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
