FROM php:8.2

# RUN apt-get update -y && apt-get upgrade -y

# Install dependencies
RUN apt-get update && apt-get install -y \
    bash \
    curl \
    git \
    openssh-client \
    unzip \
    wget \
    npm \
    nodejs

RUN docker-php-ext-install pdo mysqli pdo_mysql

RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer


RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install symfony-cli

RUN mkdir /app

COPY ./festiflux /app

WORKDIR /app

RUN composer install

RUN npm install

RUN npm run watch &

CMD [ "symfony", "server:start", "--no-tls", "--port=80" ]


