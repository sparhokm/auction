init: docker-down-clear \
	api-clear frontend-clear cucumber-clear\
	docker-pull docker-build docker-up \
	api-init frontend-init cucumber-init
up: docker-up
down: docker-down
restart: down up
check: lint analyze validate-schema cucumber-check test test-e2e
lint: api-lint frontend-lint cucumber-lint
analyze: api-analyze
validate-schema: api-validate-schema
test: api-test api-fixtures frontend-test
test-unit: api-test-unit
test-functional: api-test-functional api-fixtures
test-smoke: api-fixtures cucumber-clear cucumber-smoke
test-e2e:
	make api-fixtures
	make cucumber-clear
	- make cucumber-e2e
	make cucumber-report

update-deps: api-composer-update frontend-yarn-upgrade cucumber-yarn-upgrade restart


docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --pull

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/*'

api-init: api-composer-install api-permissions api-wait-db api-migrations api-fixtures

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 var/cache var/log var/test

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-composer-update:
	docker-compose run --rm api-php-cli composer update

api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-migrations:
	docker-compose run --rm api-php-cli composer app migrations:migrate -- --no-interaction

api-backup:
	docker-compose run --rm api-postgres-backup

api-fixtures:
	docker-compose run --rm api-php-cli composer app fixtures:load

api-check: api-validate-schema api-lint api-analyze api-test

api-validate-schema:
	docker-compose run --rm api-php-cli composer app orm:validate-schema -- -v

api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli composer rector -- --dry-run
	docker-compose run --rm api-php-cli composer php-cs-fixer fix -- --dry-run --diff

api-lint-fix:
	docker-compose run --rm api-php-cli composer rector
	docker-compose run --rm api-php-cli composer php-cs-fixer fix

api-analyze:
	docker-compose run --rm api-php-cli composer psalm -- --no-diff

api-analyze-diff:
	docker-compose run --rm api-php-cli composer psalm

api-test:
	docker-compose run --rm api-php-cli composer test

api-test-coverage:
	docker-compose run --rm api-php-cli composer test-coverage

api-test-unit:
	docker-compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-unit-coverage:
	docker-compose run --rm -e XDEBUG_MODE=coverage api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker-compose run --rm api-php-cli composer test -- --testsuite=functional

api-test-functional-coverage:
	docker-compose run --rm -e XDEBUG_MODE=coverage api-php-cli composer test-coverage -- --testsuite=functional

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready build'

frontend-init: frontend-yarn-install frontend-ready

frontend-yarn-install:
	docker-compose run --rm frontend-node-cli yarn install

frontend-yarn-upgrade:
	docker-compose run --rm frontend-node-cli yarn upgrade

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

frontend-check: frontend-lint frontend-test frontend-ts-check

frontend-ts-check:
	docker-compose run --rm frontend-node-cli yarn ts-check

frontend-lint:
	docker-compose run --rm frontend-node-cli yarn eslint
	docker-compose run --rm frontend-node-cli yarn stylelint

frontend-lint-fix:
	docker-compose run --rm frontend-node-cli yarn eslint-fix
	docker-compose run --rm frontend-node-cli yarn stylelint-fix
	docker-compose run --rm frontend-node-cli yarn prettier

frontend-test:
	docker-compose run --rm frontend-node-cli yarn test --watchAll=false

frontend-test-watch:
	docker-compose run --rm frontend-node-cli yarn test

cucumber-clear:
	docker run --rm -v ${PWD}/cucumber:/app -w /app alpine sh -c 'rm -rf var/*'

cucumber-init: cucumber-yarn-install

cucumber-check: cucumber-lint cucumber-ts-check

cucumber-yarn-install:
	docker-compose run --rm cucumber-node-cli yarn install

cucumber-yarn-upgrade:
	docker-compose run --rm cucumber-node-cli yarn upgrade

cucumber-ts-check:
	docker-compose run --rm cucumber-node-cli yarn ts-check

cucumber-lint:
	docker-compose run --rm cucumber-node-cli yarn lint

cucumber-lint-fix:
	docker-compose run --rm cucumber-node-cli yarn lint-fix

cucumber-smoke:
	docker-compose run --rm cucumber-node-cli yarn smoke

cucumber-e2e:
	docker-compose run --rm cucumber-node-cli yarn e2e

cucumber-report:
	docker-compose run --rm cucumber-node-cli yarn report

build: build-frontend build-api

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-frontend:${IMAGE_TAG} frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/auction-api-php-cli:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/common/postgres-backup/Dockerfile --tag=${REGISTRY}/auction-api-postgres-backup:${IMAGE_TAG} api/docker/common

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-frontend push-api

push-frontend:
	docker push ${REGISTRY}/auction-frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/auction-api:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-postgres-backup:${IMAGE_TAG}

testing-build: testing-build-testing-api-php-cli testing-build-cucumber

testing-build-testing-api-php-cli:
	docker --log-level=debug build --pull --file=api/docker/testing/php-cli/Dockerfile --tag=${REGISTRY}/auction-testing-api-php-cli:${IMAGE_TAG} api

testing-build-cucumber:
	docker --log-level=debug build --pull --file=cucumber/docker/testing/node/Dockerfile --tag=${REGISTRY}/auction-cucumber-node-cli:${IMAGE_TAG} cucumber

testing-init:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml up -d
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml run --rm api-php-cli wait-for-it api-postgres:5432 -t 60
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml run --rm api-php-cli php bin/app.php migrations:migrate --no-interaction
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml run --rm testing-api-php-cli php bin/app.php fixtures:load --no-interaction
	sleep 15

testing-smoke:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml run --rm cucumber-node-cli yarn smoke

testing-e2e:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml run --rm cucumber-node-cli yarn e2e

testing-down-clear:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml down -v --remove-orphans

try-testing: try-build try-testing-build try-testing-init try-testing-smoke try-testing-e2e try-testing-down-clear

try-testing-build:
	REGISTRY=localhost IMAGE_TAG=0 make testing-build

try-testing-init:
	REGISTRY=localhost IMAGE_TAG=0 make testing-init

try-testing-smoke:
	REGISTRY=localhost IMAGE_TAG=0 make testing-smoke

try-testing-e2e:
	REGISTRY=localhost IMAGE_TAG=0 make testing-e2e

try-testing-down-clear:
	REGISTRY=localhost IMAGE_TAG=0 make testing-down-clear

deploy:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'docker network create --driver=overlay traefik-public || true'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER} && mkdir site_${BUILD_NUMBER}'

	ssh ${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh ${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	scp -P ${PORT} docker-compose-production.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=auction" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_DB_PASSWORD=${API_DB_PASSWORD}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_HOST=${API_MAILER_HOST}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_PORT=${API_MAILER_PORT}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_USER=${API_MAILER_USER}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_PASSWORD=${API_MAILER_PASSWORD}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "API_MAILER_FROM_EMAIL=${API_MAILER_FROM_EMAIL}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "SENTRY_DSN=${SENTRY_DSN}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build -d api-postgres api-php-cli'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run api-php-cli wait-for-it api-postgres:5432 -t 60'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose run api-php-cli php bin/app.php migrations:migrate --no-interaction'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}/secrets'
	scp -o StrictHostKeyChecking=no -P ${PORT} ${API_DB_PASSWORD_FILE} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/api_db_password
	scp -o StrictHostKeyChecking=no -P ${PORT} ${API_MAILER_PASSWORD_FILE} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/api_mailer_password
	scp -o StrictHostKeyChecking=no -P ${PORT} ${SENTRY_DSN_FILE} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/sentry_dsn
	scp -o StrictHostKeyChecking=no -P ${PORT} ${JWT_ENCRYPTION_KEY_FILE} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/jwt_encryption_key
	scp -o StrictHostKeyChecking=no -P ${PORT} ${JWT_PUBLIC_KEY} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/jwt_public_key
	scp -o StrictHostKeyChecking=no -P ${PORT} ${JWT_PRIVATE_KEY} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/jwt_private_key
	scp -o StrictHostKeyChecking=no -P ${PORT} ${BACKUP_AWS_SECRET_ACCESS_KEY_FILE} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/backup_aws_secret_access_key

rollback:
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'

