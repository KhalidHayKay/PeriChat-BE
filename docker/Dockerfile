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
    nginx \
    supervisor \
    libuv-dev \
    $PHPIZE_DEPS

# Install ext-uv for better Reverb performance (handles 1000+ connections)
RUN pecl install channel://pecl.php.net/uv-0.3.0 \
    && docker-php-ext-enable uv


# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl mbstring xml pcntl

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

# Artisan optimizations
RUN composer run-script post-autoload-dump \
    && php artisan config:clear \
    && php artisan route:clear

# Increase file descriptor limits for WebSocket connections
# Alpine uses ulimit in the entrypoint script instead of limits.conf
RUN echo "ulimit -n 10000" >> /etc/profile.d/ulimit.sh && \
    chmod +x /etc/profile.d/ulimit.sh

# Copy nginx config
COPY docker/nginx/production.conf /etc/nginx/nginx.conf

# Copy supervisor config
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create log directories
RUN mkdir -p /var/log/supervisor /var/log/nginx && \
    chown -R www-data:www-data /var/log/supervisor

# Copy entrypoint
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
