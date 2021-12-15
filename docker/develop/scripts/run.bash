#!/bin/bash

source ./docker/common/scripts/console.bash

echo_info "Update browserslist database"
npx browserslist@latest --update-db

# Start supervisord process
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf -n
