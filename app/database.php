<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

/**
 * Database connection manager.
 * Creates a single PDO instance for PostgreSQL using configuration values.
 */
class Database
{
    private ?PDO $pdo = null;

    public function __construct(private Config $config)
    {
    }

    public function connection(): PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $host = $this->config->get('db_host', '127.0.0.1');
        $port = $this->config->get('db_port', '5432');
        $dbname = $this->config->get('db_database', 'osunbiz');
        $username = $this->config->get('db_username', 'osunbiz');
        $password = $this->config->get('db_password', '');

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: " . $e->getMessage(), 0, $e);
        }

        return $this->pdo;
    }

    public function disconnect(): void
    {
        $this->pdo = null;
    }
}