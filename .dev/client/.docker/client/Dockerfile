FROM php:7.4-fpm-alpine

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --no-cache \
      freetype \
      libjpeg-turbo \
      libpng \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
      libzip-dev \
      zip \
      unzip \
    && docker-php-ext-configure gd \
      --with-freetype=/usr/include/ \
      --with-jpeg=/usr/include/ \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) gd zip\
    && docker-php-ext-enable gd \
    && apk del --no-cache \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    && rm -rf /tmp/*

RUN echo 'max_execution_time = 1000' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini;
RUN echo 'memory_limit = 999M' >> /usr/local/etc/php/conf.d/docker-php-memory_limit.ini;

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./php/init.sh /home/init.sh
RUN sed -i -e 's/\r$//' /home/init.sh
RUN chmod +x /home/init.sh

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 /var/www/html

EXPOSE 9000

ENTRYPOINT ["/home/init.sh"]