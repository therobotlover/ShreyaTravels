FROM php:8.2-apache

# System deps + PHP extensions + Apache modules (HARD FIX: only ONE MPM)
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
    git \
    unzip \
    netcat-openbsd \
    libpng-dev \
    libonig-dev \
    libzip-dev; \
    docker-php-ext-install pdo_mysql mbstring zip; \
    a2enmod rewrite; \
    \
    # Ensure only one MPM is enabled
    a2dismod mpm_event mpm_worker || true; \
    a2enmod mpm_prefork; \
    rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*; \
    \
    rm -rf /var/lib/apt/lists/*

# Apache docroot -> Laravel /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN set -eux; \
    sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf; \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Silence AH00558 by setting a global ServerName
RUN set -eux; \
    echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf; \
    a2enconf servername

WORKDIR /var/www/html

# Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first (for caching)
COPY composer.json composer.lock ./

# Copy the app code NOW so artisan exists when we run package:discover
COPY . .

# Install PHP deps (disable scripts so it doesn't try to call artisan too early)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
    && php artisan package:discover --ansi || true

# Laravel permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Entrypoint script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]
