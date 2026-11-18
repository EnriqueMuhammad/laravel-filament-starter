.DEFAULT_GOAL := help

COMPOSE := docker compose

.PHONY: help setup up down restart logs shell test pint stan migrate seed fresh assets

help: ## List common commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

setup: ## Copy .env if needed, boot the stack, migrate, and seed
	@test -f .env || cp .env.example .env
	$(COMPOSE) up -d --build
	$(COMPOSE) exec -T app composer install --no-interaction --prefer-dist
	$(COMPOSE) exec -T app php artisan key:generate --force
	$(COMPOSE) exec -T app php artisan migrate --seed --force
	$(COMPOSE) exec -T app php artisan storage:link
	$(COMPOSE) exec -T app php artisan filament:assets
	@echo "Admin panel: http://localhost:8080/admin"

up: ## Build and start the local stack
	$(COMPOSE) up -d --build

down: ## Stop the stack and keep database data
	$(COMPOSE) down

restart: ## Restart containers
	$(COMPOSE) restart

logs: ## Follow container logs
	$(COMPOSE) logs -f

shell: ## Open a shell in the PHP container
	$(COMPOSE) exec app bash

test: ## Run Pest inside the PHP container
	$(COMPOSE) exec -T app php artisan test

pint: ## Check PHP formatting inside the container
	$(COMPOSE) exec -T app vendor/bin/pint --test

stan: ## Run Larastan inside the container
	$(COMPOSE) exec -T app vendor/bin/phpstan analyse --memory-limit=1G

migrate: ## Run migrations
	$(COMPOSE) exec -T app php artisan migrate --force

seed: ## Seed demo roles, users, and inventory
	$(COMPOSE) exec -T app php artisan db:seed --force

fresh: ## Drop the local database, migrate, and seed again
	$(COMPOSE) exec -T app php artisan migrate:fresh --seed --force

assets: ## Publish Filament CSS and JavaScript into public/
	$(COMPOSE) exec -T app php artisan filament:assets
