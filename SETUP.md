# Setup Instructions

**Project**: Yii2 Labors Challenge - Professional Submission  
**Author**: Rafik-14 (https://github.com/Rafik-14)

See [README.md](README.md) for the full documentation. Quick version:

## Environment

1. **Copy the example environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Edit `.env`** with your MySQL credentials. The example file already enables development mode
   (`YII_DEBUG=true`, `YII_ENV=dev`), which works with standard XAMPP/WAMP installations:
   ```env
   DB_HOST=127.0.0.1
   DB_NAME=yii2_labors_db
   DB_USER=root
   DB_PASSWORD=your_password_here
   ```

Without a `.env` file the application runs in production mode and requires `COOKIE_VALIDATION_KEY`
to be set in the environment.

## Database

```bash
mysql -u root -p -e "CREATE DATABASE yii2_labors_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php yii migrate --interactive=0
```

## Running the Application

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Start the development server:**
   ```bash
   php yii serve --port=8080
   ```

3. **Access the application:**
   - Web interface: `http://localhost:8080/labors`
   - API endpoint: `http://localhost:8080/api/works` (POST)

## Running Tests

Tests use a separate database (`TEST_DB_NAME`, default `yii2_labors_test`):

```bash
mysql -u root -p -e "CREATE DATABASE yii2_labors_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php tests/Support/bin/yii migrate --interactive=0
vendor/bin/codecept build
vendor/bin/codecept run Unit
```

## Security Note

The `.env` file contains sensitive credentials and is excluded from version control via `.gitignore`. Never commit your actual `.env` file to a public repository.
