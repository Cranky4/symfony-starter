FROM php:8.2-fpm-alpine

# Prepare
RUN apk add --update --no-cache \
    git \
    postgresql-dev \
    freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev libwebp-dev

# Configure
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

# Install
RUN NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && docker-php-ext-install -j$(nproc) \
     exif pdo_pgsql gd

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Symfony CLI
RUN apk add --no-cache bash
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli

# Own php.ini
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

# Workdir
WORKDIR /app
RUN chown -R www-data:www-data /app

CMD ["php-fpm"]
