FROM dunglas/frankenphp:php8.3.23-bookworm

WORKDIR /app

# install required PHP extensions
RUN install-php-extensions phalcon pdo_mysql redis

COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction || true

ENV SERVER_ROOT=public/

EXPOSE 80

CMD ["frankenphp"]
