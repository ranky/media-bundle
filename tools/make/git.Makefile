CURRENT_GIT_TAG_VERSION = $(shell git describe --abbrev=0 --tags)
PREVIOUS_GIT_TAG_VERSION = $(shell git describe --abbrev=0 --tags $(CURRENT_GIT_TAG_VERSION)^)
NEXT_GIT_TAG_VERSION = $(shell git describe --abbrev=0 --tags | awk -F. '{$$NF = $$NF + 1;} 1' | sed 's/ /./g')
LAST_COMMIT = $(shell git rev-parse --short HEAD)
GIT_REMOTE = $(shell git config --get remote.origin.url)

##@ Git
git-commit: ## Fast conventional commit https://www.conventionalcommits.org/
	@echo "Committing..."
	git commit -am "ci: update"

git-show-last-commit: ## show last short commit
	#git log -1 --pretty=oneline --abbrev-commit
	git rev-parse --short HEAD

git-log: ## show last 10 commits
	git log -10 --pretty=oneline --abbrev-commit

git-status: ## Status && tag version
	git status
	@echo "Git remote: $(GIT_REMOTE)"
	@echo "Current version: $(CURRENT_GIT_TAG_VERSION)"
	@echo "Next version: $(NEXT_GIT_TAG_VERSION)"
	@echo "LAST_COMMIT: $(LAST_COMMIT)"

git-push: ## Git push
	git push --atomic origin main

git-tag: ## create new tag version && push
	@echo "Updating tags from remote..."
	git fetch --all --tags --force
	$(eval CURRENT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags))
	$(eval NEXT_GIT_TAG_VERSION=$(shell echo $(CURRENT_GIT_TAG_VERSION) | awk -F. '{$$NF = $$NF + 1;} 1' | sed 's/ /./g'))
	@echo "Creating tag $(NEXT_GIT_TAG_VERSION)"
	git tag -a $(NEXT_GIT_TAG_VERSION) -m "Releasing version $(NEXT_GIT_TAG_VERSION)"
	@echo "Pushing tag $(NEXT_GIT_TAG_VERSION)..."
	git push --atomic origin main --tags

## with date: https://stackoverflow.com/a/21759466/2046442
git-tag-from-last-commit: ## create new tag version from last commit && push
	@echo "Updating tags from remote..."
	git fetch --all --tags --force
	$(eval CURRENT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags))
	$(eval NEXT_GIT_TAG_VERSION=$(shell echo $(CURRENT_GIT_TAG_VERSION) | awk -F. '{$$NF = $$NF + 1;} 1' | sed 's/ /./g'))
	$(eval LAST_COMMIT=$(shell git rev-parse --short HEAD))
	@echo "Creating tag $(NEXT_GIT_TAG_VERSION) with last commit $(LAST_COMMIT)"
	git tag -a $(NEXT_GIT_TAG_VERSION) $(LAST_COMMIT) -m "Releasing version $(NEXT_GIT_TAG_VERSION)"
	@echo "Pushing tag $(NEXT_GIT_TAG_VERSION)..."
	git push --atomic origin $(NEXT_GIT_TAG_VERSION)

git-tag-update: ## update tag && push
	@echo "Updating tags from remote..."
	git fetch --all --tags --force
	$(eval CURRENT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags))
	@echo "Updating tag $(CURRENT_GIT_TAG_VERSION)"
	git tag -fa $(CURRENT_GIT_TAG_VERSION) -m "Update version $(CURRENT_GIT_TAG_VERSION)"
	git push --atomic origin $(CURRENT_GIT_TAG_VERSION) --force

git-create-release-from-last-tag: ## Create release
	$(eval CURRENT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags))
	@echo "Releasing $(CURRENT_GIT_TAG_VERSION) to $(GIT_REMOTE)..."
	gh release create $(CURRENT_GIT_TAG_VERSION) --title $(CURRENT_GIT_TAG_VERSION) --generate-notes


git-full-release: git-commit git-tag git-create-release-from-last-tag ## Commit, create new tag version and release

git-commits-tags: ## Show commits between lasts tags
	git log $(PREVIOUS_GIT_TAG_VERSION)..$(CURRENT_GIT_TAG_VERSION) --pretty=format:"%h %s" --no-merges | sed -E 's/([a-f0-9]+) (.*)/- \2/'

git-compare-tags: ## Generate compare versions changelog
	@echo ""
	@echo "Full Changelog"
	@echo "=============="
	@echo ""
	@echo "$(shell git remote get-url origin | sed 's/\.git//')/compare/$(PREVIOUS_GIT_TAG_VERSION)...$(CURRENT_GIT_TAG_VERSION)"
