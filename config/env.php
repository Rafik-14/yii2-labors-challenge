<?php

declare(strict_types=1);

/**
 * Loads KEY=VALUE pairs from the project's .env file into the process environment.
 *
 * Real environment variables always win over .env values, so the same code runs
 * unchanged in Docker/CI (env vars) and on a developer machine (.env file).
 * Safe to require more than once.
 */
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim(trim($value), '"\'');
        if (getenv($name) === false) {
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

if (!function_exists('env')) {
    /**
     * Returns an environment variable, or $default when it is not set.
     * (Note: PHP's own getenv() has no default-value parameter.)
     */
    function env(string $name, ?string $default = null): ?string
    {
        $value = getenv($name);

        return $value === false ? $default : $value;
    }
}
