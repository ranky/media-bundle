ENV ?= dev

include .env # Docker
include tests/.env.test # symfony
DOCKER_COMPOSE = BUILDKIT_PROGRESS=plain DOCKER_BUILDKIT=1 docker-compose -f docker-compose.yml --env-file .env
SHELL = /bin/bash

.EXPORT_ALL_VARIABLES:

# Configuration
.DEFAULT_GOAL = help
.PHONY = docker composer lint test ci phpstan php-cs-fixer behat

## Generic Tools and config
HOST_UID 	  			   := $(shell id -u)
HOST_GID 	  			   := $(shell id -g)
DOCKER        			   := DOCKER_BUILDKIT=1 $(shell which docker)
DOCKER_EXEC_PHP   		   := $(DOCKER_COMPOSE) exec php
DOCKER_PHP 				   := $(DOCKER_EXEC_PHP) php
DOCKER_PHP_COMPOSER  	   := $(DOCKER_EXEC_PHP) composer
DOCKER_PHP_SYMFONY_CONSOLE := $(DOCKER_PHP) bin/console

# example: make docker-php -- ls -la, The -- means "everything after this is an argument, not an option"
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
	@echo "Example for targets with arguments: make docker-php -- ls -la . The '--' means everything after this is an argument, not an option"
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-30s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Testing Make variable
test-variables: ## Test variables
	@echo $$PROJECT_NAME
	@echo $$APP_ENV
	@echo $$APP_DEBUG
	@echo $$PHP_VERSION
	@echo $(MAKECMDGOALS)
	@echo $(SYMFONY_CONSOLE_DOCKER)


##@ General
ci: lint test composer-validate # Lint and Test

clear-tmp: # clean tmp cache bundle
	$(DOCKER_EXEC_PHP) rm -rf /tmp/*

#fix-permissions: # Fix permissions
#	$(DOCKER_COMPOSE) exec -u root php bash -c "usermod -u $(HOST_UID) www-data && groupmod -g $(HOST_GID) www-data"
# 	sudo chmod 777 tests/public/uploads/

##@ Tools
tools-remove-packages: ## Remove all tools vendor/dependencies
	@find -type d -name vendor | grep tools | xargs rm -rf

tools-update-packages: ## Update all tools vendor/dependencies
	$(DOCKER_EXEC_PHP) bash -c "find ${APP_DIRECTORY}/tools -maxdepth 1 ! -path "${APP_DIRECTORY}/tools/docker" ! -path "${APP_DIRECTORY}/tools/make" -print0 | grep tools/ | xargs -I {} sh -c 'echo "{}"; composer update --no-plugins --no-scripts --working-dir="{}"'"

tools-update-packages-local: ## Update all tools vendor/dependencies in local
	find $(shell pwd)/tools -maxdepth 1 | grep tools/ | xargs -d $\'\n' sh -c 'for arg do echo "$$arg"; composer update --working-dir=$$arg/; done'

generate-ref-flex: ## Generate reference for flex
	$(DOCKER_EXEC_PHP) php -r 'echo bin2hex(random_bytes(20));'


# remember add *.Makefile Configuration -> Editor -> Files Types in PHPSTORM (GNU Makefile)
-include tools/make/docker.Makefile
-include tools/make/composer.Makefile
-include tools/make/test.Makefile
-include tools/make/lint.Makefile
