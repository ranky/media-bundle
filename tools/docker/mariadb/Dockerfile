ARG MARIA_VERSION=8.0.23
FROM mariadb:${MARIA_VERSION}

ARG TZ
RUN echo ${TZ} > /etc/timezone \
    && dpkg-reconfigure --frontend noninteractive tzdata \
    && chown -R mysql:mysql /var/lib/mysql

ADD ./tools/docker/mariadb/init.sql /docker-entrypoint-initdb.d/init.sql
COPY --chmod=644 ./tools/docker/mariadb/my.cnf /etc/mysql/conf.d/config.cnf
#RUN chmod 644 /etc/mysql/conf.d/config.cnf
USER mysql
ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 3306
CMD ["mysqld"]

