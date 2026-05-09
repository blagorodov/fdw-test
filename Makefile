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
	docker compose exec -T php php artisan config:cache
	docker compose exec -T php php artisan route:cache
	docker compose exec -T php php artisan migrate --force
	docker compose exec -T php php artisan db:seed --force
	docker compose exec -T php sh -lc 'chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwx storage bootstrap/cache'
	docker compose exec -T php php artisan optimize:clear
	docker compose exec -T php composer install --no-dev --optimize-autoloader

down:
	docker compose down

remove:
	docker compose down -v
