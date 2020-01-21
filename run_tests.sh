source db_setup.sh
docker exec event-manager-app_php_1 vendor/bin/phpunit --colors=always --do-not-cache-result tests/
