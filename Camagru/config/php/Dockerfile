FROM eavedillo/php_backend:latest

RUN composer require mongodb/mongodb
RUN composer require phpmailer/phpmailer
RUN composer require ramsey/uuid

COPY php.ini /usr/local/etc/php/conf.d/

EXPOSE  9000

#ENTRYPOINT [ "bash", "-c", "while true; do sleep 3306; done"]
