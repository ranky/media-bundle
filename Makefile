ENV ?= dev

include .env # Docker
include tests/.env.test # Symfony

.EXPORT_ALL_VARIABLES:

# Configuration
DOCKER_COMPOSE = BUILDKIT_PROGRESS=plain DOCKER_BUILDKIT=1 docker-compose -f docker-compose.yml --env-file .env
SHELL = /bin/bash
.DEFAULT_GOAL = help
.PHONY = docker composer lint test ci phpstan php-cs-fixer behat

## Generic Tools and config
HOST_UID := $(shell id -u)
HOST_GID := $(shell id -g)
DOCKER := DOCKER_BUILDKIT=1 $(shell which docker)
DOCKER_EXEC_PHP := $(DOCKER_COMPOSE) exec php
DOCKER_PHP := $(DOCKER_EXEC_PHP) php
DOCKER_PHP_COMPOSER := $(DOCKER_EXEC_PHP) composer
DOCKER_PHP_SYMFONY_CONSOLE := $(DOCKER_PHP) bin/console

# example: make `docker-php -- ls -la`. The -- means "everything after this is an argument, not an option"
ifeq ($(findstring $(firstword $(MAKECMDGOALS)),$(TARGETS)),)
  ARGUMENTS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(ARGUMENTS):;@:) # forward all arguments provided to 'make' to another program (eval)
endif

## Help command
DOCKER_VERSION=$(shell docker --version)
DOCKER_COMPOSE_VERSION=$(shell docker-compose --version)
help: ## Outputs this help screen
	@echo "Environment $(ENV)"
	@echo "$(DOCKER_VERSION)"
	@echo "$(DOCKER_COMPOSE_VERSION)"
	@echo "Example for targets with arguments: make docker-php -- ls -la. The '--' means everything after this is an argument, not an option"
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-30s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Testing Make variables
test-variables: ## Show all variables
	@echo $$PROJECT_NAME
	@echo $$APP_ENV
	@echo $$APP_DEBUG
	@echo $$PHP_VERSION
	@echo $(MAKECMDGOALS)
	@echo $(SYMFONY_CONSOLE_DOCKER)


##@ General
ci: lint test composer-validate ## All in one


# remember add *.Makefile in Configuration -> Editor -> Files Types in PHPSTORM (GNU Makefile)
-include tools/make/docker.Makefile
-include tools/make/composer.Makefile
-include tools/make/test.Makefile
-include tools/make/lint.Makefile
-include tools/make/tools.Makefile
