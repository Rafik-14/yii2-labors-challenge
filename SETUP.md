# Setup Instructions

**Project**: Yii2 Labors Challenge - Professional Submission  
**Author**: Rafik-14 (https://github.com/Rafik-14)

## Database Configuration

This project uses environment variables for database configuration to keep credentials secure and flexible.

### Quick Setup

1. **Copy the example environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Edit `.env` with your MySQL credentials:**
   ```env
   DB_HOST=127.0.0.1
   DB_NAME=yii2_labors_db
   DB_USER=root
   DB_PASSWORD=your_password_here
   ```

3. **Import the database:**
   ```bash
   mysql -u root -p yii2_labors_db < yii2_labors_db.sql
   ```

### Default Configuration

If you don't create a `.env` file, the application will use these default values:
- Host: `127.0.0.1`
- Database: `yii2_labors_db`
- Username: `root`
- Password: (empty)

These defaults work with standard XAMPP/WAMP installations.

## Running the Application

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Start the development server:**
   ```bash
   php -S localhost:8080 -t web web/index.php
   ```

3. **Access the application:**
   - Web interface: `http://localhost:8080/labors`
   - API endpoint: `http://localhost:8080/api/works` (POST)

## Running Tests

```bash
vendor/bin/codecept run Unit
```

## Security Note

The `.env` file contains sensitive credentials and is excluded from version control via `.gitignore`. Never commit your actual `.env` file to a public repository.
