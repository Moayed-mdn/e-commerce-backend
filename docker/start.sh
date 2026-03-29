#!/bin/bash

# Substitute PORT variable in nginx config
export PORT=${PORT:-80}

# Remove any leftover default configs
rm -f /etc/nginx/sites-enabled/*
rm -f /etc/nginx/conf.d/default.conf

# Generate our config
envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/app.conf

# Run migrations
php artisan migrate:refresh --force

# Create storage link (must be here, not pre-deploy)
php artisan storage:link

# Cache config and routes
php artisan config:cache
php artisan route:cache

# Seed ONLY if database is empty (prevents duplicates on redeploy)
php artisan db:seed --force

# Show Laravel logs in Railway
touch /var/www/storage/logs/laravel.log
tail -f /var/www/storage/logs/laravel.log &

# Start Supervisor (nginx + php-fpm + queue worker)
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf