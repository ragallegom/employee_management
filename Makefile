PROJECT_NAME=employee_management_app
APP_DIR=app
JWT_DIR=$(APP_DIR)/config/jwt
PRIVATE_KEY=$(JWT_DIR)/private.pem
PUBLIC_KEY=$(JWT_DIR)/public.pem
JWT_PASSPHRASE?=jwtsecret
DB_CONTAINER=db
PHP_CONTAINER=$(PROJECT_NAME)

.PHONY: jwt-keys jwt-check jwt-clean docker-up docker-down docker-restart db-reset db-migrate db-fixtures

jwt-keys:
	@mkdir -p $(JWT_DIR)
	@openssl genrsa -aes256 -passout pass:$(JWT_PASSPHRASE) -out $(PRIVATE_KEY) 4096
	@openssl rsa -pubout -in $(PRIVATE_KEY) -passin pass:$(JWT_PASSPHRASE) -out $(PUBLIC_KEY)
	@echo "JWT_PASSPHRASE=$(JWT_PASSPHRASE)"

jwt-check:
	@if [ ! -f $(PRIVATE_KEY) ] || [ ! -f $(PUBLIC_KEY) ]; then \
		echo "JWT keys not found. Run 'make jwt-keys' to generate them."; \
		exit 1; \

	fi
	@echo "JWT keys are present."

jwt-clean:
	@rm -f $(PRIVATE_KEY) $(PUBLIC_KEY)
	@echo "JWT keys removed."

docker-up:
	docker-compose up -d --build

docker-down:
	docker-compose down

docker-restart: docker-down docker-up

db-reset:
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:database:drop --force --if-exists
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:database:create
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

db-migrate:
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

db-fixtures:
	docker exec -it $(PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction
