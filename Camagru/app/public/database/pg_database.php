<?php
require_once __DIR__ . "/../EnvLoader.php";
require_once __DIR__ . '/pg_database.php';

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
      $dsn = EnvLoader::get('PG_DSN', 'pgsql:host=postgre;port=5432;dbname=camagru_db');
      if (!$dsn || !$username || !$password) {
        throw new Exception("Database connection parameters are not set correctly.");
      }
      $this->pdo = new PDO($dsn, $username, $password);
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
