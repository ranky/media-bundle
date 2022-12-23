##@ Composer
composer: ## Composer with arguments
	$(DOCKER_PHP_COMPOSER) $(ARGUMENTS)

composer-validate: ## Composer validate
	$(DOCKER_PHP_COMPOSER) validate

composer-install: ## Install vendors according to the current composer.lock file
	$(DOCKER_PHP_COMPOSER) install --no-scripts

composer-update: ## Update vendors according to the current composer.lock file
	$(DOCKER_PHP_COMPOSER) update --no-scripts

composer-dump-autoload: ## Update vendors according to the current composer.lock file
	$(DOCKER_PHP_COMPOSER) dump-autoload

composer-optimize: ## Optimize autoloader
	$(DOCKER_PHP_COMPOSER) dump-autoload --optimize

composer-check-updates: ## Checks if any packages will be update
	$(DOCKER_PHP_COMPOSER) outdated

composer-self-update: ## Self update composer
	$(DOCKER_COMPOSE) exec -u root php composer self-update

composer-self-update-keys: ## Self update composer with keys https://composer.github.io/pubkeys.html
	$(DOCKER_COMPOSE) exec -u root php composer self-update --update-keys

composer-normalize-dry: ## Normalizes `composer.json` content
	$(DOCKER_PHP_COMPOSER) normalize --dry-run

composer-update-bin: ## Update composer bin dependencies
	$(DOCKER_PHP) -d memory_limit=-1 $(COMPOSER_BIN) bin all update --no-progress --optimize-autoloader
