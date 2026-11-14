CURRENT_GIT_TAG_VERSION = $(shell git describe --abbrev=0 --tags)
PREVIOUS_GIT_TAG_VERSION = $(shell git describe --abbrev=0 --tags $(CURRENT_GIT_TAG_VERSION)^)
NEXT_GIT_TAG_VERSION = $(shell git describe --abbrev=0 --tags | awk -F. '{$$NF = $$NF + 1;} 1' | sed 's/ /./g')
LAST_COMMIT = $(shell git rev-parse --short HEAD)
GIT_REMOTE = $(shell git config --get remote.origin.url)

# No exportar: con .EXPORT_ALL_VARIABLES y GNU Make >= 4.4 cada $(shell) las expandiría y se llamarían entre sí
unexport CURRENT_GIT_TAG_VERSION PREVIOUS_GIT_TAG_VERSION NEXT_GIT_TAG_VERSION LAST_COMMIT GIT_REMOTE

##@ Git
git-fixup: ## Fixup https://jordanelver.co.uk/blog/2020/06/04/fixing-commits-with-git-commit-fixup-and-git-rebase-autosquash/
	git log -n 50 --pretty=format:'%h %s' --no-merges | fzf | cut -c -7 | xargs -o git commit --fixup

git-ci-commit: ## Fast conventional commit https://www.conventionalcommits.org/
	@echo "Committing..."
	git commit -am "ci: update"
	git push --atomic origin HEAD

git-wip-commit: ## Fast commit with WIP: work in progress https://www.dmitriydubson.com/post/trunk-dev-wip-commits/
	@echo "Committing..."
	git commit -am "WIP: "
	git push --atomic origin HEAD

git-push: ## Git push
	git push --atomic origin HEAD

git-fetch: ## Git fetch, prune: remove branches for branches that have been removed from origin
	git fetch --prune

git-update: ## Git fetch and pull
	git fetch origin --prune
	git pull --log origin main

git-undo: ## Undo last commit, soft: undo last commit, but keep changes in working directory
	@echo "Undoing last commit..."
	git reset --soft HEAD~1

# https://stackoverflow.com/questions/4647301/rolling-back-local-and-remote-git-repository-by-1-commit
git-undo-origin: ## Undo last commit in origin with history
	@echo "Undoing last commit in origin..."
	git revert HEAD # optional -no-commit

git-undo-origin-force: ## Undo last commit in origin without history
	@echo "Undoing last commit in origin..."
	git reset HEAD^
	git push origin +HEAD

git-undo-last-remote: ## reset to last commit from remote
	git reset --hard origin/main

git-current: ## Show current branch
	git rev-parse --abbrev-ref HEAD

git-show-last-commit: ## show last short commit
	#git log -1 --pretty=oneline --abbrev-commit
	git rev-parse --short HEAD

git-amend: ## Amend the currently staged files to the latest commit
	git commit --amend --reuse-message=HEAD

git-log: ## show git log
	git log --color --decorate --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an (%G?)>%Creset' --abbrev-commit

git-log-pretty: ## show git log pretty
	git log --graph --color --pretty=format:"%C(yellow)%H%C(green)%d%C(reset)%n%x20%cd%n%x20%cn%x20(%ce)%n%x20%B%n"

git-status: ## Status && tag version
	@git status
	@echo "Current origin: $(GIT_REMOTE)"
	@echo "Current version: $(CURRENT_GIT_TAG_VERSION)"
	@echo "Next version: $(NEXT_GIT_TAG_VERSION)"
	@echo "Last commit: $(LAST_COMMIT)"

git-remove-branch: ## Delete branch from local and remote with prompt
	@read -p "Enter branch name: " branch_name; \
	git push origin --delete $$branch_name; \
	git branch -D $$branch_name

git-gitignore: ## Git ignore rsync https://stackoverflow.com/a/7076075/2046442
	git rm -r --cached . # remove all files from the index
	git add . # add all files back

git-gitignore-remove-file: ## remove ignored files
	git rm -r --cached tests/.env.test
	git commit -m "Remove unnecessary tests/.env.test"
	make git-push

git-patch: ## Generate patch from diff
	git diff --patch > patch.patch

git-patch-apply: ## Apply patch
	git apply patch.patch

git-php-cs-fixer: ## fix with php-cs-fixer and make commit
	make php-cs-fixer-fix-no-tty
	git add .
	git commit -m "style: apply php-cs-fixer"

git-change-log: ## generate change log and make commit
	make tools-generate-change-log
	git add .
	git commit -m "docs: update change log"

git-publish: ## fix with php-cs-fixer, create tag version && push
	make git-change-log
	make git-php-cs-fixer
	make git-tag

git-tag: ## create new tag version && push
	@echo "Updating tags from remote..."
	git fetch --all --tags --force
	$(eval CURRENT_GIT_TAG_VERSION=$(shell git describe --abbrev=0 --tags))
	$(eval NEXT_GIT_TAG_VERSION=$(shell echo $(CURRENT_GIT_TAG_VERSION) | awk -F. '{$$NF = $$NF + 1;} 1' | sed 's/ /./g'))
	@echo "Creating tag $(NEXT_GIT_TAG_VERSION)"
	git tag -a $(NEXT_GIT_TAG_VERSION) -m "Releasing version $(NEXT_GIT_TAG_VERSION)"
	@echo "Pushing tag $(NEXT_GIT_TAG_VERSION)..."
	git push --atomic origin HEAD --tags

git-tag-semver: ## create new semver tag version interactively
	@echo "Updating tags from remote..."
	@git fetch --all --tags --force
	@current_tag=$$(git describe --abbrev=0 --tags 2>/dev/null || true); \
	last_commit=$$(git log -1 --pretty=format:'%h - %s (%cr) <%an>'); \
	current_branch=$$(git rev-parse --abbrev-ref HEAD); \
	dirty_status=$$(git status --porcelain); \
	if [ -z "$$current_tag" ]; then \
		base_tag="v0.0.0"; \
		display_tag="(none)"; \
		commits_since=$$(git rev-list --count HEAD); \
	else \
		base_tag="$$current_tag"; \
		display_tag="$$current_tag"; \
		commits_since=$$(git rev-list --count "$$current_tag"..HEAD); \
	fi; \
	prefix=""; \
	case "$$base_tag" in v*|V*) prefix="v" ;; esac; \
	version_core=$${base_tag#[vV]}; \
	version_core=$${version_core%%[-+]*}; \
	major=$$(echo "$$version_core" | awk -F. '{print $$1+0}'); \
	minor=$$(echo "$$version_core" | awk -F. '{print $$2+0}'); \
	patch=$$(echo "$$version_core" | awk -F. '{print $$3+0}'); \
	next_patch="$${prefix}$${major}.$${minor}.$$((patch + 1))"; \
	next_minor="$${prefix}$${major}.$$((minor + 1)).0"; \
	next_major="$${prefix}$$((major + 1)).0.0"; \
	echo ""; \
	echo "=== Current repository info (read-only) ==="; \
	echo "Origin:            $(GIT_REMOTE)"; \
	echo "Branch:            $$current_branch"; \
	echo "Last version:      $$display_tag"; \
	echo "Last commit:       $$last_commit"; \
	echo "Commits since tag: $$commits_since"; \
	if [ -n "$$dirty_status" ]; then \
		echo "Working tree:      DIRTY (uncommitted changes)"; \
		echo ""; \
		echo "WARNING: There are uncommitted changes that will NOT be included in the tag:"; \
		git status --short; \
		echo ""; \
		echo "Do you want to continue anyway?"; \
		echo "  1 - Yes, continue"; \
		echo "  2 - No, abort (commit changes first)"; \
		printf "Choose an option [1-2]: "; \
		read -r dirty_choice; \
		case "$$dirty_choice" in \
			1) ;; \
			*) echo "Aborted. Commit or stash your changes before creating a tag."; exit 0 ;; \
		esac; \
	else \
		echo "Working tree:      clean"; \
	fi; \
	echo ""; \
	echo "Select SemVer bump:"; \
	echo "  1 - Patch ($$next_patch)"; \
	echo "  2 - Minor ($$next_minor)"; \
	echo "  3 - Major ($$next_major)"; \
	echo ""; \
	printf "Choose an option [1-3]: "; \
	read -r choice; \
	case "$$choice" in \
		1) new_tag="$$next_patch" ;; \
		2) new_tag="$$next_minor" ;; \
		3) new_tag="$$next_major" ;; \
		*) echo "Aborted. No tag was created."; exit 0 ;; \
	esac; \
	if git rev-parse -q --verify "refs/tags/$$new_tag" >/dev/null; then \
		echo "Error: tag $$new_tag already exists."; \
		exit 1; \
	fi; \
	echo "Creating tag $$new_tag..."; \
	git tag -a "$$new_tag" -m "Releasing version $$new_tag"; \
	echo "Tag $$new_tag created locally."; \
	echo ""; \
	echo "Do you want to push tag $$new_tag to origin?"; \
	echo "  1 - Yes (git push --atomic origin HEAD $$new_tag)"; \
	echo "  2 - No (keep local only)"; \
	printf "Choose an option [1-2]: "; \
	read -r push_choice; \
	case "$$push_choice" in \
		1) \
			echo "Pushing tag $$new_tag..."; \
			git push --atomic origin HEAD "$$new_tag" \
			;; \
		2) \
			echo "Push skipped. Tag $$new_tag remains local." \
			;; \
		*) \
			echo "Invalid option. Push skipped; tag $$new_tag remains local." \
			;; \
	esac

## other option with date: https://stackoverflow.com/a/21759466/2046442
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


git-full-release: git-ci-commit git-tag git-create-release-from-last-tag ## Commit, create new tag version and release

git-commits-tags: ## Show commits between lasts tags
	git log $(PREVIOUS_GIT_TAG_VERSION)..$(CURRENT_GIT_TAG_VERSION) --pretty=format:"%h %s" --no-merges | sed -E 's/([a-f0-9]+) (.*)/- \2/'

git-compare-tags: ## Generate compare versions changelog
	@echo ""
	@echo "Full Changelog"
	@echo "=============="
	@echo ""
	@echo "$(shell git remote get-url origin | sed 's/\.git//')/compare/$(PREVIOUS_GIT_TAG_VERSION)...$(CURRENT_GIT_TAG_VERSION)"
