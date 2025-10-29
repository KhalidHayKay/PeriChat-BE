#!/bin/sh

# Exit immediately on error
set -e

echo "Running migrations against DB..."
php artisan migrate:fresh --force

echo "Create storage symlink..."
php artisan storage:link

echo "Clearing and caching config..."
php artisan config:cache
php artisan route:cache

echo "=== PHP Upload Settings ==="
php -i | grep upload_max_filesize
php -i | grep post_max_size
echo "==========================="

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"
