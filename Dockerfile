# Default Dockerfile
#
# @link     https://www.hyperf.io
# @document https://hyperf.wiki
# @contact  group@hyperf.io
# @license  https://github.com/hyperf/hyperf/blob/master/LICENSE

FROM hyperf/hyperf:8.1-alpine-v3.18-swoole
LABEL maintainer="Abhinav Paudel" version="1.0" license="MIT" app.name="Hyperf-kafka-rest-proxy"

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone
ARG memory_limit

ENV TIMEZONE=${timezone:-"Asia/Kathmandu"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true) \
    MEMORY_LIMIT=${memory_limit:-"1G"}

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
    && cd /etc/php* \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=${MEMORY_LIMIT}"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www

# Composer Cache
COPY ./composer.* /opt/www/

RUN composer install --no-dev --no-scripts

COPY . /opt/www
# RUN composer install --no-dev -o && php bin/hyperf.php

# make sure ssl folder is empty in the built image  
RUN rm -rf ssl && mkdir ssl
COPY .env.example .env

EXPOSE 9501

# Copy the entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint to the custom script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
