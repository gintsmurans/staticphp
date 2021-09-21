# ---------------------------------------------------------------------------
# Install base
# ---------------------------------------------------------------------------
FROM debian:stable
MAINTAINER gm@gm.lv

# Gather args
ARG PHP_VERSION=8.1
ARG META_PATH
ARG SRC_MOUNT_PATH
ARG RUNTIME_PATH

# Avoid interactive cli blockers
ENV DEBIAN_FRONTEND noninteractive

# OS dependecies
RUN apt-get update -yq \
    && apt-get install -yq apt-utils

RUN apt-get install -yq apt-transport-https lsb-release ca-certificates \
        wget curl unzip supervisor


# ---------------------------------------------------------------------------
# Install python
# ---------------------------------------------------------------------------
RUN apt-get install -y python3 python3-pip


# ---------------------------------------------------------------------------
# Install php
# ---------------------------------------------------------------------------
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    # && wget -O /etc/apt/trusted.gpg.d/nginx.gpg http://nginx.org/keys/nginx_signing.key \
    && sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' \
    # && sh -c 'echo "deb http://nginx.org/packages/debian/ $(lsb_release -sc) nginx" > /etc/apt/sources.list.d/nginx.list' \
    && apt-get update -yq \
    && apt-get install -yq php${PHP_VERSION}-cli php${PHP_VERSION}-dev php${PHP_VERSION}-curl php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-xml php${PHP_VERSION}-zip php${PHP_VERSION}-mbstring php${PHP_VERSION}-gd \
        php${PHP_VERSION}-pgsql php${PHP_VERSION}-mysql php${PHP_VERSION}-ldap

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
RUN wget -O - https://deb.nodesource.com/setup_14.x | bash \
    && apt-get install nodejs -yq \
    && wget -O /usr/lib/ssl/cert.pem https://curl.haxx.se/ca/cacert.pem


# ---------------------------------------------------------------------------
# Copy meta files
# ---------------------------------------------------------------------------
COPY ./conf $META_PATH/conf
COPY ./scripts $META_PATH/scripts
COPY ./data $META_PATH/data


# ---------------------------------------------------------------------------
# Install requirments and cachable stuff
# ---------------------------------------------------------------------------
RUN mkdir -p /srv/cache/ && mkdir -p $SRC_MOUNT_PATH \
    && mkdir /srv/cache/vendor && mkdir /srv/cache/node_modules \
    && ln -sfn /srv/cache/vendor $SRC_MOUNT_PATH/vendor \
    && ln -sfn /srv/cache/node_modules $SRC_MOUNT_PATH/node_modules \
    && chmod 777 $SRC_MOUNT_PATH/vendor \
    && chmod 777 $SRC_MOUNT_PATH/node_modules

WORKDIR /srv/cache/

# PHP
RUN cp $META_PATH/data/composer.json /srv/cache/ \
    && cp $META_PATH/data/composer.lock /srv/cache/ \
    && composer install --ignore-platform-reqs

# NPM
#RUN cp $META_PATH/data/package.json /srv/cache/ \
#    && cp $META_PATH/data/package-lock.json /srv/cache/ \
#    && npm install

#RUN npm rebuild node-sass
#RUN npx browserslist@latest --update-db

# Python
RUN pip3 install -r $META_PATH/data/requirements.txt


# ---------------------------------------------------------------------------
# Run dev
# ---------------------------------------------------------------------------
WORKDIR $RUNTIME_PATH

CMD ["/root/meta/scripts/run.bash"]

EXPOSE 5000
