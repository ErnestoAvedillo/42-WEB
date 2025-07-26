!#!/bin/bash

# Build the Docker images
docker build -t eavedillo/camagru_backend .

# push the containers
docker push eavedillo/camagru_backend