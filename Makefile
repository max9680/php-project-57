lint:
		composer exec --verbose phpcs -- --standard=PSR12 routes/web.php

lint-fix:
		composer exec --verbose phpcbf -- --standard=PSR12 routes/web.php

install:
		composer install