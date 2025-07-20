<?php
require_once '/var/www/config/php/database.php';

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

class User
{
    private $manager;
    private $db_name;

    public function __construct($manager, $db_name)
    {
        $this->manager = $manager;
        $this->db_name = $db_name;
    }

    public function create($username, $password, $email)
    {
        // Check if user already exists
        if ($this->userExists($username, $email)) {
            return false;
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user document
        $bulk = new BulkWrite;
        $bulk->insert([
            'username' => $username,
            'password' => $hashed_password,
            'email' => $email,
            'created_at' => new UTCDateTime(),
            'is_active' => true,
            'profile_picture' => null,
            'files' => [] // Array to store file references
        ]);

        try {
            $result = $this->manager->executeBulkWrite($this->db_name . '.users', $bulk);
            return $result->getInsertedCount() > 0;
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function userExists($username, $email)
    {
        $query = new Query([
            '$or' => [
                ['username' => $username],
                ['email' => $email]
            ]
        ]);

        try {
            $cursor = $this->manager->executeQuery($this->db_name . '.users', $query);
            return count($cursor->toArray()) > 0;
        } catch (Exception $e) {
            error_log("Error checking if user exists: " . $e->getMessage());
            return false;
        }
    }

    public function storeFile($userId, $filename, $fileData, $fileType, $fileSize)
    {
        // Store file as a document in a files collection
        $bulk = new BulkWrite;

        try {
            // Store file data as base64 encoded string
            $fileDocument = [
                'filename' => $filename,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'file_data' => base64_encode($fileData),
                'user_id' => $userId,
                'upload_date' => new UTCDateTime()
            ];

            $bulk->insert($fileDocument);
            $result = $this->manager->executeBulkWrite($this->db_name . '.files', $bulk);

            if ($result->getInsertedCount() > 0) {
                // Get the inserted ID
                $insertedIds = $result->getInsertedIds();
                $fileId = $insertedIds[0];

                // Update user document with file reference
                $userBulk = new BulkWrite;
                $userBulk->update(
                    ['_id' => new ObjectId($userId)],
                    ['$push' => ['files' => $fileId]],
                    ['multi' => false, 'upsert' => false]
                );

                $this->manager->executeBulkWrite($this->db_name . '.users', $userBulk);

                return $fileId;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error storing file: " . $e->getMessage());
            return false;
        }
    }

    public function getFilesByUser($userId)
    {
        $query = new Query(['_id' => new ObjectId($userId)]);

        try {
            $cursor = $this->manager->executeQuery($this->db_name . '.users', $query);
            $user = $cursor->toArray()[0] ?? null;

            if ($user && isset($user->files)) {
                return $user->files;
            }

            return [];
        } catch (Exception $e) {
            error_log("Error getting user files: " . $e->getMessage());
            return [];
        }
    }

    public function validatePassword($password)
    {
        // Password must be at least 8 characters long
        if (strlen($password) < 8) {
            return false;
        }

        // Add more validation rules as needed
        return true;
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function validateUsername($username)
    {
        // Username must be 3-20 characters, alphanumeric and underscore only
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    public function validateFileType($fileType)
    {
        $allowedTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];

        return in_array($fileType, $allowedTypes);
    }

    public function validateFileSize($fileSize, $maxSize = 5242880)
    { // 5MB default
        return $fileSize <= $maxSize;
    }
}
