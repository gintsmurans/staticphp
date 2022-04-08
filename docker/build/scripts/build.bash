#!/bin/bash

# Predefined:
# - DIST_PATH

source /root/docker/common/scripts/console.bash

DIST_PATH="/srv/sites/web_mounted/$DIST_PATH"

echo_process "Make sure .env.prod file exists .. "
if [ ! -f "./src/Application/.env.prod" ]; then
    echo_error ".env file not found in \"./src/Application/.env.prod\""
fi
echo_nl "OK"

echo_process "Figure out current version .. "
VERSION=$(cat .bumpversion.cfg | grep current_version | sed -r s,"^.*= ",,)
echo_nl "${VERSION}"

echo_info "Link dependecies from cache"
ln -sfn /srv/sites/cache/node_modules ./node_modules
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

echo_info "Copy vendors from cache"
rm -f ./vendor \
&& cp -r /srv/sites/cache/vendor ./vendor \
&& cp -r `pwd`/modules/barcodegen ./vendor/barcodegen
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

echo_info "Copy fonts"
npm run copy-fonts
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

echo_info "Install php dependecies"
composer install --prefer-dist --no-dev -o
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

# Basic php file check
echo_info "Basic php file check"
for file in $(find ./src/ -iname "*.php"); do
    php -l $file > /dev/null
    
    ret=$?
    if [ $ret -ne 0 ]; then
        echo_error "Error in $file" $ret
    fi
done

# Basic python file check
# echo_info "Basic python file test"
# for file in $(find ./scripts/ -iname "*.py"); do
#     python3 -m compileall -q $file

#     ret=$?
#     if [ $ret -ne 0 ]; then
#         echo_error "Error in $file" $ret
#     fi
# done

echo_info "Update browserslist database"
npx browserslist@latest --update-db
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

echo_info "Build assets"
npm run build
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

echo_info "Create dist directory"
mkdir -p $DIST_PATH && chmod 777 $DIST_PATH

FILENAME=pm-$VERSION.tgz
echo_info "Compress archive to $DIST_PATH/$FILENAME"
tar --exclude='.[^/]*' \
--exclude=*__pycache__* \
--exclude=./src/Application/Cache/* \
--exclude=./src/Application/Public/uploads/* \
--exclude=./src/Application/Public/php-metrics* \
--exclude=./src/Application/Public/docs* \
--exclude=./src/Application/Public/assets/vendor* \
-czvhf $DIST_PATH/$FILENAME ./LICENSE ./README.md ./scripts/ ./src/ ./vendor/
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

echo_info "Fix permissions"
chmod 777 $DIST_PATH/$FILENAME
ret=$?
if [ $ret -ne 0 ]; then
    echo_fail
    exit $ret
fi

# Success
echo_success
