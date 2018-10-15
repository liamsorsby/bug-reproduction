FROM php:7.2-apache

RUN apt-get update && apt-get -y install autoconf git unzip

RUN git clone https://github.com/phpredis/phpredis.git
RUN cd phpredis && git checkout develop && git fetch && git pull

WORKDIR /var/www/html/phpredis
RUN phpize
RUN ./configure && make && make install

RUN echo extension=redis.so > /usr/local/etc/php/conf.d/redis.ini
RUN service apache2 restart
RUN cd ..
WORKDIR /var/www/html/
