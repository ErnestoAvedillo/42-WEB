!#!/bin/bash

# Build the Docker images
docker build -t eavedillo/php_backend .

# push the containers
docker push eavedillo/php_backend