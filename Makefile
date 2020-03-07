DC = docker-compose

down:
	$(DC) down

serve:
	$(DC) run --rm arty discord:serve

ci:
	$(DC) run --entrypoint '' --rm arty composer install

cu:
	$(DC) run --entrypoint '' --rm arty composer update

test:
	$(DC) run --entrypoint '' --rm arty vendor/bin/phpunit
