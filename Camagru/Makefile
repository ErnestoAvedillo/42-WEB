COMPOSE = docker compose
LIST_CURRENT_VOLUMES=$(shell docker volume ls -q)
LIST_DANGLING_IMAGES=$(shell docker image ls -q -f "dangling=true")

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
	@echo "$(GREEN_COLOR)  build_certs$(RESET_COLOR)   - Creates self-signed certificates."
	@echo "$(GREEN_COLOR)  down$(RESET_COLOR)          - Stops and removes all containers managed by Docker Compose."
	@echo "$(GREEN_COLOR)  restart$(RESET_COLOR)       - Restarts all running containers."
	@echo "$(GREEN_COLOR)  logs$(RESET_COLOR)          - Streams logs from all containers."
	@echo "$(GREEN_COLOR)  stop$(RESET_COLOR)          - Stops running containers."
	@echo "$(GREEN_COLOR)  start$(RESET_COLOR)         - Starts stopped containers."
	@echo "$(GREEN_COLOR)  rebuild$(RESET_COLOR)       - Removes files, creates volumes, compiles code, and rebuilds containers."
	@echo "$(GREEN_COLOR)  re$(RESET_COLOR)            - Fully cleans and rebuilds the project from scratch."
	@echo "$(GREEN_COLOR)  status$(RESET_COLOR)        - Displays the status of containers, images, volumes, and networks."
	@echo "$(GREEN_COLOR)  rm_none$(RESET_COLOR)       - Removes all dangling images."
	@echo "$(GREEN_COLOR)  clean$(RESET_COLOR)         - Stops running containers."
	@echo "$(GREEN_COLOR)  inspect$(RESET_COLOR)       - Inspects all containers, images, volumes, and networks."
	@echo "$(GREEN_COLOR)  in$(RESET_COLOR)            - Starts an interactive shell
	@echo "$(YELLOW_COLOR)Usage examples:$(RESET_COLOR)"
	@echo "  make build"
	@echo "  make clean"

rm_none:
	@echo "Remove all images not used."

	@echo "Removing images: $(LIST_DANGLING_IMAGES)"
	-docker image rm $(LIST_DANGLING_IMAGES)


clean: stop
	@echo "Cleaning up..."
	-docker rm -f $$(docker ps -aq)
	-docker image rm -f $$(docker images -q)
	-docker network rm -f $$(docker network ls -q)
	-docker volume rm -f $$(docker volume ls -q)
	-docker system prune -f

# -rm -rfd ./data

inspect:
	@echo "Inspecting containers..."
	-docker ps -a
	@echo "--------------------------"
	@echo "Inspecting images..."
	-docker images
	@echo "--------------------------"
	@echo "Inspecting volumes..."
	-docker volume ls
	@echo "--------------------------"
	@echo "Inspecting networks..."
	-docker network ls

in:
	@echo "Starting an interactive shell in the specified container..."
	docker exec -it camagru_nginx sh

.PHONY: all build up down restart logs clean re fclean volumes compile run_pm del_vol rm_vol debug build_certs rebuild