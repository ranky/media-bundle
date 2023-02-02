#!/usr/bin/env bash

set -e # exit script if any command fails (non-zero value)
#cd ${APP_DIRECTORY} || exit

DOCKER_IP=$(/sbin/ip route|awk '/default/ { print $3 }')

export DOCKER_IP

if [ "$DOCKER_ENV" == "dev" ]; then
  XDEBUG_MODE=off composer install --no-ansi --no-scripts --no-progress --no-interaction --working-dir="${APP_DIRECTORY}"
  XDEBUG_MODE=off composer install --no-ansi --no-scripts --no-progress --no-interaction --working-dir="${APP_DIRECTORY}/tools/behat"
  XDEBUG_MODE=off composer install --no-ansi --no-scripts --no-progress --no-interaction --working-dir="${APP_DIRECTORY}/tools/phpstan"
  XDEBUG_MODE=off composer install --no-ansi --no-scripts --no-progress --no-interaction --working-dir="${APP_DIRECTORY}/tools/php-cs-fixer"
fi

if [ "$CI" == "true" ]; then
    XDEBUG_MODE=off composer dump-autoload --optimize --dev --working-dir="${APP_DIRECTORY}"
fi

if [ "$DOCKER_ENV" == "prod" ]; then
 composer dump-autoload --no-dev --optimize --classmap-authoritative --working-dir="${APP_DIRECTORY}"
 composer dump-env prod --working-dir="${APP_DIRECTORY}"
fi

# https://github.com/docker-library/php/blob/master/8.1/alpine3.17/fpm/docker-php-entrypoint
# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
