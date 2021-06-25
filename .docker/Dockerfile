FROM php:7.4-fpm-alpine

RUN set -ex \
    && apk update && apk upgrade\
    && apk add --no-cache $PHPIZE_DEPS \
    openssl \
    postgresql-dev \
    supervisor \
    libstdc++ \
    make \
    && apk add --no-cache \
      freetype \
      libjpeg-turbo \
      libpng \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
      libzip-dev \
      zip \
      unzip \
      vim \
      curl \
    libbz2 \
    libzip-dev \
    zlib-dev \
    bzip2-dev \
    libxslt-dev \
    libmcrypt-dev \
    libxml2-dev \
    autoconf \
    libjpeg-turbo-dev \
    libpng-dev \
    yaml-dev \
    libaio-dev \
    oniguruma-dev \
    php7-bz2 \
    php7-pdo php7-pgsql php7-bcmath php7-zmq php7-curl php7-pear \
    busybox-initscripts \
    && docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      --with-jpeg=/usr/include/ \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) gd bcmath bz2 exif json mbstring opcache pcntl pdo pdo_pgsql pdo_mysql simplexml soap sockets xsl \
    && pecl install apcu-5.1.18 \
    && docker-php-ext-enable apcu \
    && apk del --no-cache \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && rm -rf /tmp/*

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo 'max_execution_time = 1000' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini;
RUN echo 'memory_limit = 999M' >> /usr/local/etc/php/conf.d/docker-php-memory_limit.ini;

ADD ./supervisord/* /etc/supervisord/

RUN touch /var/log/cron.log
RUN chmod 0777 /var/log/cron.log

ADD ./crontab /etc/crontabs/root
RUN crontab /etc/crontabs/root
CMD ['cron', '-f']
CMD ['crond', '-f']

COPY ./php/php.ini /usr/local/etc/php/php.ini
COPY ./php/init.sh /home/init.sh
RUN sed -i -e 's/\r$//' /home/init.sh
RUN chmod +x /home/init.sh

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 /var/www/html

EXPOSE 9000

ENTRYPOINT ["/home/init.sh"]