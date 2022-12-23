TMP_ENV := $(DOCKER_ENV)
CI_ENABLED = $(if $(CI),true,false)
include .env # Docker

### Start: only for test ###
include tests/.env # Symfony test env
# if exists .env.test, include it and override .env
ifneq (,$(wildcard .env.test))
	include tests/.env.test
endif
### End: only for test ###

# override DOCKER_ENV from parameter if TMP_ENV is not empty
ifneq ($(TMP_ENV),)
	override DOCKER_ENV = $(TMP_ENV)
endif
# if exists .env.$(DOCKER_ENV), include it and override .env
ifneq (,$(wildcard .env.$(DOCKER_ENV)))
	include .env.$(DOCKER_ENV)
endif

.EXPORT_ALL_VARIABLES:

## Configuration ##
HOST_UID ?= $(shell id -u)
HOST_GID ?= $(shell id -g)
ifeq ($(CI_ENABLED),true)
    HOST_UID = 1001
    HOST_GID = 1001
endif
# --env-file .env is necessary If I'm not going to use Make
DOCKER_COMPOSE = DOCKER_ENV=$(DOCKER_ENV) HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) BUILDKIT_PROGRESS=plain DOCKER_BUILDKIT=1 docker-compose -f docker-compose.yml
SHELL = /bin/bash
.DEFAULT_GOAL = help
.PHONY = docker composer lint test ci phpstan php-cs-fixer behat

## Generic Tools and config ##
DOCKER := DOCKER_BUILDKIT=1 $(shell which docker)
DOCKER_EXEC_PHP := $(DOCKER_COMPOSE) exec -T php
DOCKER_EXEC_ROOT_PHP := $(DOCKER_COMPOSE) exec -T -u root php
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
DOCKER_COMPOSE_VERSION=$(shell docker-compose --version)
help: ## Outputs this help screen
	@echo "Environment $(DOCKER_ENV)"
	@echo "$(DOCKER_VERSION)"
	@echo "$(DOCKER_COMPOSE_VERSION)"
	@echo "Example for targets with arguments: make docker-php -- ls -la. The '--' means everything after this is an argument, not an option"
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-30s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Testing Make variables
test-variables:  ## Show all variables
	@echo "APP_ENV: $(APP_ENV)"
	@echo "DOCKER_ENV: $(DOCKER_ENV)"
	@echo "HOST_UID: $(HOST_UID)"
	@echo "HOST_GID: $(HOST_GID)"
	@echo "PROJECT_NAME: $$PROJECT_NAME"
	@echo "APP_DIRECTORY: $$APP_DIRECTORY"
	@echo "SYMFONY APP_ENV: $$APP_ENV"
	@echo "SYMFONY APP_DEBUG: $$APP_DEBUG"
	@echo "PHP_VERSION: $$PHP_VERSION"
	@echo $(MAKECMDGOALS)
	make docker -- config



##@ General
ci: lint test composer-validate ## All in one


# remember add *.Makefile in Configuration -> Editor -> Files Types in PHPSTORM (GNU Makefile)
-include tools/make/docker.Makefile
-include tools/make/composer.Makefile
-include tools/make/test.Makefile
-include tools/make/lint.Makefile
-include tools/make/tools.Makefile
