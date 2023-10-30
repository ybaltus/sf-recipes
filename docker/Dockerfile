FROM php:8.1-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Install Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

# Install Nodejs
RUN apt install -y nodejs npm

# Install dependencies for Symfony
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql opcache intl zip calendar dom mbstring gd xsl
RUN pecl install apcu && docker-php-ext-enable apcu

# Copy vhost for Apache
COPY ./docker/vhosts/vhosts.conf /etc/apache2/sites-enabled/000-default.conf

# Copie the project files for Apache
COPY . /var/www

# Change the workdir on the container
WORKDIR /var/www/

# Install composer packages
RUN composer install

# Install node modules
RUN npm install

RUN chmod +x ./docker.sh

ENTRYPOINT ["/docker.sh"]
