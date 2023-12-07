#!/bin/bash

if [ ! -f ".env.prod.local" ]; then
	echo -e "\nArquivo de configuração .env.prod.local não encontrador\n"
	exit 9
fi

composer install --no-dev --optimize-autoloader
composer dump-env prod
php bin/console cache:clear --env=PROD

