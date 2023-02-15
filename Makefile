up:
	docker compose up -d

down:
	docker compose down --remove-orphans

restart: down up

build:
	docker compose build --pull

# Composer
composer-install:
	docker compose exec app-php composer install
composer-update:
	docker compose exec app-php composer update -W
composer-validate:
	docker compose exec app-php composer validate --strict

# Migrations
migrate-diff:
	docker compose exec app-php bin/console make:migration -q
migrate:
	docker compose exec app-php bin/console doctrine:migrations:migrate
migrate-prev:
	docker compose exec app-php bin/console doctrine:migrations:migrate prev -q

# Quality and health
phpstan:
	docker compose exec app-php composer run phpstan
phpcs:
	docker compose exec app-php composer run phpcs
phpcbf:
	docker compose exec app-php composer run phpcbf
test:
	docker compose exec app-php composer run paratest

run:
	docker compose exec app-php bin/console app:news:parse -s br
