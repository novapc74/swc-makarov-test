SAIL = ./vendor/bin/sail
DOCKER_PHP = docker run --rm -u "$$(id -u):$$(id -g)" -v "`pwd`:/var/www/html" -w /var/www/html laravelsail/php82-composer:latest

.PHONY: install up down restart test seed check-overdue

install:
	@test -f .env || cp .env.example .env
	$(DOCKER_PHP) composer install
	# Исправляем права (потребуется пароль sudo)
	sudo chown -R $${USER}:33 storage bootstrap/cache
	sudo chmod -R 775 storage bootstrap/cache
	$(SAIL) up -d
	@echo "Waiting for Database..."
	@sleep 10
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate:fresh --seed
	@echo "✅ Done!"

up:
	$(SAIL) up -d

down:
	$(SAIL) down

restart:
	$(SAIL) stop
	$(SAIL) up -d

test:
	$(SAIL) artisan test

check-overdue:
	$(SAIL) artisan app:check-overdue-tasks

shell:
	$(SAIL) shell
