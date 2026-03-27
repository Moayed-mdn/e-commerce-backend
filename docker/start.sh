#!/bin/bash

# Substitute PORT variable in nginx config
export PORT=${PORT:-80}
envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

# Start Supervisor (which manages nginx, php-fpm, queue worker)
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf