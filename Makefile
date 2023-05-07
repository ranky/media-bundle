TMP_ENV := $(DOCKER_ENV)
CI_ENABLED = $(if $(CI),true,false)
ENV_FILE = .env

### Docker Environment ###
ifneq (,$(wildcard .env.local))
	ENV_FILE = .env.local
else ifneq (,$(wildcard .env.$(DOCKER_ENV)))
	ENV_FILE = .env.$(DOCKER_ENV)
else
	ENV_FILE = .env
endif

include $(ENV_FILE)

### Symfony Test Environment  ###
ifneq (,$(wildcard tests/.env.local))
	include tests/.env.local
else ifneq (,$(wildcard tests/.env.$(DOCKER_ENV)))
	include tests/.env.$(DOCKER_ENV)
else
	include tests/.env
endif

# override DOCKER_ENV from parameter if TMP_ENV is not empty
ifneq ($(TMP_ENV),)
	override DOCKER_ENV = $(TMP_ENV)
endif

ifneq ($(DB_CONNECTION_OVERRIDE),)
	override DB_CONNECTION = $(DB_CONNECTION_OVERRIDE)
endif

.EXPORT_ALL_VARIABLES:

## Configuration ##
HOST_UID ?= $(shell id -u)
HOST_GID ?= $(shell id -g)
HOST_IP = $(shell hostname -I | awk '{print $1}')

ifeq ($(CI_ENABLED),true)
	# CI, Github Actions, runs-on: ubuntu-latest
	SHELL = /bin/bash
    HOST_UID = 1001
    HOST_GID = 1001
    DOCKER_COMPOSE := DOCKER_ENV=$(DOCKER_ENV) HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) BUILDKIT_PROGRESS=plain DOCKER_BUILDKIT=1 docker compose -f docker-compose.yml -f docker-compose.ci.yml --env-file $(ENV_FILE)
    DOCKER_EXEC_PHP := $(DOCKER_COMPOSE) exec -e DB_CONNECTION=$(DB_CONNECTION)  -T php
    DOCKER_EXEC_ROOT_PHP := $(DOCKER_COMPOSE) exec -e DB_CONNECTION=$(DB_CONNECTION) -T -u root php
else
	SHELL = /bin/zsh
	DOCKER_COMPOSE := DOCKER_ENV=$(DOCKER_ENV) HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) BUILDKIT_PROGRESS=plain DOCKER_BUILDKIT=1 docker compose -f docker-compose.yml --env-file $(ENV_FILE)
	DOCKER_EXEC_PHP := $(DOCKER_COMPOSE) exec -e DB_CONNECTION=$(DB_CONNECTION) php
    DOCKER_EXEC_ROOT_PHP := $(DOCKER_COMPOSE) exec -e DB_CONNECTION=$(DB_CONNECTION) -u root php
endif

.DEFAULT_GOAL = help
.PHONY = docker composer lint test ci phpstan php-cs-fixer behat

## Generic Tools and config ##
DOCKER := DOCKER_BUILDKIT=1 $(shell which docker)
DOCKER_PHP := $(DOCKER_EXEC_PHP) php
DOCKER_PHP_COMPOSER := $(DOCKER_EXEC_PHP) composer --working-dir="$(APP_DIRECTORY)"
DOCKER_PHP_SYMFONY_CONSOLE := $(DOCKER_PHP) bin/console

# example: make `docker-php -- ls -la`. The -- means "everything after this is an argument, not an option"
ifeq ($(findstring $(firstword $(MAKECMDGOALS)),$(TARGETS)),)
  ARGUMENTS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(ARGUMENTS):;@:) # forward all arguments provided to 'make' to another program (eval)
endif

## Help command
DOCKER_VERSION=$(shell docker --version)
DOCKER_COMPOSE_VERSION=$(shell docker compose version)
help: ## Show this help
	@echo "Environment $(DOCKER_ENV)"
	@echo "$(DOCKER_VERSION)"
	@echo "$(DOCKER_COMPOSE_VERSION)"
	@echo "Example for targets with arguments: make docker-php -- ls -la. The '--' means everything after this is an argument, not an option"
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-30s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Testing Make variables
test-variables:  ## Show all variables
	@echo "APP_ENV: $(APP_ENV)"
	@echo "DOCKER_ENV: $(DOCKER_ENV)"
	@echo "ENV_FILE: $(ENV_FILE)"
	@echo "HOST_UID: $(HOST_UID)"
	@echo "HOST_GID: $(HOST_GID)"
	@echo "MYSQL_DATABASE_URL: $(MYSQL_DATABASE_URL)"
	@echo "POSTGRES_DATABASE_URL: $(POSTGRES_DATABASE_URL)"
	@echo "DB_CONNECTION: $(DB_CONNECTION)"


##@ General
ci: lint test composer-validate ## All in one
lint: phpstan-clear phpstan php-cs-fixer ## All in one for lint tools
test: phpunit behat ## All in one for test tools

# remember add *.Makefile in Configuration -> Editor -> Files Types in PHPSTORM (GNU Makefile)
-include tools/make/docker.Makefile
-include tools/make/composer.Makefile
-include tools/make/test.Makefile
-include tools/make/lint.Makefile
-include tools/make/tools.Makefile
-include tools/make/git.Makefile
