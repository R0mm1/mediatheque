FROM mysql:5.7

ARG MYSQL_CONTAINER_USER
ARG MYSQL_CONTAINER_GROUP

RUN chmod 777 -R /var/log/mysql/

RUN chown -R mysql:mysql /var/lib/mysql

RUN sed -i "s/#log-error/log-error/g" /etc/mysql/mysql.conf.d/mysqld.cnf

RUN usermod -u 1000 ${MYSQL_CONTAINER_USER} \
 && groupmod -g 1000 ${MYSQL_CONTAINER_GROUP}