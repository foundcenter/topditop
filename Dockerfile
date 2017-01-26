FROM ubuntu:16.04

ENV DEBIAN_FRONTEND noninteractive

## Install php supervisor
RUN apt update && \
    apt install -y php-fpm php-cli php-gd php-mcrypt php-mysql php-curl php-mbstring php-xml \
                       nginx \
                       curl \
                       zip \
                       nano \
                       supervisor


# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log

RUN ln -sf /dev/stderr /var/log/nginx/error.log

RUN sed -i 's/^listen\s*=.*$/listen = 127.0.0.1:9000/' /etc/php/7.0/fpm/pool.d/www.conf && \
    sed -i 's/^\;error_log\s*=\s*syslog\s*$/error_log = \/var\/www\/html\/storage\/logs\/php-cgi.log/' /etc/php/7.0/fpm/php.ini && \
    sed -i 's/^\;error_log\s*=\s*syslog\s*$/error_log = \/var\/www\/html\/storage\/logs\/php-cli.log/' /etc/php/7.0/cli/php.ini && \
    sed -i 's/^\upload_max_filesize\s*=\s*2M\s*$/upload_max_filesize = 10M/' /etc/php/7.0/fpm/php.ini && \
    sed -i 's/^\post_max_size\s*=\s*8M\s*$/post_max_size = 10M/' /etc/php/7.0/fpm/php.ini

RUN curl -sS https://getcomposer.org/installer | php

RUN mv composer.phar /usr/local/bin/composer

COPY files/root /

COPY src /var/www/html

RUN cd /var/www/html && composer install

RUN mkdir -p "/var/www/html/storage/logs"

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]