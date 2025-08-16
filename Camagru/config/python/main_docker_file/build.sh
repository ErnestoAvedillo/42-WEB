#!/bin/bash

# Build the Docker images
docker build -t eavedillo/python_backend .

# push the containers
docker push eavedillo/python_backend