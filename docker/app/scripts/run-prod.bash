#!/bin/bash

##################
### Preprocess ###
##################

THIS_DIR="$(dirname "$(realpath "$0")")"
source "$THIS_DIR/console.bash"

echo_info "Sync static files"
rsync -a --progress ./src/Application/Public/ ./static/

echo_info "Sync local files from upload forlder"
rsync -a --progress ./src/Application/Public/uploads/ /srv/media/uploads/


########################
### Run main process ###
########################

# Define a function to handle the SIGTERM signal
function handle_sigterm {
  echo "Received SIGTERM signal. Stopping long running process..."
  echo $$
  # Stop the long running process here
  exit 0
}

echo_info "Set the trap to catch the SIGTERM signal"
trap 'handle_sigterm' SIGTERM

echo_info "Start php-fpm process"
php-fpm -F -R --force-stderr &
pid=$!

echo_info "Wait for the process $pid to finish"
wait $pid
