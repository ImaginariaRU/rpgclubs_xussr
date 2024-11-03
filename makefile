#!/usr/bin/make
PACKAGE_NAME  = rpgclubs
INSTALL_DIR = rpgclubs
PATH_PROJECT = $(DESTDIR)/var/www/$(INSTALL_DIR)
PATH_PUBLIC = $(PATH_PROJECT)/public
CWD=$(shell pwd)

help:
	@perl -e '$(HELP_ACTION)' $(MAKEFILE_LIST)

install:  	##@system Install package. Don't run it manually!!!
	@echo Installing...
	install -d $(PATH_PROJECT)
	cp -r public $(PATH_PROJECT)/
	cp -r engine $(PATH_PROJECT)/
	cp -r admin.cron $(PATH_PROJECT)/
	cp -r admin.tools $(PATH_PROJECT)/
	cp composer.json $(PATH_PROJECT)
#	cp $(PATH_PROJECT)/public/frontend/favicons/favicon.ico $(PATH_PROJECT)/public/
	git rev-parse --short HEAD > $(PATH_PUBLIC)/_version
	git log --oneline --format=%B -n 1 HEAD | head -n 1 >> $(PATH_PUBLIC)/_version
	git log --oneline --format="%at" -n 1 HEAD | xargs -I{} date -d @{} +%Y-%m-%d >> $(PATH_PUBLIC)/_version
	set -e && cd $(PATH_PROJECT)/ && composer install && rm composer.lock
#	cp makefile.production-toolkit $(PATH_PROJECT)/makefile
	chmod -R -x+X $(PATH_PROJECT)/*
	chmod 444 $(PATH_PROJECT)/public/*.php
	install -d $(PATH_PROJECT)/cache
	install -d $(PATH_PROJECT)/logs

update:		##@build Update project from GIT
	@echo Updating project from GIT
	git pull --no-rebase

#make_env:   ##@work Prepare local environment
#	@npm ci

build:	##@build Build DEB-package with gulp
	@dh_clean
#	@./node_modules/.bin/gulp build --production
	export COMPOSER_HOME=/tmp/ && dpkg-buildpackage -rfakeroot -uc -us --compression-level=9 --diff-ignore=node_modules --tar-ignore=node_modules
	@dh_clean

compile:		##@work Compile dev version
	@echo Compiling with GULP
	@./node_modules/.bin/gulp build

dchr:		##@development Publish release
	@dch --controlmaint --release --distribution unstable

dchv:		##@development Append release
	@export DEBEMAIL="karel.wintersky@yandex.ru" && \
	export DEBFULLNAME="Karel Wintersky" && \
	echo "$(YELLOW)------------------ Previous version header: ------------------$(GREEN)" && \
	head -n 3 debian/changelog && \
	echo "$(YELLOW)--------------------------------------------------------------$(RESET)" && \
	read -p "Next version: " VERSION && \
	dch --controlmaint -v $$VERSION
dchc:
	@export DEBEMAIL="karel.wintersky@yandex.ru" && \
	export DEBFULLNAME="Karel Wintersky" && \
	dch --create


# ------------------------------------------------
# Add the following 'help' target to your makefile, add help text after each target name starting with '\#\#'
# A category can be added with @category
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)
HELP_ACTION = \
	%help; while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-_]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
	print "usage: make [target]\n\n"; for (sort keys %help) { print "${WHITE}$$_:${RESET}\n"; \
	for (@{$$help{$$_}}) { $$sep = " " x (32 - length $$_->[0]); print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; }; \
	print "\n"; }

# -eof-
