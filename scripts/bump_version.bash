#!/bin/bash

# Find base path
BASE_PATH=$(dirname "$0")/../
BASE_PATH="`cd $BASE_PATH;pwd`"
APP_PATH="$BASE_PATH/src/Application"

# Set variables the we need
COMMIT_INFO=$(git log -1 --pretty="%H%n%cd" --date=format:"%d.%m.%Y %H:%M")
COMMIT_HASH=$(echo "$COMMIT_INFO" | head -1)
COMMIT_DATE=$(echo "$COMMIT_INFO" | head -2 | tail -1)

# Bump version
echo "Bumping version"
bump2version --allow-dirty patch
if [ "$?" != "0" ]; then
    exit 1
fi

git add .bumpversion.cfg
if [ "$?" != "0" ]; then
    exit 1
fi

# Replace entries in ./Application/Config/App.php
CONFIG_FILENAME="$APP_PATH/Config/App.php"
echo "Updating commit info in $CONFIG_FILENAME"
sed -E "s/(git_commit_hash'\] = ').*(';)/\1$COMMIT_HASH\2/" $CONFIG_FILENAME > $CONFIG_FILENAME.bak \
    && mv $CONFIG_FILENAME.bak $CONFIG_FILENAME \
    && sed -E "s/(git_commit_date'\] = ').*(';)/\1$COMMIT_DATE\2/" $CONFIG_FILENAME > $CONFIG_FILENAME.bak \
    && mv $CONFIG_FILENAME.bak $CONFIG_FILENAME
if [ "$?" != "0" ]; then
    exit 1
fi
git add $CONFIG_FILENAME

# Replace entries in ./Application/Public/assets/base/js/config.ts
CONFIG_FILENAME="$APP_PATH/Public/assets/src/base/ts/config.ts"
echo "Updating commit info in $CONFIG_FILENAME"
sed -E "s/(git_commit_hash: ').*(',)/\1$COMMIT_HASH\2/" $CONFIG_FILENAME > $CONFIG_FILENAME.bak \
    && mv $CONFIG_FILENAME.bak $CONFIG_FILENAME \
    && sed -E "s/(git_commit_date: ').*(',)/\1$COMMIT_DATE\2/" $CONFIG_FILENAME > $CONFIG_FILENAME.bak \
    && mv $CONFIG_FILENAME.bak $CONFIG_FILENAME
if [ "$?" != "0" ]; then
    exit 1
fi
git add $CONFIG_FILENAME

echo "Done"
