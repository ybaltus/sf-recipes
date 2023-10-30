#!/usr/bin/env bash

php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixture:load --no-interaction
npm run dev
exec apache2-foreground