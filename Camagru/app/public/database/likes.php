<?php
require_once __DIR__ . '/../database/pg_database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Likes
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
            echo "Error connecting to the database: " . $e->getMessage();
        }
    }
    
    public function getUserLikeStatus($userUuid, $documentUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
              SELECT like_status FROM likes
              WHERE user_uuid = :userUuid AND document_uuid = :documentUuid
            ");
            $stmt->execute([':userUuid' => $userUuid, ':documentUuid' => $documentUuid]);
            $result = $stmt->fetch();
            
            if ($result) {
                return (bool)$result['like_status']; // Asegurar que retorna boolean
            } else {
                return null; // Usuario no ha dado like ni dislike
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function hasUserLiked($userUuid, $documentUuid)
    {
        $status = $this->getUserLikeStatus($userUuid, $documentUuid);
        return $status === true;
    }

    public function hasUserDisliked($userUuid, $documentUuid)
    {
        $status = $this->getUserLikeStatus($userUuid, $documentUuid);
        return $status === false;
    }

    public function hasUserReacted($userUuid, $documentUuid)
    {
        $status = $this->getUserLikeStatus($userUuid, $documentUuid);
        return $status !== null;
    }

    public function getLikeCount($documentUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
              SELECT COUNT(*) as like_count FROM likes
              WHERE document_uuid = :documentUuid AND like_status = true
            ");
            $stmt->execute([':documentUuid' => $documentUuid]);
            $result = $stmt->fetch();
            return (int)$result['like_count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getDislikeCount($documentUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
              SELECT COUNT(*) as dislike_count FROM likes
              WHERE document_uuid = :documentUuid AND like_status = false
            ");
            $stmt->execute([':documentUuid' => $documentUuid]);
            $result = $stmt->fetch();
            return (int)$result['dislike_count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function deleteLikePost($userUuid, $documentUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM likes WHERE user_uuid = :userUuid AND document_uuid = :documentUuid
            ");
            $stmt->execute([':userUuid' => $userUuid, ':documentUuid' => $documentUuid]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function addLike($userUuid, $documentUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO likes (user_uuid, document_uuid, like_status) 
                VALUES (:userUuid, :documentUuid, true)
                ON CONFLICT (user_uuid, document_uuid) 
                DO UPDATE SET like_status = true
            ");
            $stmt->execute([':userUuid' => $userUuid, ':documentUuid' => $documentUuid]);
            return ['success' => true, 'message' => 'Like added successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error adding like: ' . $e->getMessage()];
        }
    }

    public function addDislike($userUuid, $documentUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO likes (user_uuid, document_uuid, like_status) 
                VALUES (:userUuid, :documentUuid, false)
                ON CONFLICT (user_uuid, document_uuid) 
                DO UPDATE SET like_status = false
            ");
            $stmt->execute([':userUuid' => $userUuid, ':documentUuid' => $documentUuid]);
            return ['success' => true, 'message' => 'Dislike added successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error adding dislike: ' . $e->getMessage()];
        }
    }

    public function toggleLike($userUuid, $documentUuid)
    {
        try {
            $currentStatus = $this->getUserLikeStatus($userUuid, $documentUuid);

            if ($currentStatus === true) {
                // Ya tiene like, lo removemos
                return $this->deleteLikePost($userUuid, $documentUuid) ? 
                       ['success' => true, 'message' => 'Like removed successfully'] :
                       ['success' => false, 'message' => 'Error removing like'];
            } else {
                // No tiene like o tiene dislike, añadimos like
                return $this->addLike($userUuid, $documentUuid);
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error toggling like: ' . $e->getMessage()];
        }
    }

    public function toggleDislike($userUuid, $documentUuid)
    {
        try {
            $currentStatus = $this->getUserLikeStatus($userUuid, $documentUuid);
            
            if ($currentStatus === false) {
                // Ya tiene dislike, lo removemos
                return $this->deleteLikePost($userUuid, $documentUuid) ? 
                       ['success' => true, 'message' => 'Dislike removed successfully'] :
                       ['success' => false, 'message' => 'Error removing dislike'];
            } else {
                // No tiene dislike o tiene like, añadimos dislike
                return $this->addDislike($userUuid, $documentUuid);
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error toggling dislike: ' . $e->getMessage()];
        }
    }
    public function deleteLikesByDocUuid($docUuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM likes 
                WHERE document_uuid = :docUuid
            ");
            $stmt->bindParam(':docUuid', $docUuid);
            $stmt->execute();
            return ['success' => true, 'message' => 'Likes deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error deleting likes: ' . $e->getMessage()];
        }
    }
}