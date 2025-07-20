<?php
class Database
{
    private $host = 'mongodb'; // MongoDB container name
    private $port = '27017';
    private $username = 'root';
    private $password = 'root';
    private $db_name = 'camagru';
    private $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            // MongoDB connection without authentication (matches your docker-compose.yml)
            $connection_string = "mongodb://{$this->host}:{$this->port}";

            echo "<!-- Attempting MongoDB connection: $connection_string -->";
            $this->conn = new MongoDB\Driver\Manager($connection_string);

            // Test the connection with a simple operation
            $command = ['ping' => 1];
            $query = new MongoDB\Driver\Query([], ['limit' => 1]);

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
