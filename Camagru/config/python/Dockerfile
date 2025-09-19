FROM eavedillo/python_backend:latest

COPY . /tmp/

WORKDIR /var/www/html/python/

#ENTRYPOINT [ "bash", "-c", "while true; do sleep 3306; done"]
CMD ["uvicorn", "main:app", "--reload", "--host", "0.0.0.0", "--port", "6000"]
