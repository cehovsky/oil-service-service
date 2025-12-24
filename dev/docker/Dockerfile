FROM php:8.5-fpm AS php_dev

WORKDIR /app

# Dev essentials and PHP extensions (bcmath/pcntl/pdo_mysql + Xdebug)
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip nginx \
    && docker-php-ext-install bcmath pcntl pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /var/lib/apt/lists/*

# Composer CLI
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Xdebug config
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN { \
      echo "xdebug.mode=develop,debug,coverage"; \
      echo "xdebug.start_with_request=trigger"; \
      echo "xdebug.client_host=host.docker.internal"; \
    } > /usr/local/etc/php/conf.d/99-dev.ini

# App dirs
RUN mkdir -p var/cache/test \
    && chmod 777 var/cache/test

# Nginx config
RUN rm -f /etc/nginx/sites-enabled/default
RUN cat >/etc/nginx/conf.d/default.conf <<'EOF'
server {
    listen 80;
    server_name _;
    root /app/public;
    index index.php index.html;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $document_root;
    }

    client_max_body_size 32m;
}
EOF

# Start both php-fpm and nginx
CMD bash -lc "php-fpm -D && exec nginx -g 'daemon off;'"

EXPOSE 80