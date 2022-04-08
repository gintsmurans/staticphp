# ---------------------------------------------------------------------------
# Init from static_php_common
# ---------------------------------------------------------------------------
FROM static_php_common
MAINTAINER gm@gm.lv

# Copy dependecies
COPY . /root/docker/develop/

# ---------------------------------------------------------------------------
# Run dev
# ---------------------------------------------------------------------------
WORKDIR /srv/sites/web

RUN envsubst < /root/docker/develop/conf/supervisord.services.conf > /etc/supervisor/conf.d/services.conf

CMD ["/root/docker/develop/scripts/run.bash"]

EXPOSE 5500
