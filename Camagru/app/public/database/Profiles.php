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
    /**
     * Get profile data by user UUID
     */
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
    /**
     * Register a new user profile
     */
    public function registerUserProfile($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO profiles (user_uuid, national_id_nr, nationality, date_of_birth, street, city, state, zip_code, country, phone_number, profile_picture)
                VALUES (:user_uuid, :national_id_nr, :nationality, :date_of_birth, :street, :city, :state, :zip_code, :country, :phone_number, :profile_picture)
            ");

            $stmt->execute($data);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function register($uuid, $national_id_nr = "", $nationality = "", $date_of_birth = "", $street = "", $city = "", $state = "", $zip_code = "", $country = "", $phone_number = "", $profile_picture = "")
    {
        $stmt = $this->pdo->prepare("
        insert into profiles (user_uuid,national_id_nr,nationality,date_of_birth,street,city,state,zip_code,country,phone_number,profile_picture)
        values (:user_uuid,:national_id_nr,:nationality,:date_of_birth,:street,:city,:state,:zip_code,:country,:phone_number,:profile_picture)");

        $stmt->bindParam(':user_uuid', $user_uuid);
        $stmt->bindParam(':national_id_nr', $national_id_nr);
        $stmt->bindParam(':nationality', $nationality);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':zip_code', $zip_code);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':profile_picture', $profile_picture);

        $result = $stmt->execute();

        if ($result) {
            return [
                'success' => true,
                'message' => 'User registered successfully',
                'verification_token' => $verificationToken
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to register user'];
        }
    }
}
