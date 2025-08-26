FROM eavedillo/php_backend:latest

WORKDIR /var/www/html

RUN composer require mongodb/mongodb
RUN composer require phpmailer/phpmailer
RUN composer require ramsey/uuid

COPY php.ini /usr/local/etc/php/conf.d/
WORKDIR /tmp/

COPY start.sh  ./
RUN chmod +x start.sh

EXPOSE  $PHP_PORT

#ENTRYPOINT [ "bash", "-c", "while true; do sleep 3306; done"]
ENTRYPOINT [ "/tmp/start.sh" ]