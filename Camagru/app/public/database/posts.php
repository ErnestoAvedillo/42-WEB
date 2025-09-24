<?php
require_once __DIR__ . '/../database/pg_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Posts
{
    private $pdo;
    public function __construct()
    {
        try {
            // Get database connection from PGDatabase class
            $pgDatabase = new PGDatabase();
            $this->pdo = $pgDatabase->getConnection();

            // Check if connection is successful
            if (!$this->pdo) {
                throw new Exception("Failed to connect to the database.");
            }
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    /**
     * Get posts by document UUID
     */
    public function getPostsByDocUuid($docUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT caption FROM posts
                WHERE document_uuid = :docUuid
            ");
            $stmt->execute([':docUuid' => $docUuid]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Get posts by user UUID
     */
    public function getPostsByUserUuid($userUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT caption FROM posts
                WHERE user_uuid = :userUuid
            ");
            $stmt->execute([':userUuid' => $userUuid]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Get all posts
     */
    public function addPost($userUuid, $docUuid, $caption)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO posts (user_uuid, document_uuid, caption) 
                VALUES (:userUuid, :docUuid, :caption)
            ");
            $stmt->bindParam(':userUuid', $userUuid);
            $stmt->bindParam(':docUuid', $docUuid);
            $stmt->bindParam(':caption', $caption);
            $result = $stmt->execute();

            return true;
        } catch (PDOException $e) {
            $errorInfo = $stmt->errorInfo();
            error_log('Database error in addPost: ' . $e->getMessage() . ' Info: ' . print_r($errorInfo, true));
            return false;
        }
    }
    public function deletePostsByDocUuid($docUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM posts 
                WHERE document_uuid = :docUuid
            ");
            $stmt->bindParam(':docUuid', $docUuid);
            $result = $stmt->execute();

            return true;
        } catch (PDOException $e) {
            $errorInfo = $stmt->errorInfo();
            error_log('Database error in deletePost: ' . $e->getMessage() . ' Info: ' . print_r($errorInfo, true));
            return false;
        }
    }
}
