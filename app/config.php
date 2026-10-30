<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;

/**
 * Application configuration.
 * Reads values from environment variables (.env) with sensible defaults.
 */
class Config
{
    private array $values = [];

    public function __construct(array $env = [])
    {
        $this->values = [
            'app_name'     => $_ENV['APP_NAME'] ?? 'OsunBiz',
            'app_env'      => $_ENV['APP_ENV'] ?? 'local',
            'app_key'      => $_ENV['APP_KEY'] ?? '',
            'app_debug'    => filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
            'app_url'      => $_ENV['APP_URL'] ?? 'http://127.0.0.1:8000',
            'bcrypt_rounds'=> (int) ($_ENV['BCRYPT_ROUNDS'] ?? 12),

            'log_channel'  => $_ENV['LOG_CHANNEL'] ?? 'stack',
            'log_level'    => $_ENV['LOG_LEVEL'] ?? 'debug',

            'db_connection'=> $_ENV['DB_CONNECTION'] ?? 'pgsql',
            'db_host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'db_port'      => $_ENV['DB_PORT'] ?? '5432',
            'db_database'  => $_ENV['DB_DATABASE'] ?? 'osunbiz',
            'db_username'  => $_ENV['DB_USERNAME'] ?? 'osunbiz',
            'db_password'  => $_ENV['DB_PASSWORD'] ?? '',
            'database_url' => $_ENV['DATABASE_URL'] ?? '',

            'session_driver'=> $_ENV['SESSION_DRIVER'] ?? 'database',
            'session_lifetime'=> (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
            'cache_store'   => $_ENV['CACHE_STORE'] ?? 'database',

            'mail_mailer'   => $_ENV['MAIL_MAILER'] ?? 'smtp',
            'mail_host'     => $_ENV['MAIL_HOST'] ?? '127.0.0.1',
            'mail_port'     => (int) ($_ENV['MAIL_PORT'] ?? 1025),
            'mail_username' => $_ENV['MAIL_USERNAME'] ?? '',
            'mail_password' => $_ENV['MAIL_PASSWORD'] ?? '',
            'mail_encryption'=> $_ENV['MAIL_ENCRYPTION'] ?? '',
            'mail_from_address'=> $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@example.com',
            'mail_from_name'=> $_ENV['MAIL_FROM_NAME'] ?? 'OsunBiz',
        ];

        // Merge any explicitly passed env overrides
        foreach ($env as $key => $value) {
            $this->values[$key] = $value;
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }
}