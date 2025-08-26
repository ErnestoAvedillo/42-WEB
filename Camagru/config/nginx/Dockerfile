FROM nginx:alpine

RUN apk update && apk add --no-cache vim 
# Copy the Nginx configuration file
WORKDIR /etc/nginx
COPY nginx.conf.template nginx.conf.template
COPY start.sh start.sh
RUN chmod +x start.sh

#COPY nginx.conf /etc/nginx/conf.d

#this is the normal start
#CMD ["nginx", "-g", "daemon off;"]
#this is the debug mode start
#ENTRYPOINT [ "sh", "-c", "while true; do sleep 3306; done"]
#this is the specific start 
CMD ["/etc/nginx/start.sh"]