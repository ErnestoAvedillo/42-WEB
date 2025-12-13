<?php
require_once __DIR__ . '/../EnvLoader.php'; // Adjust path since we're in database/ folder
require_once __DIR__ . '/../vendor/autoload.php'; // Ensure MongoDB library is loaded

use MongoDB\Driver\Exception\Exception;
use MongoDB\Driver\Command;
use MongoDB\Client;
use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;
use \Ramsey\Uuid\Uuid;

class DocumentDB
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $db_name;
    private $conn_str;
    private $conn;
    private $collection_name;

    public function __construct($collection_name = null)
    {
        // Load environment variables in constructor
        $this->host = EnvLoader::get('MONGODB_SERVER', 'mongodb');
        $this->port = EnvLoader::get('MONGODB_PORT', '27017');
        $this->username = EnvLoader::get('MONGODB_ADMIN_USERNAME', 'admin');
        $this->password = EnvLoader::get('MONGODB_PASSWORD', 'admin123');
        $this->db_name = EnvLoader::get('MONGODB_DATABASE', 'camagru');
        $this->conn_str = EnvLoader::get('MONGODB_URL', "mongodb://{$this->username}:{$this->password}@{$this->host}:{$this->port}/{$this->db_name}?authSource=admin");
        $this->collection_name = $collection_name ?: EnvLoader::get('MONGODB_COLLECTION', 'uploads');
    }

    public function connect($collection = null)
    {
        $this->conn = null;
        if ($collection) {
            $this->collection_name = $collection;
        }
        try {
            $this->conn = new MongoDB\Driver\Manager($this->conn_str);

            // Test the connection with a simple operation
            $command = new MongoDB\Driver\Command(['ping' => 1]);
            $this->conn->executeCommand('admin', $command);
            //echo "<!-- MongoDB connection successful -->";
            return $this->conn;
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            //echo "<!-- Connection error: " . $e->getMessage() . " -->";
            //error_log("MongoDB Connection Error: " . $e->getMessage());
            return null;
        }
    }

    public function getDatabase()
    {
        return $this->db_name;
    }

    public function uploadFile($file, $user_uuid = null)
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = (new MongoDB\Client($this->conn_str))->selectCollection($this->db_name, $this->collection_name);
        if (!$collection) {
            throw new Exception("Failed to select collection '{$this->collection_name}'.");
        }
        //echo $filename;
        try {
            $myOwnUUID = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $filename = basename($file['name']);
            $fileData = file_get_contents($file['tmp_name']);
            $mimeType = mime_content_type($file['tmp_name']);
            // Store file in MongoDB as a document
            $result = $collection->insertOne([
                '_id' => $myOwnUUID, // Use a custom UUID or MongoDB ObjectId
                'user_uuid' => $user_uuid, // Replace with actual user UUID if needed
                'filename' => $filename,
                'filedata' => new MongoDB\BSON\Binary($fileData, MongoDB\BSON\Binary::TYPE_GENERIC),
                'mimetype' => $mimeType,
                'uploaded_at' => new MongoDB\BSON\UTCDateTime()
            ]);
            $idvalue = $result->getInsertedId();
            return $idvalue; // Return the ID of the inserted document
        } catch (Exception $e) {
            throw new Exception("File upload error: " . $e->getMessage());
        }
    }

    public function insertCombine($imageData, $user_uuid = null)
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = (new MongoDB\Client($this->conn_str))->selectCollection($this->db_name, $this->collection_name);
        if (!$collection) {
            throw new Exception("Failed to select collection '{$this->collection_name}'.");
        }
        try {
            $myOwnUUID = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $filename = $myOwnUUID . '.jpg';
            $mimeType = 'image/jpeg';
            // Store file in MongoDB as a document
            $result = $collection->insertOne([
                '_id' => $myOwnUUID, // Use a custom UUID or MongoDB ObjectId
                'user_uuid' => $user_uuid, // Replace with actual user UUID if needed
                'filename' => $filename,
                'filedata' => new MongoDB\BSON\Binary($imageData, MongoDB\BSON\Binary::TYPE_GENERIC),
                'mimetype' => $mimeType,
                'uploaded_at' => new MongoDB\BSON\UTCDateTime()
            ]);
            $idvalue = $result->getInsertedId();
            return $idvalue; // Return the ID of the inserted document
        } catch (Exception $e) {
            throw new Exception("Combine insert error: " . $e->getMessage());
        }
    }


    public function delete($id)
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = $this->getCollection();
        $result = $collection->deleteOne(['_id' => $id]);
        return $result->getDeletedCount() > 0;
    }

    public function getCollection()
    {
        $this->connect();
        //echo "<!-- Getting collection from MongoDB -->";
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $client = new MongoDB\Client($this->conn_str);
        return $client->selectCollection($this->db_name, $this->collection_name);
    }

    public function setCollection($collection_name)
    {
        $this->collection_name = $collection_name;
    }

    public function getFileById($id)
    {
        $this->connect();
        //echo "<!-- Getting file by ID: " . htmlspecialchars($id) . " -->
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = $this->getCollection();
        //$file = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        $file = $collection->findOne(['_id' => $id]);
        if (!$file) {
            return null; // Return null if no file found
            //throw new Exception("File not found with ID: " . $id);
        }
        return $file;
    }
    public function getUserPhotos($user_uuid)
    {
        $this->connect();
        //echo "<!-- Getting user photos for UUID: " . htmlspecialchars($user_uuid) .
        if (!$this->conn) {
            //echo "<!-- Database connection not established. -->";
            throw new Exception("Database connection not established.");
        }
        $collection = $this->getCollection();
        $file = $collection->find(['user_uuid' => $user_uuid])->toArray();
        //echo "<!-- Retrieved photos for user UUID: " . htmlspecialchars($user_uuid) . " -->";
        foreach ($file as $f) {
            //echo "<!-- Retrieved photo: " . htmlspecialchars($f['filename']) . " -->";
        }
        if (!$file) {
            return []; // Return an empty array if no photos found
        }
        return $file;
    }
    public function getAllFiles()
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = $this->getCollection();
        $files = $collection->find()->toArray();
        return $files;
    }
    public function getPhotoOwner($picture_uuid)
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        error_log("Getting photo owner for picture UUID: " . $picture_uuid);
        $collection = $this->getCollection();
        $file = $collection->findOne(['_id' => $picture_uuid]);
        if (!$file) {
            return null; // Return null if no file found
        }
        return $file['user_uuid'] ?? null;
    }
    public function getFilesSortedByDate($ascending = true, $elements = 5, $firstElement = 0)
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = $this->getCollection();
        $sortOrder = $ascending ? 1 : -1;
        $files = $collection->find([], ['sort' => ['uploaded_at' => $sortOrder]])->toArray();
        return array_slice($files, $firstElement, $elements);
    }
    public function getFilesSortedByUsername($elements = 5, $firstElement = 0)
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = $this->getCollection();
        $sortOrder = 1; // Ascending order
        $files = $collection->find([], ['sort' => ['user_uuid' => $sortOrder]])->toArray();

        return array_slice($files, $firstElement, $elements);
    }
    public function getTotalFilesCount()
    {
        $this->connect();
        if (!$this->conn) {
            throw new Exception("Database connection not established.");
        }
        $collection = $this->getCollection();
        $count = $collection->countDocuments();
        return $count;
    }
}
