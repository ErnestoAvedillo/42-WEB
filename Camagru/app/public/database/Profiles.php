<?php
require_once __DIR__ . '/../EnvLoader.php';
require_once __DIR__ . '/pg_database.php';

class Profiles
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
     * Get user profile by user ID
     */
    public function getUserProfile($user_uuid_Id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT *
                FROM profiles 
                WHERE user_uuid = :user_uuid
            ");

            $stmt->execute([':user_uuid' => $user_uuid_Id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update user profile
     */
    public function updateUserProfile($user_uuid_Id, $data)
    {
        try {
            $allowedFields = ['first_name', 'last_name', 'email'];
            $updates = [];
            $params = [':user_uuid' => $user_uuid_Id];

            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $updates[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }

            if (empty($updates)) {
                return false;
            }

            $sql = "UPDATE users SET " . implode(', ', $updates) . ", updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function getProfileData($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT *
                FROM profiles 
                WHERE user_uuid = :user_uuid
            ");

            $stmt->execute([':user_uuid' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
}
