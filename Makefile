PORT ?= 8000

lint:
		composer exec --verbose phpcs -- --standard=PSR12 routes/web.php app/Http

lint-fix:
		composer exec --verbose phpcbf -- --standard=PSR12 routes/web.php app/Http

install:
		composer install

test:
		./vendor/bin/phpunit tests

start:
	php artisan migrate:refresh --seed --force && php artisan serve
