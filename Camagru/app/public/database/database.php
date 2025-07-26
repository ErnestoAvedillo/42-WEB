<?php
require_once '../EnvLoader.php'; // Adjust path since we're in database/ folder

class Database
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $db_name;
    private $conn;

    public function __construct()
    {
        // Load environment variables in constructor
        $this->host = EnvLoader::get('MONGODB_SERVER', 'mongodb');
        $this->port = EnvLoader::get('MONGODB_PORT', '27017');
        $this->username = EnvLoader::get('MONGODB_ADMIN_USERNAME', 'admin');
        $this->password = EnvLoader::get('MONGODB_PASSWORD', 'admin123');
        $this->db_name = EnvLoader::get('MONGODB_DATABASE', 'camagru');
    }

    public function connect()
    {
        $this->conn = null;

        try {
            // MongoDB connection with authentication (matches your docker-compose.yml)
            $connection_string = "mongodb://{$this->username}:{$this->password}@{$this->host}:{$this->port}/{$this->db_name}?authSource=admin";

            echo "<!-- Attempting MongoDB connection: mongodb://{$this->username}:***@{$this->host}:{$this->port}/{$this->db_name} -->";
            $this->conn = new MongoDB\Driver\Manager($connection_string);

            // Test the connection with a simple operation
            $command = new MongoDB\Driver\Command(['ping' => 1]);
            $this->conn->executeCommand('admin', $command);

            echo "<!-- MongoDB connection successful -->";
            return $this->conn;
        } catch (Exception $e) {
            echo "<!-- Connection error: " . $e->getMessage() . " -->";
            error_log("MongoDB Connection Error: " . $e->getMessage());
            return null;
        }
    }

    public function getDatabase()
    {
        return $this->db_name;
    }
}
