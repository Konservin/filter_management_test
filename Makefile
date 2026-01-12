.PHONY: test setup reset webr_dev

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "setup		set up the original test project"
	@echo "reset		reset project"
	@echo "webr			For debugging purposes, make a simple encore reload"

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
	docker compose exec php php bin/console cache:clear

webr_dev:
	docker compose exec php yarn encore dev

test:
	docker compose exec php php bin/console cache:clear --env=test
	docker compose exec -e APP_ENV=test php php bin/phpunit
