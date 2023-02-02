PHPUNIT_DIRECTORY = $(APP_DIRECTORY)/tools/phpunit
BEHAT_DIRECTORY = $(APP_DIRECTORY)/tools/behat
PHPUNIT_BIN = $(APP_DIRECTORY)/vendor/bin/phpunit
SIMPLE_PHPUNIT_BIN = $(APP_DIRECTORY)/vendor/bin/simple-phpunit
BEHAT_BIN = $(BEHAT_DIRECTORY)/vendor/bin/behat

##@ Testing
phpunit: ## PHPUnit --exclude-group aws_s3
	$(DOCKER_EXEC_PHP) bash -c "$(PHPUNIT_BIN) --exclude-group aws_s3 --do-not-cache-result --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist"

phpunit-aws: ## PHPUnit --group aws_s3
	$(DOCKER_EXEC_PHP) bash -c "$(PHPUNIT_BIN) --group aws_s3 --do-not-cache-result --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist"

phpunit-ci: ## PHPUnit as root for CI
	$(DOCKER_EXEC_ROOT_PHP) bash -c "$(PHPUNIT_BIN) --exclude-group aws_s3 --do-not-cache-result --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist"

phpunit-single: ## PHPUnit single file
	$(DOCKER_EXEC_PHP) bash -c "$(PHPUNIT_BIN) --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist $(APP_DIRECTORY)/tests/src/UserSecurity/LoginTest.php"

phpunit-testsuite: ## PHPUnit testsuite
	$(DOCKER_EXEC_PHP) bash -c "$(PHPUNIT_BIN) --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist --testsuite user_security"

behat: ## behat
	$(DOCKER_EXEC_PHP) bash -c "$(BEHAT_BIN) --config $(BEHAT_DIRECTORY)/behat.yml --suite=api --stop-on-failure --snippets-for -vv"

behat-s3: ## behat test in aws s3 storage
	$(DOCKER_EXEC_PHP) bash -c "$(BEHAT_BIN) --config $(BEHAT_DIRECTORY)/behat.yml --suite=s3_storage_api --stop-on-failure --snippets-for -vv"

behat-ci: ## behat as root for CI
	$(DOCKER_EXEC_ROOT_PHP) bash -c "$(BEHAT_BIN) --config $(BEHAT_DIRECTORY)/behat.yml --stop-on-failure -vv"

test: phpunit behat ## All in one for test tools
