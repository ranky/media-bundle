#!/usr/bin/env bash
set -e # exit script if any command fails (non-zero value)

cd ${APP_DIRECTORY} || exit

#su - www-data

# Composer dump-autoload
if [ "$DOCKER_ENV" == "dev" ]; then
  XDEBUG_MODE=off composer dump-autoload --dev
else
  composer dump-autoload --no-dev --optimize --classmap-authoritative
fi

if [ "$DOCKER_ENV" == "prod" ]; then
 composer dump-env prod
fi

docker-php-entrypoint php-fpm

#exec "$@"
