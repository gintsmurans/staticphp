#!/bin/bash

source ${META_PATH}/scripts/console.bash

# Go to web directory
# cd ${SRC_MOUNT_PATH}

# Copy fonts
# npm run copy-fonts

# Update config
envsubst < ${META_PATH}/conf/supervisord.services.conf > /etc/supervisor/conf.d/services.conf

# Start supervisord process
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf -n
