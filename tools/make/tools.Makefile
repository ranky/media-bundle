##@ Tools/Utils

tools-install: ## Install tools
	$(DOCKER_EXEC_PHP) bash -c "composer install --no-ansi --no-interaction --no-plugins --no-scripts --working-dir="$(APP_DIRECTORY)/tools/behat""
	$(DOCKER_EXEC_PHP) bash -c "composer install --no-ansi --no-interaction --no-plugins --no-scripts --working-dir="$(APP_DIRECTORY)/tools/phpstan""
	$(DOCKER_EXEC_PHP) bash -c "composer install --no-ansi --no-interaction --no-plugins --no-scripts --working-dir="$(APP_DIRECTORY)/tools/php-cs-fixer""

tools-install-packages-container: ## Update all tools vendor/dependencies
	$(DOCKER_EXEC_PHP) bash -c "find ${APP_DIRECTORY}/tools -maxdepth 1 ! -path "${APP_DIRECTORY}/tools/docker" ! -path "${APP_DIRECTORY}/tools/make" -print0 | grep tools/ | xargs -I {} sh -c 'echo "{}"; composer install --no-ansi --no-interaction --no-plugins --no-scripts --working-dir="{}"'"

tools-update-packages-container: ## Update all tools vendor/dependencies
	$(DOCKER_EXEC_PHP) bash -c "find ${APP_DIRECTORY}/tools -maxdepth 1 ! -path "${APP_DIRECTORY}/tools/docker" ! -path "${APP_DIRECTORY}/tools/make" -print0 | grep tools/ | xargs -I {} sh -c 'echo "{}"; composer update --no-ansi --no-interaction --no-plugins --no-scripts --working-dir="{}"'"

tools-update-packages-local: ## Install all tools vendor/dependencies in local
	find $(shell pwd)/tools -maxdepth 1 | grep tools/ | xargs -d $\'\n' sh -c 'for arg do echo "$$arg"; composer update --no-ansi --no-interaction --no-progress --no-scripts --working-dir=$$arg/; done'

tools-remove-packages: ## Remove all tools vendor/dependencies
	@find -type d -name vendor | grep tools | xargs rm -rf

generate-ref-flex: ## Generate reference for flex
	$(DOCKER_EXEC_PHP) php -r 'echo bin2hex(random_bytes(20));'

clear-symfony-cache: ## Clear symfony cache
	$(DOCKER_EXEC_ROOT_PHP) rm -rf /tmp/*

fix-permissions: ## Fix local permissions
	$(DOCKER_EXEC_ROOT_PHP) chown -R $(HOST_UID):$(HOST_GID) $(APP_DIRECTORY)
	$(DOCKER_EXEC_ROOT_PHP) chmod -R 777 $(APP_DIRECTORY)/tools
	$(DOCKER_EXEC_ROOT_PHP) chmod -R 777 $(APP_DIRECTORY)/tests/public/uploads/
	$(DOCKER_EXEC_ROOT_PHP) chmod -R g+s $(APP_DIRECTORY)/tests/public/uploads/
