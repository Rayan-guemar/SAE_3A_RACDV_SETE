FROM php:8.2

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

COPY ./festiflux/assets /app/assets
COPY ./festiflux/bin /app/bin
COPY ./festiflux/config /app/config
COPY ./festiflux/migrations /app/migrations
COPY ./festiflux/public /app/public
COPY ./festiflux/src /app/src
COPY ./festiflux/templates /app/templates
COPY ./festiflux/tests /app/tests
COPY ./festiflux/.env /app/.env
COPY ./festiflux/composer.json /app/composer.json
COPY ./festiflux/package.json /app/package.json
COPY ./festiflux/package-lock.json /app/package-lock.json
COPY ./festiflux/webpack.config.js /app/webpack.config.js
COPY ./festiflux/symfony /app/symfony
COPY ./festiflux/symfony.lock /app/symfony.lock

WORKDIR /app

RUN composer install

RUN npm i && npm run dev

# ENTRYPOINT ["bash", "-c", "composer install", "&&", "npm", "install", "&&", "npm", "run", "dev", "&&", "symfony", "server:start", "--no-tls", "--port=80"]

CMD [ "symfony", "server:start", "--no-tls", "--port=80" ]