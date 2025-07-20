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
            // MongoDB connection without authentication for development
            $connection_string = "mongodb://{$this->host}:{$this->port}";
            $this->conn = new MongoDB\Driver\Manager($connection_string);
            return $this->conn;
        } catch (Exception $e) {
            echo "Connection error: " . $e->getMessage();
            return null;
        }
    }

    public function getDatabase()
    {
        return $this->db_name;
    }
}
