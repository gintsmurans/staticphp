FROM debian:stable

MAINTAINER gm@gm.lv

# Avoid interactive cli blockers
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update -yq \
    && apt-get install -yq apt-utils

RUN apt-get install -yq apt-transport-https lsb-release ca-certificates \
        wget curl unzip git supervisor sudo

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    # && wget -O /etc/apt/trusted.gpg.d/nginx.gpg http://nginx.org/keys/nginx_signing.key \
    && sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' \
    # && sh -c 'echo "deb http://nginx.org/packages/debian/ $(lsb_release -sc) nginx" > /etc/apt/sources.list.d/nginx.list' \
    && apt-get update -yq \
    && apt-get install -yq php7.3-cli php7.3-json php7.3-xml php7.3-mbstring

# PHP composer
# Source: https://stackoverflow.com/a/42147748
RUN wget -O /tmp/composer-setup.php https://getcomposer.org/installer \
    && wget -O /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    # Make sure we're installing what we think we're installing!
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --stable \
    && rm -f /tmp/composer-setup.*

# node.js and npm
RUN wget -O - https://deb.nodesource.com/setup_12.x | bash \
    && apt-get install nodejs -yq \
    # CURL cacert
    # see: https://curl.haxx.se/docs/caextract.html
    # see: https://stackoverflow.com/questions/29822686/curl-error-60-ssl-certificate-unable-to-get-local-issuer-certificate
    # place downloaded file where "default_cert_file" points when running
    # php -r "var_dump(openssl_get_cert_locations());"
    && wget -O /usr/lib/ssl/cert.pem https://curl.haxx.se/ca/cacert.pem

# Supervisord configuration
ADD ./supervisord.web.conf  /etc/supervisor/conf.d/web.conf
ADD ./post-install.bash  /root/post-install.bash

# Fix www-data home directory
RUN mkdir -p /var/www/ \
    && chown www-data:www-data /var/www/ \
    && mkdir -p /srv/sites/web/ \
    && chown www-data:www-data /srv/sites/web/

# Set work directory and start php webserver
WORKDIR /srv/sites/web/
CMD ["/usr/bin/php7.3", "-S", "0.0.0.0:5000", "-t", "./Application/Public/"]

EXPOSE 5000
