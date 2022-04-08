# ---------------------------------------------------------------------------
# Install base
# ---------------------------------------------------------------------------
FROM debian:stable
MAINTAINER gm@gm.lv

# Gather args
ARG PHP_VERSION=8.1

# Avoid interactive cli blockers
ENV DEBIAN_FRONTEND noninteractive

# OS dependecies
RUN apt-get update -yq \
    && apt-get install -yq apt-utils

RUN apt-get install -yq apt-transport-https lsb-release ca-certificates \
        nano wget curl unzip git rsync supervisor build-essential


# ---------------------------------------------------------------------------
# Install python
# ---------------------------------------------------------------------------
RUN apt-get install -y python3 python3-pip


# ---------------------------------------------------------------------------
# Install php
# ---------------------------------------------------------------------------
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' \
    && apt-get update -yq \
    && apt-get install -yq php${PHP_VERSION}-cli php${PHP_VERSION}-dev php${PHP_VERSION}-curl php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-xml php${PHP_VERSION}-zip php${PHP_VERSION}-mbstring php${PHP_VERSION}-gd \
        php${PHP_VERSION}-pgsql php${PHP_VERSION}-mysql php${PHP_VERSION}-ldap

RUN ln -s /bin/sed /usr/bin/sed

# PHP composer
# Source: https://stackoverflow.com/a/42147748
RUN wget -O /tmp/composer-setup.php https://getcomposer.org/installer \
    && wget -O /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    # Make sure we're installing what we think we're installing!
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --stable \
    && rm -f /tmp/composer-setup.*


# ---------------------------------------------------------------------------
# Install node.js and npm
# ---------------------------------------------------------------------------
RUN wget -O - https://deb.nodesource.com/setup_16.x | bash \
    && apt-get install nodejs -yq \
    && wget -O /usr/lib/ssl/cert.pem https://curl.haxx.se/ca/cacert.pem \
    && npm install -g npm@latest


# ---------------------------------------------------------------------------
# Copy files, Install dependecies
# ---------------------------------------------------------------------------
WORKDIR /srv/sites/cache

# Cache scripts and dependecies first
COPY . /root/docker/common/
RUN cp /root/docker/common/data/* ./

# PHP
RUN composer install

# NPM
RUN npm install --no-optional \
    && npm cache clean --force

# # Python
# RUN python3 -m pip install -r requirements.txt

WORKDIR /srv/sites/web
