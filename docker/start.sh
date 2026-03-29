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

# ══════════════════════════════════════════════════════════════
# Copy default files into the volume (only if they don't exist)
# The volume is mounted at /var/www/storage/app/public
# so build-time files are wiped. This restores them.
# ══════════════════════════════════════════════════════════════
if [ -d /var/www/storage/app/public-defaults ]; then
    for dir in /var/www/storage/app/public-defaults/*/; do
        dirname=$(basename "$dir")
        if [ ! -d "/var/www/storage/app/public/$dirname" ]; then
            echo "Copying default files: $dirname"
            cp -r "$dir" "/var/www/storage/app/public/$dirname"
        fi
    done

    # Copy root-level files (not in subdirectories)
    for file in /var/www/storage/app/public-defaults/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            if [ ! -f "/var/www/storage/app/public/$filename" ]; then
                echo "Copying default file: $filename"
                cp "$file" "/var/www/storage/app/public/$filename"
            fi
        fi
    done

    # Fix ownership of copied files
    chown -R www-data:www-data /var/www/storage/app/public
fi

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