FROM php:8.2-fpm-alpine

RUN set -eux; \
    apk add --no-cache $PHPIZE_DEPS \
        git bash unzip icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev postgresql-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j$(nproc) intl mbstring zip gd opcache pdo_pgsql; \
    pecl install redis && docker-php-ext-enable redis

# Opcache sane defaults (override with env if needed)
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.memory_consumption=128"; \
      echo "opcache.max_accelerated_files=20000"; \
      echo "opcache.validate_timestamps=1"; \
      echo "opcache.revalidate_freq=1"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
