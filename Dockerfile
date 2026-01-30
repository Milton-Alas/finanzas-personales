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
    bash \
    shadow # Necesario para usermod/groupmod

# Sincronizar usuarios: Configurar Nginx para que corra como www-data
# y asegurar que el ID de www-data sea 1000 (estándar de Docker)
RUN sed -i 's/user nginx;/user www-data;/g' /etc/nginx/nginx.conf && \
    usermod -u 1000 www-data && groupmod -g 1000 www-data

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

# Permissions: Asegurar que TODO el proyecto pertenezca a www-data (quien corre Nginx y PHP)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Start command
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]