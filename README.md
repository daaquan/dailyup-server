# DailyUp API

Cute and easy-to-use habit tracker API built with **Phalcon**.

## Features

- CRUD endpoints for habits
- JWT based authentication

## Project Structure

- `public/` - Phalcon based API

## Getting Started

### Backend

Requires PHP and Composer. Install dependencies and run migrations:

```bash
composer install
php -r "include 'database/migrations/001_create_topics.php'; (new CreateTopics())->up(new Phalcon\\Db\\Adapter\\Pdo\\Sqlite(['dbname'=>':memory:']));"
php -S localhost:8000 -t public
```

### Production

To build and run the containers for production use:

```bash
make docker-prod
```
\nA cron task runs `php app/cli.php crawl topics` every 10 minutes.
