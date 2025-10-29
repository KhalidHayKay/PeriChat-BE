FROM php:8.4-fpm-alpine

# Update & install system deps
RUN apk update && apk add --no-cache \
    git \
    zip \
    unzip \
    curl \
    libpng-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    bash \
    mysql-client \
    postgresql-client \
    postgresql-dev \
    nginx \
    supervisor

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql intl mbstring xml

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files (layer caching)
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the source
COPY . .

# Permissions
RUN chown -R www-data:www-data . && chmod -R 755 .

# Ensure nginx has proper writable directories
RUN mkdir -p /var/lib/nginx/tmp /var/tmp/nginx /var/log/nginx && \
    chown -R www-data:www-data /var/lib/nginx /var/tmp/nginx /var/log/nginx && \
    chmod -R 755 /var/lib/nginx /var/tmp/nginx /var/log/nginx

# Artisan optimizations
RUN composer run-script post-autoload-dump \
    && php artisan config:clear \
    && php artisan route:clear

# Copy custom PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/uploads.ini

# Copy nginx config
COPY docker/nginx/production.conf /etc/nginx/nginx.conf

# Copy entrypoint to start both php-fpm and nginx
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
