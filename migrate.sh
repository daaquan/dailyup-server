#!/bin/bash

# Run database migrations
docker-compose exec app php artisan migrate

echo "Database migrations completed."