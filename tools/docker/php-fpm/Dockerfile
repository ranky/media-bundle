ARG PHP_VERSION=8.1
ARG DOCKER_ENV
FROM php:${PHP_VERSION}-fpm-alpine as build
ARG APP_DIRECTORY

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

ARG DOCKER_ENV
ARG INSTALL_PHP_XDEBUG
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

### User & Group ###
ARG HOST_UID
ARG HOST_GID
ARG HOME_DIR=/home/www-data
#groupmod -o -g ${HOST_GID} www-data && \
RUN usermod -u ${HOST_UID} www-data && \
    groupmod -g ${HOST_GID} www-data && \
    usermod -o -u ${HOST_UID} -g www-data www-data && \
    chown -R www-data:www-data /var/www && \
    # https://stackoverflow.com/a/59743322
    mkdir -p ${HOME_DIR}/.composer/cache && \
    chmod +x /usr/bin/composer && \
    chown -R ${HOST_UID}:${HOST_GID} ${HOME_DIR} && \
    #chmod -R ug+rwx ${HOME_DIR} && \
    usermod --uid ${HOST_UID} --home ${HOME_DIR} --shell /bin/bash www-data
    #groupmod --gid ${HOST_GID} www-data

### php-fpm config ###
RUN rm /usr/local/etc/php-fpm.d/* && \
    rm -f $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini && \
    touch /var/log/php-fpm.log && \
    touch /var/log/php-errors.log && \
    chown -R www-data:www-data /var/log/php-*.log
COPY ./tools/docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR ${APP_DIRECTORY}


###  development ###
FROM build as build_dev
# $PHP_INI_DIR => /usr/local/etc/php
ARG APP_DIRECTORY
RUN echo "I am dev with bind mount (host volumes)" && \
    mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini  && \
    rm -f $PHP_INI_DIR/php.ini-production
COPY ./tools/docker/php-fpm/app.ini $PHP_INI_DIR/conf.d/php.ini
COPY ./composer.* ${APP_DIRECTORY}/
RUN if [ "$DOCKER_ENV" = "dev" ] && [ "$INSTALL_PHP_XDEBUG" = "true" ]; then \
    echo 'zend_extension=xdebug' >> $PHP_INI_DIR/conf.d/php.ini; \
fi
RUN XDEBUG_MODE=off composer install --no-progress --no-interaction --no-scripts


FROM build_${DOCKER_ENV} as final
COPY --chmod=+x ./tools/docker/php-fpm/entrypoint.sh /entrypoint.sh
USER www-data
ENTRYPOINT ["/entrypoint.sh"]
