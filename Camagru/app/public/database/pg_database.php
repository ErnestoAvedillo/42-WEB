<?php
require_once __DIR__ . "/../EnvLoader.php";

class PGDatabase
{
    private $pdo;

    public function __construct()
    {
        try {
            // Get database configuration from environment variables
            $host = EnvLoader::get('PG_HOST', 'postgre');
            $port = EnvLoader::get('PG_PORT', '5432');
            $database = EnvLoader::get('PG_DATABASE', 'camagru_db');
            $username = EnvLoader::get('PG_USER', 'camagru');
            $password = EnvLoader::get('PG_PASSWORD', 'camagru');
            $dsn = EnvLoader::get('PG_DSN', "pgsql:host=$host;port=$port;dbname=$database");
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                //PDO::PGSQL_ATTR_BYTEA_OUTPUT => PDO::BYTEA_OUTPUT_BASE64,
            ];
            if (!$dsn || !$username || !$password) {
                throw new Exception("Database connection parameters are not set correctly.");
            }
            $this->pdo = new PDO($dsn, $username, $password, $options);
            if (!$this->pdo) {
                throw new Exception("Failed to connect to database");
            }
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
