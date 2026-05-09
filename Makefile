.PHONY: up down remove build init

build:
	docker compose build

up:
	docker compose up -d

init: up
	docker compose exec -T php sh -lc '[ -f .env ] || cp .env.example .env'
	docker compose exec -T php composer install
	docker compose exec -T php php artisan key:generate
	docker compose exec -T php php artisan storage:link
	docker compose exec -T php php artisan migrate --force
	docker compose exec -T php php artisan db:seed --force
	docker compose exec -T php php artisan optimize:clear

down:
	docker compose down

remove:
	docker compose down -v
