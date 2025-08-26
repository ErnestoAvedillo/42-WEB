#!/bin/sh
envsubst '${APP_PORT} ${PHP_PORT} ${PYTHON_PORT} ${NODE_PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf
exec nginx -g 'daemon off;'