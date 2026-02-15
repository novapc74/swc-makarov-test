vendor:
	docker run --rm \
        -u "$$(id -u):$$(id -g)" \
        -v "$$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install

key-gen:
	./vendor/bin/sail artisan key:generate

up:
	./vendor/bin/sail up -d

down:
	./vendor/bin/sail down

migrate:
	./vendor/bin/sail artisan migrate

seed:
	./vendor/bin/sail artisan db:seed

test:
	./vendor/bin/sail artisan test
