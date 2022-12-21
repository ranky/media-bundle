##@ Tools/Utils
tools-remove-packages: ## Remove all tools vendor/dependencies
	@find -type d -name vendor | grep tools | xargs rm -rf

tools-update-packages: ## Update all tools vendor/dependencies
	$(DOCKER_EXEC_PHP) bash -c "find ${APP_DIRECTORY}/tools -maxdepth 1 ! -path "${APP_DIRECTORY}/tools/docker" ! -path "${APP_DIRECTORY}/tools/make" -print0 | grep tools/ | xargs -I {} sh -c 'echo "{}"; composer update --no-plugins --no-scripts --working-dir="{}"'"

tools-update-packages-local: ## Update all tools vendor/dependencies in local
	find $(shell pwd)/tools -maxdepth 1 | grep tools/ | xargs -d $\'\n' sh -c 'for arg do echo "$$arg"; composer update --working-dir=$$arg/; done'

generate-ref-flex: ## Generate reference for flex
	$(DOCKER_EXEC_PHP) php -r 'echo bin2hex(random_bytes(20));'
