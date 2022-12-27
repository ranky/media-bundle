CURRENT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags)
PREVIOUS_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags $(CURRENT_GIT_TAG_VERSION)^)
NEXT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags | awk -F. '{$$NF = $$NF + 1;} 1' | sed 's/ /./g')

##@ Git
git-commit: ## Fast conventional commit https://www.conventionalcommits.org/
	@echo "Committing..."
	git commit -am "ci: update"

git-status: ## Status && tag version
	git status
	@echo "Current version: $(CURRENT_GIT_TAG_VERSION)"
	@echo "Next version: $(NEXT_GIT_TAG_VERSION)"

git-push: ## Git push
	git push --atomic origin main

git-tag: ## create new tag && push
	@echo "Updating tags from remote..."
	git fetch --all --tags --force
	$(eval CURRENT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags))
	$(eval NEXT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags | awk -F. '{$$NF = $$NF + 1;} 1' | sed 's/ /./g'))
	@echo "Creating tag $(NEXT_GIT_TAG_VERSION)"
	git tag $(NEXT_GIT_TAG_VERSION) -m "Releasing version $(NEXT_GIT_TAG_VERSION)"
	@echo "Pushing tag $(NEXT_GIT_TAG_VERSION)..."
	git push --atomic origin main --tags

git-release: git-tag ## Create new tag and release
	@echo "Releasing to $(GIT_REMOTE)..."
	gh release create $(NEXT_GIT_TAG_VERSION) --generate-notes

git-full-release: git-commit git-tag ## Commit, create new tag and release
	@echo "Releasing to $(GIT_REMOTE)..."
	gh release create $(NEXT_GIT_TAG_VERSION) --generate-notes


git-commits-tags: ## Show commits between lasts tags
	git log $(PREVIOUS_GIT_TAG_VERSION)..$(CURRENT_GIT_TAG_VERSION) --pretty=format:"%h %s" --no-merges | sed -E 's/([a-f0-9]+) (.*)/- \2/'

git-compare-tags: ## Generate compare versions changelog
	@echo ""
	@echo "Full Changelog"
	@echo "=============="
	@echo ""
	@echo "$(shell git remote get-url origin | sed 's/\.git//')/compare/$(PREVIOUS_GIT_TAG_VERSION)...$(CURRENT_GIT_TAG_VERSION)"
