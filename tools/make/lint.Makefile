PHP_CS_FIXER_DIRECTORY = $(APP_DIRECTORY)/tools/php-cs-fixer
PHPSTAN_DIRECTORY = $(APP_DIRECTORY)/tools/phpstan
PHPCS_BIN 	= $(PHP_CS_FIXER_DIRECTORY)/vendor/bin/php-cs-fixer
PHPSTAN_BIN = $(PHPSTAN_DIRECTORY)/vendor/bin/phpstan

##@ Lint
php-cs-fixer: ## Lint with php-cs-fixer --stop-on-violation
	PHP_CS_FIXER_FUTURE_MODE=1 $(DOCKER_EXEC_PHP) bash -c "$(PHPCS_BIN) fix --config=$(PHP_CS_FIXER_DIRECTORY)/.php-cs-fixer.dist.php --dry-run --verbose --diff --format=txt > $(PHP_CS_FIXER_DIRECTORY)/phpcs.md"

php-cs-fixer-ci: ## php-cs-fixer as root for CI
	PHP_CS_FIXER_FUTURE_MODE=1 $(DOCKER_EXEC_ROOT_PHP) bash -c "$(PHPCS_BIN) fix --config=$(PHP_CS_FIXER_DIRECTORY)/.php-cs-fixer.dist.php --dry-run --verbose --diff --format=txt --stop-on-violation"

php-cs-fixer-fix: ## Lint and fix files with php-cs-fixer
	PHP_CS_FIXER_FUTURE_MODE=1 $(DOCKER_EXEC_PHP) bash -c "$(PHPCS_BIN) fix --config=$(PHP_CS_FIXER_DIRECTORY)/.php-cs-fixer.dist.php --diff"

php-cs-fixer-fix-arg: ## Lint and fix single file with php-cs-fixer for PHPSTORM
	PHP_CS_FIXER_FUTURE_MODE=1 $(DOCKER_EXEC_PHP) bash -c "$(PHPCS_BIN) fix --config=$(PHP_CS_FIXER_DIRECTORY)/.php-cs-fixer.dist.php $(ARGUMENTS)"

phpstan-clear: ## Run PHPStan clear cache
	$(DOCKER_EXEC_PHP) bash -c "$(PHPSTAN_BIN) clear-result-cache -c $(PHPSTAN_DIRECTORY)/phpstan.neon"

phpstan: ## Run PHPStan Example: -c phpstan.neon --memory-limit 1G --no-progress --no-interaction
	$(DOCKER_EXEC_PHP) bash -c "$(PHPSTAN_BIN) analyse -c $(PHPSTAN_DIRECTORY)/phpstan.neon --error-format=table > $(PHPSTAN_DIRECTORY)/phpstan.md"

phpstan-single: ## Run PHPStan Example: -c phpstan.neon --memory-limit 1G --no-progress --no-interaction
	$(DOCKER_EXEC_PHP) bash -c "$(PHPSTAN_BIN) analyse -c $(PHPSTAN_DIRECTORY)/phpstan.neon --error-format=table $(APP_DIRECTORY)/src/Domain/Service/FileCompressHandler.php"

phpstan-ci: ## Run PHPStan as root for CI
	$(DOCKER_EXEC_ROOT_PHP) bash -c "$(PHPSTAN_BIN) analyse -c $(PHPSTAN_DIRECTORY)/phpstan.neon --no-progress --no-interaction"

lint: phpstan-clear phpstan php-cs-fixer ## All in one for lint tools

##@ Validate

validate-composer: ## Composer validate && diagnose && audit
	$(DOCKER_EXEC_PHP) bash -c "composer validate && composer diagnose && composer audit"
