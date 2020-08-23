#!/bin/bash

# Go to web directory
cd /srv/sites/web

# Install composer dependecies
sudo -u www-data composer install

# Install npm dependecies
sudo -u www-data npm install

# Restart supervisor
service supervisor stop
service supervisor start
