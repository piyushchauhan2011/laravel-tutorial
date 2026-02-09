FROM composer:2 AS vendor
WORKDIR /app
COPY app/composer.json app/composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

FROM node:22-alpine AS assets
WORKDIR /app
COPY app/package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install --no-audit --no-fund; fi
COPY app/resources ./resources
COPY app/public ./public
COPY app/vite.config.js ./
RUN npm run build

FROM dunglas/frankenphp:1-php8.4
WORKDIR /app

RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY app /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build
COPY deployment/docker/Caddyfile /etc/caddy/Caddyfile
COPY deployment/docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && rm -f bootstrap/cache/*.php \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV SERVER_NAME=:8000

EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
