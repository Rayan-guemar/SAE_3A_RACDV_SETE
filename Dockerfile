FROM php:8.2

ENV COMPOSER_ALLOW_SUPERUSER=1

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

# RUN mv /tmp/* /app

# RUN composer install

# RUN npm i && npm run dev

# ENTRYPOINT ["bash", "-c", "composer install", "&&", "npm", "install", "&&", "npm", "run", "dev", "&&", "symfony", "server:start", "--no-tls", "--port=80"]

# CMD [ "composer", "install", "&&", "npm", "i", "&&", "npm", "run", "dev", "&&" "symfony", "server:start", "--no-tls", "--port=80" ]