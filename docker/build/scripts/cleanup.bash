#!/bin/bash

# Predefined:
# - DIST_PATH

source ./docker/common/scripts/console.bash

echo_info "Clean dist"
rm -rf $DIST_PATH/*
