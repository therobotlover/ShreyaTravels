#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
  cp .env.example.prod .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force
fi

# db_host="${DB_HOST:-mysql}"
# db_port="${DB_PORT:-3306}"
# db_wait_seconds="${DB_WAIT_SECONDS:-60}"

# db_host="${DB_HOST:-mysql}"
# db_port="${DB_PORT:-3306}"
# echo "Waiting for database at ${db_host}:${db_port}..."
# while ! nc -z "$db_host" "$db_port" >/dev/null 2>&1; do
#   sleep 2
# done
# echo "Database is reachable."

php artisan config:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure only one MPM is enabled at runtime (protects against base image changes).
if command -v a2dismod >/dev/null 2>&1; then
  a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true
  a2enmod mpm_prefork >/dev/null 2>&1 || true
  rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
fi

exec /usr/local/bin/apache2-foreground
