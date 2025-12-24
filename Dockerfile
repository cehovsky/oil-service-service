FROM php:8.5-fpm AS php_prod

WORKDIR /app

# Install minimal extensions required by the app
RUN apt-get update \
	&& apt-get install -y --no-install-recommends libzip-dev unzip \
	&& docker-php-ext-install bcmath pcntl pdo_mysql \
	&& docker-php-ext-enable opcache \
	&& rm -rf /var/lib/apt/lists/*

COPY . /app

RUN mkdir -p var/cache/test \
	&& chmod 777 var/cache/test
