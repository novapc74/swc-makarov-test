SAIL = ./vendor/bin/sail
DOCKER_PHP = docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(shell pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest

.PHONY: install up down restart test seed check-overdue

install:
	@test -f .env || cp .env.example .env
	$(DOCKER_PHP) composer install
	# Исправляем права на папки сразу после установки пакетов
	sudo chown -R $${USER}:33 storage bootstrap/cache
	sudo chmod -R 775 storage bootstrap/cache
	$(SAIL) up -d
	@echo "Ожидаем готовность PostgreSQL..."
	# Цикл проверки готовности базы (вместо обычного sleep)
	@until $(SAIL) artisan db:monitor > /dev/null 2>&1; do \
		echo "База данных еще не готова... ждем 2 секунды"; \
		sleep 2; \
	done
	@echo "База готова! Завершаем настройку..."
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate:fresh --seed
	@echo "✅ Проект успешно развернут! API доступно на порту 80."

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
