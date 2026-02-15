first-init:
	./vendor/bin/sail down
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan migrate

seed:
	./vendor/bin/sail artisan db:seed
