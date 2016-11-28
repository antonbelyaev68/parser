FROM ubuntu:latest
MAINTAINER Anton Belyaev <antonbelyaev68@gmail.com>

RUN apt-get update && apt-get -y upgrade && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    apache2 libapache2-mod-php7.0 curl lynx-cur git tree \
    php7.0 php7.0-dev php7.0-common php7.0-cgi php7.0-fpm php7.0-mysql php7.0-opcache \
    php7.0-intl php7.0-gd php7.0-curl php7.0-mcrypt php7.0-imap php7.0-ldap php7.0-json \
    php7.0-zip php7.0-soap 

RUN a2enmod php7.0
RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

RUN sed -i "s/error_reporting = .*$/error_reporting = E_ALL & ~E_NOTICE/" /etc/php/7.0/apache2/php.ini
RUN sed -i "s/max_execution_time\ =\ 30/max_execution_time\ =\ 0/g" /etc/php/7.0/apache2/php.ini
RUN sed -i "s/memory_limit\ =\ 128M/memory_limit\ =\ 2G/g" /etc/php/7.0/apache2/php.ini

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

EXPOSE 80

RUN git clone https://github.com/antonbelyaev68/parser.git /var/www/parser
RUN tree /var/www/
RUN cd /var/www/parser
RUN composer --working-dir=/var/www/parser/ install

RUN mkdir /var/www/parser/var
RUN mkdir /var/www/parser/var/logs
RUN mkdir /var/www/parser/var/cache

RUN chmod -R 777 /var/www/parser/var/*

ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

CMD /usr/sbin/apache2ctl -D FOREGROUND


