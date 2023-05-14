up:
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose --env-file=.env.dev up -d --force-recreate --build --remove-orphans
down:
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose --env-file=.env.dev down --volumes --remove-orphans
composer-install:
	docker-compose exec php su --command="composer -n install --prefer-dist" www-data
cs:
	docker-compose exec php su --command="composer cs" www-data
stan:
	docker-compose exec php su --command="composer stan" www-data
tests:
	docker-compose exec php su --command="composer tests" www-data

APP_NAME = $(shell docker ps -q -f name=php)

bash:
	docker exec -it --user www-data $(APP_NAME) bash
