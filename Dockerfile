FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN useradd -m -u 82 -g 82 www-data || true

WORKDIR /var/www

COPY . /var/www

USER www-data

EXPOSE 8000
