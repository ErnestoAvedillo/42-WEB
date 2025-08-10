FROM eavedillo/camagru_backend:latest

RUN composer require mongodb/mongodb
RUN composer require phpmailer/phpmailer

COPY php.ini /usr/local/etc/php/conf.d/

EXPOSE  9000

#ENTRYPOINT [ "bash", "-c", "while true; do sleep 3306; done"]
