docker exec -ti event-manager-api_php_1  php bin/console doctrine:database:drop --force
docker exec -ti event-manager-api_php_1  php bin/console doctrine:database:create
docker exec -ti event-manager-api_php_1  php bin/console doctrine:schema:validate --skip-sync
docker exec -ti event-manager-api_php_1  php bin/console doctrine:schema:update --force --complete --dump-sql
docker exec -ti event-manager-api_php_1  php bin/console doctrine:fixtures:load --no-interaction
