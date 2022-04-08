# ---------------------------------------------------------------------------
# Init from static_php_common
# ---------------------------------------------------------------------------
FROM static_php_common
MAINTAINER gm@gm.lv

# ---------------------------------------------------------------------------
# RUN the build
# ---------------------------------------------------------------------------
WORKDIR /srv/sites/build

# Copy dependecies
COPY docker/build/ /root/docker/build/

# Copy all to build folder
COPY . .

CMD ["/root/docker/build/scripts/build.bash"]
