FROM php:8.1-fpm
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN mkdir /app
COPY . /app
COPY ./provision/php.ini /usr/local/etc/php/php.ini-production
RUN chmod -R 775 /app/var/cache
RUN php /app/bin/console cache:clear