FROM dunglas/frankenphp:php8.4.10-bookworm

WORKDIR /app

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    && install-php-extensions zip

# --- install PHP extensions ---
RUN install-php-extensions phalcon pdo_mysql redis

# --- install Composer ---
# frankenphp は curl, unzip, php が入ってるのでこれでOK
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- copy app source ---
COPY . .

# --- install PHP dependencies ---
RUN composer install --no-dev --prefer-dist --no-interaction || true

# --- PHP JIT 有効化 ---
RUN cat << 'EOF' > /usr/local/etc/php/conf.d/jit.ini
opcache.enable=1
opcache.enable_cli=1
opcache.jit_buffer_size=100M
opcache.jit=1255
EOF

ENV SERVER_ROOT=public/

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--adapter", "caddyfile"]
