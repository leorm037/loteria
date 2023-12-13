#!/bin/bash

if [ ! -f ".env.prod.local" ]; then
	echo -e "\nArquivo de configuração .env.prod.local não encontrador\n"
	exit
fi
composer update
composer code:fix
composer install --no-dev --optimize-autoloader
composer dump-env prod
php bin/console cache:clear --env=DEV
php bin/console cache:clear --env=TEST
php bin/console cache:clear --env=PROD
php bin/console cache:warmup
