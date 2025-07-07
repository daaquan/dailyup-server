# DailyUp Web

Cute and easy-to-use habit tracker built with a **Phalcon** API and a **React** frontend.

## Features

- API driven: Phalcon exposes CRUD endpoints for habits
- React UI with soft colors and rounded cards
- Add or remove habits, toggle completion
- Google Fonts `Fredoka` for playful typography
- Localization ready (Japanese and English)

## Project Structure

- `public/` - Phalcon based API
- `frontend/` - React single page app

## Getting Started

### Backend

Requires PHP and Composer. Install dependencies and run migrations:

```bash
composer install
php -r "include 'database/migrations/001_create_topics.php'; (new CreateTopics())->up(new Phalcon\\Db\\Adapter\\Pdo\\Sqlite(['dbname'=>':memory:']));"
php -S localhost:8000 -t public
```

### Frontend

Requires Node.js. Install dependencies and build:

```bash
cd frontend
npm install
npm run dev
```

The frontend expects the API to run on `localhost:8000`.
