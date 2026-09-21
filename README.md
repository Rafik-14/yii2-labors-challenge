# Yii2 Labors Challenge

A Yii 2 application for managing labor records and exposing the aggregated work data through a JSON API.

This project is not the stock Yii 2 basic template login/logout demo. It has been customized for a labor-tracking challenge and includes:

- a CRUD interface for labor records at `/labors`
- an API endpoint at `POST /api/works`
- MySQL/MariaDB-backed persistence
- a built-in PHP CLI server static-file guard in `web/index.php`

## Features

- View, create, update, and delete labor records
- Filter records by `need_work`
- Aggregate working minutes by worker and date for the API response
- Local development support via the built-in PHP web server

## Local setup

### 1) Install dependencies

```bash
composer install
```

### 2) Configure the database

```bash
cp .env.example .env
```

Edit `.env` with your local MySQL/MariaDB credentials:

```env
DB_HOST=127.0.0.1
DB_NAME=yii2_labors_db
DB_USER=root
DB_PASSWORD=your_password_here
```

If you do not create `.env`, the app falls back to:

```text
DB_HOST=127.0.0.1
DB_NAME=yii2_labors_db
DB_USER=root
DB_PASSWORD=
```

### 3) Create the database and import the provided schema/data

```bash
mysql -u root -p -e "CREATE DATABASE yii2_labors_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p yii2_labors_db < yii2_labors_db.sql
```

Alternatively, you can create the empty database and run migrations:

```bash
php yii migrate --interactive=0
```

### 4) Start the application

```bash
php -S localhost:8080 -t web web/index.php
```

Open:

```text
http://localhost:8080/labors
```

### 5) Test the API

```bash
curl -X POST http://localhost:8080/api/works
```

## Project structure

```text
config/        Yii app and DB configuration
controllers/   Web controllers and API endpoints
models/        ActiveRecord models
migrations/    Database schema setup
data/          Mock data used for fallback or migration seeding
views/         UI templates
web/           Public entry point and static assets
tests/         Codeception tests
```

## Authentication status

This repository does not include a login/logout system. The README no longer describes a user authentication flow, and there is no login controller or login page in the current project.

## Contradictions and stale content found

The original README was the stock Yii 2 basic template README, which contradicted the actual project in several places:

1. It described a generic Yii app with user login/logout and a contact page.
   - The application currently has no login/logout flow.
   - There is no contact page in the active controllers/views.

2. It recommended `composer create-project yiisoft/yii2-app-basic basic`.
   - This project is already a completed app, not a freshly scaffolded template.

3. It included Docker and generic installation instructions for the template.
   - Those instructions are not specific to this repository and do not reflect the actual project behavior.

4. It implied the default Yii template features were part of the project.
   - The real app is focused on labor records and JSON aggregation.

5. It mentions a contact page and basic auth flow that do not exist in the codebase.
   - `SiteController` only exposes the home page and error action.
   - `models/LoginForm.php` exists as a leftover pattern, but there is no actual login route or UI connected to it.

## Notes

- The `.env` file is intentionally ignored and should never be committed.
- The built-in PHP CLI server route guard in `web/index.php` ensures static assets such as CSS and JS are served directly instead of being routed into Yii.
