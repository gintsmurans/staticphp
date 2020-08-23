#!/bin/bash

if [ ! -d app ]; then
    git clone git@github.com:gintsmurans/staticphp.git ./app

    cd ./app
    git checkout develop
    cp .env.example .env
fi

# Down docker
docker-compose down
docker-compose rm

# Build up
docker-compose up --build --force-recreate -d

# Run post install comands
docker-compose exec app /root/post-install.bash
