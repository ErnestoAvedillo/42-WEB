FROM node:current-alpine3.22
WORKDIR /tmp/
COPY package.json ./
COPY start.sh ./
RUN chmod +x start.sh
EXPOSE 3000
#CMD ["npm", "run", "dev"]
#ENTRYPOINT [ "sh", "-c", "while true; do sleep 3306; done"]
ENTRYPOINT [ "/tmp/start.sh" ]