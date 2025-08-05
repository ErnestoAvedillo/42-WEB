COMPOSE = docker compose
LIST_CURRENT_VOLUMES=$(shell docker volume ls -q)

DOCKER_COMPOSE_FILE = ./docker-compose.yml
CERTS=selfsigned.crt selfsigned.key

# Define targets
all: build 

build: 	build_certs 
	$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) build --no-cache
	$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) up -d

build_certs: $(CERTS)

$(CERTS):
	./cert.sh

down:
	$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) down

restart: down
	$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) up -d

logs:
	$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) logs -f

stop : 
	@$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) stop

start : 
	@$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) start

rebuild: down build

re: clean build

status:
	@echo "Checking status of containers..."
	@$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) ps
	@echo "Checking status of images..."
	@$(COMPOSE) -f $(DOCKER_COMPOSE_FILE) images
	@echo "Checking status of volumes..."
	@$(COMPOSE) -f  volume ls
	@echo "Checking status of networks..."
	@$(COMPOSE) -f  network ls

help:
	@echo "$(BLUE_COLOR)Available Makefile targets:$(RESET_COLOR)"
	@echo "$(GREEN_COLOR)  all$(RESET_COLOR)           - Default target, builds the project."
	@echo "$(GREEN_COLOR)  build$(RESET_COLOR)         - Creates volumes, compiles code, and starts services with Docker Compose."
	@echo "$(GREEN_COLOR)  down$(RESET_COLOR)          - Stops and removes all containers managed by Docker Compose."
	@echo "$(GREEN_COLOR)  restart$(RESET_COLOR)       - Restarts all running containers."
	@echo "$(GREEN_COLOR)  logs$(RESET_COLOR)          - Streams logs from all containers."
	@echo "$(GREEN_COLOR)  stop$(RESET_COLOR)          - Stops running containers."
	@echo "$(GREEN_COLOR)  start$(RESET_COLOR)         - Starts stopped containers."
	@echo "$(GREEN_COLOR)  rebuild$(RESET_COLOR)       - Removes files, creates volumes, compiles code, and rebuilds containers."
	@echo "$(GREEN_COLOR)  rm_files$(RESET_COLOR)      - Removes temporary and generated files."
	@echo "$(GREEN_COLOR)  debug$(RESET_COLOR)         - Starts a bash shell (or sh as fallback) in a specified container."
	@echo "$(GREEN_COLOR)  volumes$(RESET_COLOR)       - Creates necessary volume directories and log files."
	@echo "$(GREEN_COLOR)  compile$(RESET_COLOR)       - Installs frontend dependencies using npm."
	@echo "$(GREEN_COLOR)  copy_env$(RESET_COLOR)       - Builds the frontend project using npm."
	@echo "$(GREEN_COLOR)  del_vol$(RESET_COLOR)       - Deletes Docker volumes and certain temporary files."
	@echo "$(GREEN_COLOR)  rm_vol$(RESET_COLOR)        - Removes Docker volumes and cleans up migrations and caches."
	@echo "$(GREEN_COLOR)  clean$(RESET_COLOR)         - Stops running containers."
	@echo "$(GREEN_COLOR)  fclean$(RESET_COLOR)        - Cleans and removes all data, images, and volumes."
	@echo "$(GREEN_COLOR)  re$(RESET_COLOR)            - Fully cleans and rebuilds the project from scratch."
	@echo "$(YELLOW_COLOR)Usage examples:$(RESET_COLOR)"
	@echo "  make build"
	@echo "  make clean"

clean: stop
	@echo "Cleaning up..."
	-docker rm -f $$(docker ps -aq)
	-docker image rm -f $$(docker images -q)
	-docker network rm -f $$(docker network ls -q)
	-docker volume rm -f $$(docker volume ls -q)
	-docker system prune -f
	-rm -rfd ./data

inspect:
	@echo "Inspecting containers..."
	-docker ps -a
	@echo "Inspecting images..."
	-docker images
	@echo "Inspecting volumes..."
	-docker volume ls
	@echo "Inspecting networks..."
	-docker network ls

in:
	@echo "Starting an interactive shell in the specified container..."
	docker exec -it camagru_nginx sh

.PHONY: all build up down restart logs clean re fclean volumes compile run_pm del_vol rm_vol debug build_certs rebuild