SAIL = ./vendor/bin/sail
DOCKER_PHP = docker run --rm -u $$(id -u):$$(id -g) -v $$(pwd):/var/www/html -w /var/www/html laravelsail/php82-composer:latest

.PHONY: install up down restart test seed check-overdue worker shell

install:
	@test -f .env || cp .env.example .env
	$(DOCKER_PHP) composer install
	sudo chmod -R 777 storage bootstrap/cache
	$(SAIL) up -d
	@echo "Waiting for database..."
	@sleep 10
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate:fresh --seed
	$(SAIL) artisan queue:restart
	@echo "✅ Установка завершена. Проверьте http://localhost"

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
