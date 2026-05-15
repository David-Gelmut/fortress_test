DEV_FILE=docker-compose.yml

init: up ci key migrate-fresh

up:
	docker compose -f $(DEV_FILE) up -d

down:
	docker compose -f $(DEV_FILE) down

build:
	docker compose -f $(DEV_FILE) up -d --build

no-cache:
	docker compose -f $(DEV_FILE) build --no-cache

key:
	docker compose exec -it app php artisan key:generate
migrate:
	docker compose exec -it app php artisan migrate
migrate-fresh: migrate-reset migrate seed
migrate-reset:
	docker compose exec -it app php artisan migrate:reset
seed:
	docker compose exec -it app php artisan db:seed
env:
	cp .env.example .env
ci:
	docker compose exec -it app composer install
cu:
	docker compose exec -it app composer update
clear:
	docker compose exec -it app php artisan optimize:clear
logs-parse:
	docker compose exec -it app php artisan log:parse $(path)

