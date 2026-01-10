.PHONY: install update clean dev load assets optimize setup

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "setup		set up the original test project"
	@echo "reload_dev	For debugging purposes, make a simple Docker reload"
	@echo "*_dev		More dev related commands, look in code"


setup:
	composer install
	yarn install
	npm run dev
	#php bin/console doctrine:schema:update --force # not needed for docker-based app
	docker-compose down -v
	docker-compose rm -f
	docker volume prune -f
	docker-compose build --no-cache && docker-compose up -d

reload_dev:
	sudo chown -R 33:33 .
	docker restart symfony_php

webr_dev:
	sudo chown -R $$(whoami):$$(whoami) .
	npm run dev
	sudo chown -R 33:33 .
	docker restart symfony_php

edit_dev:
	sudo chown -R $$(whoami):$$(whoami) .

rebuild_dev:
	sudo chown -R $$(whoami):$$(whoami) .
	npm run dev
	sudo chown -R 33:33 .
	docker-compose down --volumes && docker-compose build --no-cache && docker-compose up -d
	sudo chown -R 33:33 .
