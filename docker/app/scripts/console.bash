#!/usr/bin/env bash

# Variables
BASE_PATH=$(pwd)
HOSTNAME=$(hostname)

# Colors
WHITE='\033[0;37m'
CYAN='\033[0;36m'
UCYAN='\033[4;36m'
GREEN='\033[0;32m'
UGREEN='\033[4;32m'
YELLOW='\033[0;33m'
UYELLOW='\033[4;33m'
RED='\033[0;31m'
URED='\033[4;31m'

ON_BLACK='\033[40m'

NC='\033[0m' # No Color


function echo_process {
    printf "$CYAN[$HOSTNAME] $1"
}
function echo_nl {
    printf "${NC}\n"
}
function echo_ok {
    printf "${GREEN}OK$1${NC}\n"
}
function echo_fail {
    printf "${RED}ERR${NC}\n"
}

function echo_debug {
    printf "${WHITE}${ON_BLACK}[$HOSTNAME] $1${NC}\n"
}
function echo_info {
    printf "${UCYAN}[$HOSTNAME] $1${NC}\n"
}
function echo_warning {
    >&2 printf "${UYELLOW}[$HOSTNAME] $1${NC}\n"
}
function echo_error {
    >&2 printf "\n\n"
    >&2 printf "${RED}################################ FAILED ########################################${NC}\n\n"
    >&2 printf "${URED}[$HOSTNAME] $1${NC}\n\n"
    >&2 printf "${RED}################################################################################${NC}\n\n"
    >&2 printf "\n\n"
    exit -1
}
function echo_success {
    printf "\n\n"
    printf "${GREEN}################################ DONE ########################################${NC}\n\n"
    printf "${UGREEN}[$HOSTNAME] $1${NC}\n\n"
    printf "${GREEN}##############################################################################${NC}\n\n"
    printf "\n\n"
    exit 0
}
