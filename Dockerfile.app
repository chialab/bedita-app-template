ARG PHP_VERSION=7.4
ARG BASE_IMAGE=chialab/php
FROM ${BASE_IMAGE}:${PHP_VERSION}-fpm

# General configuration
WORKDIR /app/
RUN chown www-data:www-data /app
COPY deploy/php-conf.ini /usr/local/etc/php/conf.d/comune-bologna.ini
COPY deploy/phpfpm-conf.ini /usr/local/etc/php-fpm.d/zzz-comune-bologna.conf
COPY deploy/app_local.php /app/config/app_local.php
ENV DEBUG=0 \
    LOG_DEBUG_URL="console:///?stream=php://stdout" \
    LOG_ERROR_URL="console:///?stream=php://stdout"

# Install dependencies
COPY --chown=www-data:www-data ./composer.json ./composer.lock /app/
RUN composer install --no-dev --no-cache --prefer-dist

# Add sources and built assets
COPY --chown=www-data:www-data . /app/
RUN composer dump-autoload --classmap-authoritative --no-cache \
 && composer run post-install-cmd --no-interaction \
 && mkdir -p /app/tmp/cache/models/ /app/tmp/cache/persistent/ /app/tmp/cache/twig_view/ \
 && chown -R www-data:www-data /app \
 && chmod -R a=rwX /app
