SAIL = ./vendor/bin/sail
DOCKER_PHP = docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(shell pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest

.PHONY: install up down restart test seed check-overdue worker

install:
	@test -f .env || cp .env.example .env
	$(DOCKER_PHP) composer install
	# Исправляем права на папки логов и кэша
	sudo chmod -R 777 storage bootstrap/cache
	$(SAIL) up -d
	@echo "Waiting for database (10s)..."
	@sleep 10
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate:fresh --seed
	# Перезапускаем воркер, чтобы он подтянул новые настройки
	$(SAIL) artisan queue:restart

up:
	$(SAIL) up -d
down:
	$(SAIL) down
test:
	$(SAIL) artisan test
worker:
	$(SAIL) artisan queue:work
check-overdue:
	$(SAIL) artisan tasks:check-overdue
shell:
	$(SAIL) shell
