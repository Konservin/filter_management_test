.PHONY: install update clean dev load assets optimize setup

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "setup		set up the original test project"
	@echo "reload_dev	For debugging purposes, make a simple Docker reload"
	@echo "*_dev		More dev related commands, look in code"

setup:
	docker compose up -d --build
	docker compose exec php composer install
	docker compose exec php yarn install
	docker compose exec php yarn encore dev
	#docker compose exec php php bin/console doctrine:migrations:migrate -n

reset:
	docker compose down -v --remove-orphans
	docker compose up -d --build
	docker compose exec php composer install
	docker compose exec php yarn install
	docker compose exec php yarn encore dev
	#docker compose exec php php bin/console

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
