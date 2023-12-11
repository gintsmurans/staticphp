#!/bin/bash

PLATFORM=`uname`

# Find base path
BASE_PATH=$(dirname $(readlink -f "$0"))/..
BASE_PATH="`cd $BASE_PATH;pwd`"

# Git stuff
COMMIT="HEAD"
LOCAL_BRANCH="`git name-rev --name-only HEAD`"
TRACKING_REMOTE="`git config branch.$LOCAL_BRANCH.remote`"
TRACKING_BRANCH="$TRACKING_REMOTE/$LOCAL_BRANCH"


# Test non-ascii filenames
echo "*Testing non-ascii filenames.. "
if [ $(git diff --cached --name-only --diff-filter=A -z $COMMIT | LC_ALL=C tr -d '[ -~]\0' | wc -c) -gt 0 ]; then
    echo "Error: Attempt to add a non-ascii file name."
    echo
    echo "This can cause problems if you want to work"
    echo "with people on other platforms."
    echo
    echo "To be portable it is advisable to rename the file ..."
    echo
    exit 1
fi
echo " Done"
echo


# Test for most common debug symbols
#echo "*Testing for debug symbols.. "
#if [ "$(git diff --cached $COMMIT | grep -P 'print_r|console\\.log')" != "" ]; then
#    echo "!!! ERROR !!!"
#    echo "$(git diff --cached $COMMIT | grep -P 'print_r|console\\.log')"
#    exit 1
#fi
#echo " Done"
#echo


# Trying to compile all php files
if [ $(git diff-index --cached --name-only --diff-filter=ACMR $COMMIT | grep \\.php | wc -l) -gt 0 ]; then
    echo "*PHP file(-s) changed, running lint.."

    for file in $(git diff-index --cached --name-only --diff-filter=ACMR $COMMIT | grep \\.php); do
        php -l $file > /dev/null

        if [ "$?" != "0" ]; then
            echo "!!! ERROR: $COMMIT $file"
            exit 1
        fi
    done

    echo " Done"
    echo
fi


# Compile css
if [ $(git diff-index --cached --name-only $COMMIT | grep \\.scss | wc -l) -gt 0 ]; then
    echo "*SCSS file(s) modified, compressing.. "
    cd $BASE_PATH
    npm run css:build

    if [ "$?" != "0" ]; then
        echo
        echo "Something went wrong while trying to minify css files"
        echo
        exit 1
    fi

    echo " Done"
    echo
fi


# Compile js
if [ $(git diff-index --cached --name-only $COMMIT | grep -E "\.(js|ts)$" | wc -l) -gt 0 ]; then
    echo "*JS file(s) modified, compressing.. "
    cd $BASE_PATH
    npm run js:build

    if [ "$?" != "0" ]; then
        echo
        echo "Something went wrong while trying to minify js files"
        echo
        exit 1
    fi

    echo " Done"
    echo
fi


# Bump patch version
echo "*Bumping patch version.. "
./scripts/bump_version.bash 1


# Test for whitespace errors
echo "*Testing for whitespace errors.. "
git diff-index --cached --check $COMMIT --
if [ "$?" != "0" ]; then
    echo "!!! ERROR !!!"
    exit 1
fi
echo " Done"
echo
