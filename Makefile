SAIL = ./vendor/bin/sail
DOCKER_PHP = docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(shell pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest

.PHONY: install up down restart test seed check-overdue worker shell

install:
	@test -f .env || cp .env.example .env
	$(DOCKER_PHP) composer install
	$(SAIL) up -d
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate --seed

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
