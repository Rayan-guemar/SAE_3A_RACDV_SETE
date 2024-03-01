FROM php:8.2

ENV COMPSOER_ALLOW_SUPERUSER=1

# Install dependencies
RUN apt-get update && apt-get install -y \
    bash \
    curl \
    git \
    openssh-client \
    unzip \
    wget \
    npm \
    nodejs \
    php7.1-pgsql

RUN docker-php-ext-install pdo mysqli pdo_mysql

RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /bin/composer


RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install symfony-cli

RUN mkdir /app

COPY ./festiflux /app

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

RUN npm install

RUN npm run watch &

CMD [ "symfony", "server:start", "--no-tls", "--port=80" ]


