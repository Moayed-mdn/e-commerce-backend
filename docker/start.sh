#!/bin/bash

# Substitute PORT variable in nginx config
export PORT=${PORT:-80}

# Remove any leftover default configs
rm -f /etc/nginx/sites-enabled/*
rm -f /etc/nginx/conf.d/default.conf

# Generate our config
envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/app.conf

# Fix permissions (MUST run before anything else)
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Cache config and routes
php artisan config:cache
php artisan route:cache

# Seed only if database is empty (prevents duplicates)
php artisan tinker --execute="if(DB::table('categories')->count() === 0) { Artisan::call('db:seed', ['--force' => true]); echo 'Seeded!'; } else { echo 'Already seeded, skipping.'; }"

# Show Laravel logs in Railway
touch /var/www/storage/logs/laravel.log
chown www-data:www-data /var/www/storage/logs/laravel.log
tail -f /var/www/storage/logs/laravel.log &

# Start Supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf