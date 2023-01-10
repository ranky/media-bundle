ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-fpm-alpine as build
ARG DOCKER_ENV=dev
ARG APP_DIRECTORY=/var/www/ranky-media-bundle
ARG HOST_UID=1000
ARG HOST_GID=1000
ARG APP_USER=appuser
ARG APP_GROUP=appgroup
ARG INSTALL_PHP_XDEBUG=false
RUN apk add --no-cache --update-cache $PHPIZE_DEPS \
    curl \
    bash \
    git \
    ### usermod change uid & gid
    shadow \
    ### Intl
    icu icu-dev icu-libs icu-data-full \
    ### Not remember. Encryption maybe
    gnupg \
    ### ZIP
    zip unzip libzip-dev \
    ### ffmpeg
    ffmpeg \
    ### imagick
    imagemagick imagemagick-dev \
    ### GD
    freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
    ### Image Optimize
    jpegoptim optipng pngquant gifsicle libwebp libwebp-tools \
    ### Xml
    libxml2-dev && \
    ### php extensions
    #docker-php-ext-install -j$(nproc) iconv && \
    docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-configure intl && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip intl xml opcache pdo pdo_mysql && \
    ### enable imagick
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    ### enable apcu
    pecl install apcu-5.1.21 && \
    docker-php-ext-enable apcu

RUN if [ "$DOCKER_ENV" = "dev" ] && [ "$INSTALL_PHP_XDEBUG" = "true" ]; then \
    pecl install xdebug-3.1.3 && \
    docker-php-ext-enable xdebug; \
fi

### Clean ###
RUN apk del $PHPIZE_DEPS && \
    apk del --no-cache icu-dev libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev imagemagick-dev && \
    rm -rf /var/cache/apk/* /tmp/* /var/tmp/* /usr/share/doc/*

### composer ###
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

## Create user & group ###
## -D - no password ###
ARG HOME_DIR=/home/${APP_USER}
RUN addgroup -g ${HOST_GID} ${APP_GROUP} && \
    adduser -G ${APP_GROUP} -u ${HOST_UID} ${APP_USER} -D --shell /bin/bash --home ${HOME_DIR} && \
    ## Add user current user to www-data group
    usermod -a -G www-data `whoami` && \
    ## Add user appuser to www-data group
    usermod -a -G www-data ${APP_USER} && \
    chmod +x /usr/bin/composer && \
    mkdir -p ${APP_DIRECTORY} && \
    chown -R ${HOST_UID}:${HOST_GID} ${APP_DIRECTORY}

### php-fpm config ###
RUN rm /usr/local/etc/php-fpm.d/* && \
    rm -f $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini && \
    touch /var/log/php-fpm.log && \
    touch /var/log/php-errors.log && \
    chown -R www-data:www-data /var/log/php-*.log
COPY ./tools/docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR ${APP_DIRECTORY}

###  development ###
FROM build as final
ARG DOCKER_ENV=dev
ARG APP_DIRECTORY=/var/www/ranky-media-bundle
ARG HOST_UID=1000
ARG HOST_GID=1000
ARG APP_USER=appuser
ARG APP_GROUP=appgroup
ARG INSTALL_PHP_XDEBUG=false
COPY ./composer.* ${APP_DIRECTORY}/
COPY ./tools ${APP_DIRECTORY}/tools
# $PHP_INI_DIR => /usr/local/etc/php
RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini && \
    rm -f $PHP_INI_DIR/php.ini-production && \
    chown -R ${HOST_UID}:${HOST_GID} /var/www
COPY ./tools/docker/php-fpm/app.ini $PHP_INI_DIR/conf.d/php.ini
RUN if [ "$DOCKER_ENV" = "dev" ] && [ "$INSTALL_PHP_XDEBUG" = "true" ]; then \
    echo 'zend_extension=xdebug' >> $PHP_INI_DIR/conf.d/php.ini; \
fi
COPY --chmod=+x ./tools/docker/php-fpm/entrypoint.sh /entrypoint.sh
USER ${APP_USER}
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
