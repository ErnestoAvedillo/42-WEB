<?php

/**
 * Simple Environment Variables Loader
 * Loads .env file variables into $_ENV and makes them available via getenv()
 */
class EnvLoader
{
    public static function load($envFile = null)
    {
        if ($envFile === null) {
            $envFile = dirname(__DIR__, 2) . '/.env';
        }

        if (!file_exists($envFile)) {
            return false;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                $value = trim($value, '"\'');

                // Set environment variables
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        return true;
    }

    public static function get($key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

// Auto-load when this file is included
EnvLoader::load();
