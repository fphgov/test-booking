FROM php:7-fpm-alpine

RUN apk add --no-cache --update git wget mysql-client libzip-dev supervisor

RUN apk add --no-cache --update zlib-dev icu-dev g++ libxml2-dev && docker-php-ext-configure intl && docker-php-ext-install intl

ENV LIBRARY_PATH=/lib:/usr/lib

ENV XDEBUG_MODE=off
ENV XDEBUG_CLIENT_HOST=host.docker.internal
ENV XDEBUG_CLIENT_PORT=9001

WORKDIR /var/www/html

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql zip opcache && \
    docker-php-ext-enable opcache

RUN apk add --no-cache cmake freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
  docker-php-ext-configure gd && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j${NPROC} gd

# Install Xdebug
RUN apk add --no-cache $PHPIZE_DEPS && pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.mode=$XDEBUG_MODE" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=$XDEBUG_CLIENT_HOST" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_port=$XDEBUG_CLIENT_PORT" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.var_display_max_data=512" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.var_display_max_children=128" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.var_display_max_depth=3" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;

HEALTHCHECK --interval=5m --timeout=3s CMD curl -f http://localhost/ || exit 1

COPY bash/crontab /etc/crontabs/docker-crontab

RUN chmod 0600 /etc/crontabs/docker-crontab

RUN /usr/bin/crontab /etc/crontabs/docker-crontab

EXPOSE 9001

RUN sed -i 's/9000/9002/' /usr/local/etc/php-fpm.d/zz-docker.conf
RUN sed -i 's/pm.max_children = 5/pm.max_children = 816/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/pm.start_servers = 2/pm.start_servers = 15/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/pm.min_spare_servers = 1/pm.min_spare_servers = 15/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/pm.max_spare_servers = 3/pm.max_spare_servers = 25/' /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9002

COPY supervisord.conf /etc/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
