PHP_CS_FIXER_DIRECTORY = tools/php-cs-fixer
PHPSTAN_DIRECTORY = tools/phpstan
PHPCS_BIN 	= $(PHP_CS_FIXER_DIRECTORY)/vendor/bin/php-cs-fixer
PHPSTAN_BIN = $(PHPSTAN_DIRECTORY)/vendor/bin/phpstan

##@ Lint
php-cs-fixer: ## Lint with php-cs-fixer --stop-on-violation
	PHP_CS_FIXER_FUTURE_MODE=1 $(DOCKER_EXEC_PHP) bash -c "$(PHPCS_BIN) fix --config=$(PHP_CS_FIXER_DIRECTORY)/.php-cs-fixer.dist.php --dry-run --verbose --diff --format=txt > $(PHP_CS_FIXER_DIRECTORY)/phpcs.md"

php-cs-fixer-fix: ## Lint and fix files with php-cs-fixer --stop-on-violation
	PHP_CS_FIXER_FUTURE_MODE=1 $(DOCKER_EXEC_PHP) bash -c "$(PHPCS_BIN) fix --config=$(PHP_CS_FIXER_DIRECTORY)/.php-cs-fixer.dist.php --diff > $(PHP_CS_FIXER_DIRECTORY)/phpcs.md"

phpstan-clear: ## Run PHPStan clear
	$(DOCKER_EXEC_PHP) bash -c "$(PHPSTAN_BIN) clear-result-cache -c $(PHPSTAN_DIRECTORY)/phpstan.neon"

phpstan: ## Run PHPStan Example: -c phpstan.neon --memory-limit 1G --no-progress --no-interaction
	$(DOCKER_EXEC_PHP) bash -c "$(PHPSTAN_BIN) analyse -c $(PHPSTAN_DIRECTORY)/phpstan.neon --error-format=table > $(PHPSTAN_DIRECTORY)/phpstan.md"

lint: phpstan-clear phpstan php-cs-fixer ## All in one for lint tools

##@ Validate

validate-composer: ## Composer validate && diagnose && audit
	$(DOCKER_EXEC_PHP) bash -c "composer validate && composer diagnose && composer audit"
