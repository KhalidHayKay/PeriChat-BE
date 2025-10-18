#!/bin/sh

# Exit immediately on error
set -e

# Increase file descriptor limit
ulimit -n 10000 2>/dev/null || echo "Could not set ulimit (requires privileged mode)"

echo "Running migrations against DB..."
php artisan migrate --force

echo "Clearing and caching config..."
php artisan config:cache
php artisan route:cache

echo "Starting Supervisor (manages PHP-FPM, Nginx, Reverb, Queue)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
