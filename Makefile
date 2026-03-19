.PHONY: up down build restart shell logs migrate seed fresh install

# Start all services
up:
	docker-compose up -d

# Stop all services
down:
	docker-compose down

# Build/rebuild containers
build:
	docker-compose build --no-cache

# Restart services
restart:
	docker-compose restart

# Open shell in app container
shell:
	docker-compose exec app sh

# View logs
logs:
	docker-compose logs -f

# Run migrations
migrate:
	docker-compose exec app php artisan migrate

# Seed database
seed:
	docker-compose exec app php artisan db:seed

# Fresh migration with seed
fresh:
	docker-compose exec app php artisan migrate:fresh --seed

# Install dependencies
install:
	docker-compose exec app composer install
	docker-compose exec app npm install
	docker-compose exec app npm run build

# Clear all caches
clear:
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

# Run queue worker
queue:
	docker-compose exec app php artisan queue:work

# Setup development environment
setup:
	cp .env.example .env
	docker-compose up -d
	sleep 5
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate --seed
	docker-compose exec app npm install
	docker-compose exec app npm run build
	@echo "Setup complete! Access: http://localhost"
