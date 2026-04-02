#!/bin/bash
set -e

echo "=== Fixing permissions ==="
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Creating storage link ==="
php artisan storage:link || true

echo "=== Caching config ==="
php artisan config:cache
php artisan route:cache

echo "=== Seeding if needed ==="
php artisan tinker --execute="if(DB::table('categories')->count() === 0) { Artisan::call('db:seed', ['--force' => true]); echo 'Seeded!'; } else { echo 'Already seeded, skipping.'; }"

echo "=== Starting PHP-FPM ==="
exec php-fpm