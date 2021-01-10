FROM ubuntu:groovy-20201125.2

WORKDIR /app

RUN apt update && apt install -y software-properties-common && add-apt-repository ppa:ondrej/php

ENV TZ=Europe/Lisbon \
    XDEBUG_CONFIG='idekey=PHPSTORM' \
    PHP_IDE_CONFIG='serverName=ptournament'

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt update && apt install -y nginx libpcre3-dev unzip git curl php8.0-dev php8.0-common php8.0-fpm php8.0-mysql php8.0-gd php8.0-zip php8.0-dom php8.0-mbstring php8.0-yaml php8.0-xdebug

COPY docker/web/php/xdebug-local.ini /etc/php/8.0/mods-available/xdebug-local.ini

RUN phpenmod -s ALL xdebug-local

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

CMD /etc/init.d/php8.0-fpm start && nginx -g "daemon off;"
