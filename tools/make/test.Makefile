PHPUNIT_DIRECTORY = tools/phpunit
BEHAT_DIRECTORY = tools/behat
PHPUNIT_BIN = vendor/bin/phpunit
SIMPLE_PHPUNIT_BIN = vendor/bin/simple-phpunit
BEHAT_BIN = $(BEHAT_DIRECTORY)/vendor/bin/behat

##@ Testing
phpunit: ## PHPUnit full test
	$(DOCKER_EXEC_PHP) bash -c "$(PHPUNIT_BIN) --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist"

phpunit-single: ## PHPUnit single file
	$(DOCKER_EXEC_PHP) bash -c "$(PHPUNIT_BIN) --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist ./tests/src/UserSecurity/LoginTest.php"

phpunit-testsuite: ## PHPUnit testsuite
	$(DOCKER_EXEC_PHP) bash -c "$(PHPUNIT_BIN) --configuration $(PHPUNIT_DIRECTORY)/phpunit.xml.dist --testsuite user_security"

behat: ## behat
	$(DOCKER_EXEC_PHP) bash -c "$(BEHAT_BIN) --config $(BEHAT_DIRECTORY)/behat.yml --stop-on-failure --snippets-for -vv"

test: phpunit behat ## TEST
