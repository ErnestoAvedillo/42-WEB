FROM nginx:alpine

RUN apt-get update && apt-get install -y \
    vim 
# Copy the Nginx configuration file
COPY nginx.conf /etc/nginx/nginx.conf
#COPY nginx.conf /etc/nginx/conf.d

EXPOSE 8080 

#CMD ["nginx", "-g", "daemon off;"]
#ENTRYPOINT [ "bash", "-c", "while true; do sleep 3306; done"]