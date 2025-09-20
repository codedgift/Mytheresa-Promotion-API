.PHONY: help build up down restart logs shell test install migrate seed

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*## "} /^[a-zA-Z_-]+:.*## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker containers
	docker-compose build --no-cache

up: ## Start the application
	docker-compose up -d
	@echo "Application is starting..."
	@echo "API will be available at: http://localhost:8080"
	@echo "Swagger documentation: http://localhost:8080/api/doc"

down: ## Stop the application
	docker-compose down

restart: ## Restart the application
	docker-compose restart

logs: ## Show application logs
	docker-compose logs -f

shell: ## Access PHP container shell
	docker-compose exec php bash

install: ## Install dependencies and setup application
	docker-compose exec php composer install --no-interaction --optimize-autoloader
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose exec php php bin/console app:seed-products

test: ## Run tests
	docker-compose exec php php bin/phpunit

migrate: ## Run database migrations
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

seed: ## Seed database with sample products
	docker-compose exec php php bin/console app:seed-products

setup: build up install ## Complete setup (build, start, install dependencies)
	@echo "Setup complete! Visit http://localhost:8080/api/doc for API documentation"