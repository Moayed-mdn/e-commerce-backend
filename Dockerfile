FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    gettext-base \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first (for better caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Copy application code
COPY . /var/www

# Run composer again to trigger scripts
RUN composer dump-autoload --optimize

# Create storage directories
RUN mkdir -p /var/www/storage/framework/{views,cache,sessions,testing} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Copy Nginx template
COPY docker/nginx/railway.conf /etc/nginx/templates/default.conf.template

# Remove ALL default nginx configs
RUN rm -f /etc/nginx/sites-enabled/* \
    && rm -f /etc/nginx/sites-available/* \
    && rm -f /etc/nginx/conf.d/default.conf

# Copy Supervisor config
COPY docker/supervisord-railway.conf /etc/supervisor/conf.d/supervisord.conf

# Copy startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Copy default storage files to a staging directory (NOT inside the volume mount)
RUN cp -r /var/www/storage/app/public /var/www/storage/app/public-defaults

EXPOSE 80

# Run as root (needed for nginx + supervisor)
CMD ["/usr/local/bin/start.sh"]