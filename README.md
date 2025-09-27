
# Nuri Backend Web

**Built with [Laravel](https://laravel.com/) + [FrankenPHP](https://frankenphp.dev/) + Docker**  

This is the backend API for Nuri, leveraging Laravel’s robust framework, FrankenPHP for ultra-fast PHP runtime, and Docker for consistent, isolated development environments.

---

## Quickstart Guide

1. Copy the environment file:
cp .env.example .env

2. Adjust `.env` if necessary (Database, APP_KEY, etc). Example configuration:
APP_KEY=base64:xxxxxx
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=nuri_db
DB_USERNAME=root
DB_PASSWORD=

3. Build and start the Docker containers:
docker compose up -d --build

4. Check if the containers are running:
docker ps

5. Install Composer dependencies (optional, if Dockerfile doesn't install automatically):
docker exec -it nuri-backend-app composer install

6. Generate APP_KEY if not already set:
docker exec -it nuri-backend-app php artisan key:generate

7. Run migrations and seeders:
docker exec -it nuri-backend-app php artisan migrate --seed
