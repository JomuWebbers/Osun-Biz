<?php

declare(strict_types=1);

namespace App;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Config.php';
require __DIR__ . '/Database.php';

/**
 * Application bootstrap.
 * Loads environment variables, configuration, database connection, and starts the session.
 */
class Bootstrap
{
    public Config $config;
    public Database $database;

    public function __construct()
    {
        $this->loadEnv();
        $this->config = new Config($_ENV);
        $this->database = new Database($this->config);
        $this->startSession();
    }

    private function loadEnv(): void
    {
        $envFile = __DIR__ . '/../../.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, '"\'');
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $lifetime = (int) $this->config->get('session_lifetime', 120);
            ini_set('session.cookie_lifetime', (string) ($lifetime * 60));
            ini_set('session.gc_maxlifetime', (string) ($lifetime * 60));
            session_start();
        }
    }

    public function db(): PDO
    {
        return $this->database->connection();
    }
}


