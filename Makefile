PORT ?= 8000

lint:
		composer exec --verbose phpcs -- --standard=PSR12 routes/web.php

lint-fix:
		composer exec --verbose phpcbf -- --standard=PSR12 routes/web.php

install:
		composer install

test:
		./vendor/bin/phpunit tests

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public
