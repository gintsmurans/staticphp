# ---------------------------------------------------------------------------
# Init from static_php_common
# ---------------------------------------------------------------------------
FROM static_php_common
MAINTAINER gm@gm.lv

# ---------------------------------------------------------------------------
# Run dev
# ---------------------------------------------------------------------------
WORKDIR /srv/sites/web

RUN envsubst < ./docker/develop/conf/supervisord.services.conf > /etc/supervisor/conf.d/services.conf

CMD ["./docker/develop/scripts/run.bash"]

EXPOSE 5500
