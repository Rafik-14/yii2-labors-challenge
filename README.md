# Yii2 Labors Challenge

A Yii 2 application for managing labor records and exposing the aggregated work data through a JSON API.

This project is not the stock Yii 2 basic template login/logout demo. It has been customized for a labor-tracking challenge and includes:

- a CRUD interface for labor records at `/labors` (Hungarian UI, Kartik DatePicker and Checkbox-X widgets)
- an API endpoint at `POST /api/works`
- MySQL/MariaDB-backed persistence managed by migrations
- a built-in PHP CLI server static-file guard in `web/index.php`

## Features

- View, create, update, and delete labor records
- Filter every grid column (name, e-mail, IP, need-work, minutes, date), sort, and paginate (20 per page)
- Server-side validation: trimmed names, valid e-mail and IP, 0–1440 working minutes, strict dates
- Dates are entered and shown in the English format required by the spec (`23-Feb-1982`) and stored as `Y-m-d H:i:s`;
  editing only the date of an existing shift keeps its stored start time
- Aggregate working minutes by worker and date for the API response
- Hungarian (`hu-HU`) translations for all labels, buttons, messages and pages

## Local setup

### 1) Install dependencies

```bash
composer install
```

### 2) Configure the environment

```bash
cp .env.example .env
```

`.env.example` documents every variable. The important ones:

| Variable | Default | Purpose |
|---|---|---|
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | `127.0.0.1`, `yii2_labors_db`, `root`, *(empty)* | Application database |
| `TEST_DB_NAME` | `yii2_labors_test` | Separate database used by the test suite |
| `YII_DEBUG`, `YII_ENV` | `false`, `prod` | Set to `true` / `dev` locally for the debug toolbar and Gii |
| `COOKIE_VALIDATION_KEY` | *(none)* | Required unless `YII_ENV=dev`. Generate: `php -r "echo bin2hex(random_bytes(32));"` |
| `API_TOKEN` | *(empty)* | When set, `POST /api/works` requires `Authorization: Bearer <token>` |
| `API_CORS_ORIGINS` | `*` | Comma-separated origins allowed to call the API from a browser |

Real environment variables (Docker, CI, hosting panel) override values from `.env`.
Without a `.env` file the application starts in **production mode**, so `COOKIE_VALIDATION_KEY` must then be set.

### 3) Create the database

```bash
mysql -u root -p -e "CREATE DATABASE yii2_labors_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php yii migrate --interactive=0
```

The migrations create the table and seed the 1,000 mock records from `data/mock-data.json`.
Alternatively import `yii2_labors_db.sql`, then still run `php yii migrate --interactive=0` to apply the schema
updates added after the dump was taken.

### 4) Start the application

```bash
php yii serve --port=8080
```

Open `http://localhost:8080/labors`.

### 5) Test the API

```bash
curl -X POST http://localhost:8080/api/works
```

## API: `POST /api/works`

Returns every shift with `need_work = true`, grouped by calendar day (`YYYY-MM-DD`, chronological), then by the
worker's full name, with `working_minutes` summed per worker per day (`null` minutes count as `0`):

```json
{
  "2021-05-19": {
    "Basile Seedhouse": { "name": "Basile Seedhouse", "working_minutes": 301 }
  }
}
```

- Other HTTP methods get `405 Method Not Allowed`; CORS preflight (`OPTIONS`) is supported.
- If `API_TOKEN` is set, a missing or wrong bearer token gets `401 Unauthorized`.
- If the database is unreachable, the error is logged and the response is built from `data/mock-data.json`
  with the same rules and ordering.
- Workers are identified by full name, as the specification requires; two different people with the same
  first and last name are therefore summed together.

## Tests

The suite uses its own database so it never modifies development data.

```bash
mysql -u root -p -e "CREATE DATABASE yii2_labors_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php tests/Support/bin/yii migrate --interactive=0
vendor/bin/codecept build
vendor/bin/codecept run Unit
```

Unit tests cover the model rules and date conversion, the search model, the API aggregation rules
(multi-shift sums, null minutes, excluded rows, invalid dates, midnight boundaries), the database fallback,
the 405/401 responses, and the flash-alert widget. Database tests run inside a transaction that is rolled back.

The acceptance test (`tests/Acceptance/HomeCest.php`) needs a running server:

```bash
vendor/bin/codecept run Acceptance -o "modules: config: PhpBrowser: url: http://localhost:8080"
```

## Project structure

```text
config/        Yii app, DB and environment configuration (config/env.php loads .env)
controllers/   Web controllers and API endpoints
models/        ActiveRecord model, search model and the API report builder
migrations/    Database schema setup and seeding
data/          Mock data used for seeding and as the API fallback (not web-accessible)
views/         UI templates
web/           Public entry point and static assets (the only directory that should be web-served)
tests/         Codeception tests
```

## Authentication status

This repository does not include a login/logout system, and the CRUD pages are public, as in the challenge
specification. `models/User.php` is an intentional stub that never resolves an identity. Before exposing the
application beyond a trusted network, put it behind authentication (for example web-server basic auth or an
`AccessControl` filter once a real identity source exists).

## Notes

- The `.env` file is intentionally ignored and should never be committed.
- The built-in PHP CLI server route guard in `web/index.php` ensures static assets such as CSS and JS are served directly instead of being routed into Yii.
