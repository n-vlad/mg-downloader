FROM php:7.4-fpm

# Setup scripts.
COPY /script/* /var/scripts/

# Setup interface display.
RUN echo "export PS1=\"[\[\e[36m\]\A\[\e[m\]] \u@\h \[\e[36m\]\w\[\e[m\]: \"" >> ~/.bashrc \
 && echo "export LANG=en_US.UTF-8" >> ~/.bashrc \
 && echo "cd /var/www/mg-downloader" >> ~/.bashrc

# Setup Composer to be ready to use within the container.
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install required packages.
RUN apt-get update \
 && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev \
 && docker-php-ext-install mysqli pdo pdo_mysql zip \
 && pecl install redis-5.1.1 \
 && pecl install xdebug-2.8.1 \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-enable redis xdebug \
 && docker-php-ext-install -j$(nproc) gd

# Setup XDebug
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini \
 && echo "[xdebug]" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.var_display_max_data = 20000" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.var_display_max_depth = 10" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.default_enable = 0" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.idekey = \"PHPSTORM\"" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.remote_port = 9001" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/php.ini \
 && echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/php.ini \
 && sed -i 's/memory_limit = .*/memory_limit = '-1'/' /usr/local/etc/php/php.ini \
 && sed -i 's/;error_log = php_errors.log/error_log = \/var\/log\/php_errors.log/' /usr/local/etc/php/php.ini

WORKDIR /var/www/mg-downloader

CMD [ "bash", "-c", "/var/scripts/development-boot.sh && php-fpm" ]
