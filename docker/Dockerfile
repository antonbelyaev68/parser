FROM ubuntu:latest
MAINTAINER Anton Belyaev <antonbelyaev68@gmail.com>

RUN apt-get update && apt-get -y upgrade && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    apache2 php7.0 php7.0-mysql libapache2-mod-php7.0 curl lynx-cur git tree

RUN a2enmod php7.0
RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

RUN sed -i "s/error_reporting = .*$/error_reporting = E_ALL & ~E_NOTICE/" /etc/php/7.0/apache2/php.ini

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

EXPOSE 80

RUN mkdir /var/www/parser
RUN chmod 777 /var/www/parser
RUN git clone https://github.com/antonbelyaev68/parser.git /var/www/parser
RUN tree /var/www/parser
RUN sudo /var/www/parser composer install

ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

CMD /usr/sbin/apache2ctl -D FOREGROUND


