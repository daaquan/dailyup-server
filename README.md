# DailyUp Web

Cute and easy-to-use habit tracker built with a **Laravel (Lumen)** API and a **React** frontend.

## Features

- API driven: Laravel exposes CRUD endpoints for habits
- React UI with soft colors and rounded cards
- Add or remove habits, toggle completion
- Google Fonts `Fredoka` for playful typography
- Localization ready (Japanese and English)

## Project Structure

- `backend/` - Lumen based API
- `frontend/` - React single page app

## Getting Started

### Backend

Requires PHP and Composer. Install dependencies and run migrations:

```bash
cd backend
composer install
php artisan migrate
php -S localhost:8000 -t public
```

### Frontend

Requires Node.js. Install dependencies and build:

```bash
cd frontend
npm install
npm run dev
```

The frontend expects the API to run on `localhost:8000`. Open `frontend/public/index.html` in a browser or use a simple static server with `npm start`.
