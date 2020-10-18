#!/usr/bin/env bash

set -e

# Prevents this script from running twice on container reboot.
if [[ -e /.system-initialized ]]; then
    exit 0
fi
touch /.system-initialized

printf "\n- Setting up MG-Downloader for ${APP_ENV} -\n"

composer install

printf "\n- Setting up ${APP_ENV} database -\n\n"

su - root -c "php -f ./bin/console doctrine:schema:create --env=${APP_ENV}"

printf "\n- Setting up user for access -\n\n"

su - root -c "php -f ./bin/console fos:user:create admin admin@admin.com admin --super-admin"

printf "\n- Setup finished - container can now be used -\n\n"
