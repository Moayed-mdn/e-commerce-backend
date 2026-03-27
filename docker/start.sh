#!/bin/bash

# Substitute PORT variable in nginx config
export PORT=${PORT:-80}

# Remove any leftover default configs
rm -f /etc/nginx/sites-enabled/*
rm -f /etc/nginx/conf.d/default.conf

# Generate our config
envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/app.conf

# Start Supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf