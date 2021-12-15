#!/bin/bash

# Predefined:
# - DIST_PATH

source ./docker/common/scripts/console.bash

echo_info "Update browserslist database"
npx browserslist@latest --update-db

# TODO: echo_info "Copy .env for javascript"
# cp ./docker/build/data/.env ./src/.env

echo_process "Figure out current version"
VERSION=$(cat .bumpversion.cfg | grep current_version | sed -r s,"^.*= ",,)
echo_info "${VERSION}"

echo_info "Install php dependecies"
composer install --prefer-dist --no-dev
if [ $? -ne 0 ]; then
    echo_fail
fi

echo_info "Build assets"
npm run build
if [ $? -ne 0 ]; then
    echo_fail
fi

# Basic php file check
echo_info "Basic php file check"
for file in $(find ./src/ -iname "*.php"); do
    php -l $file > /dev/null
    
    if [ "$?" != "0" ]; then
        echo "!!! ERROR: $file"
        echo_fail
    fi
done

# Basic python file check
echo_info "Basic python file test"
for file in $(find ./Scripts/ -iname "*.py"); do
    env python3 -m compileall -q $file
    
    if [ "$?" != "0" ]; then
        echo "!!! ERROR: $file"
        echo_fail
    fi
done

echo_info "Compress archive"
FILENAME=sPHP-$VERSION.tgz
tar --exclude='.[^/]*' \
--exclude=./apigen.yml \
--exclude=./composer* \
--exclude=./tmp* \
--exclude=./dist* \
--exclude=./fabfile* \
--exclude=./docker* \
--exclude=./package* \
--exclude=./node_modules* \
--exclude=./src/Application/Cache/* \
--exclude=./src/Application/Public/uploads/* \
--exclude=./src/Application/Public/php-metrics* \
--exclude=./src/Application/Public/docs* \
--exclude=./src/Application/Public/assets/vendor* \
--exclude=*__pycache__* \
-czvf $DIST_PATH/$FILENAME ./

echo_info "Fix permissions"
chmod 777 $DIST_PATH/$FILENAME

# Success
echo_success
