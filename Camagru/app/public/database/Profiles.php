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
            $allowedFields = [
                'national_id_nr',
                'nationality',
                'date_of_birth',
                'street',
                'city',
                'state',
                'zip_code',
                'country',
                'phone_number',
            ];
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
    public function getProfileData($uuid)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT *
                FROM profiles 
                WHERE user_uuid = :user_uuid
            ");

            $stmt->execute([':user_uuid' => $uuid]);
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
                INSERT INTO profiles (user_uuid,
                national_id_nr,
                nationality,
                date_of_birth,
                street,
                city,
                state,
                zip_code,
                country,
                phone_number
            ) VALUES (
                :user_uuid,
                :national_id_nr,
                :nationality,
                :date_of_birth,
                :street,
                :city,
                :state,
                :zip_code,
                :country,
                :phone_number
            )
        ");
            // Prepare the SQL statement
            // Bind parameters
            $stmt->bindParam(':user_uuid', $data['user_uuid']);
            $stmt->bindParam(':national_id_nr', $data['national_id_nr']);
            $stmt->bindParam(':nationality', $data['nationality']);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $stmt->bindParam(':street', $data['street']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':state', $data['state']);
            $stmt->bindParam(':zip_code', $data['zip_code']);
            $stmt->bindParam(':country', $data['country']);
            $stmt->bindParam(':phone_number', $data['phone_number']);
            // Execute the statement
            $result = $stmt->execute();
            if (!$result) {
                return ['success' => false, 'message' => 'Failed to register user profile'];
            }
            return ['success' => true, 'message' => 'User profile registered successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to register user profile: ' . $e->getMessage()];
        }
    }

    public function register($uuid)
    {
        $stmt = $this->pdo->prepare("
        insert into profiles (user_uuid,national_id_nr,nationality,date_of_birth,street,city,state,zip_code,country,phone_number)
        values (:user_uuid,:national_id_nr,:nationality,:date_of_birth,:street,:city,:state,:zip_code,:country,:phone_number)");
        // For the register operation, we use the same UUID as the user UUID and rest of the fields are set to empty strings or defaults.
        $stmt->bindParam(':user_uuid', $uuid);
        $stmt->bindParam(':national_id_nr', '');
        $stmt->bindParam(':nationality', '');
        $stmt->bindParam(':date_of_birth', '');
        $stmt->bindParam(':street', '');
        $stmt->bindParam(':city', '');
        $stmt->bindParam(':state', '');
        $stmt->bindParam(':zip_code', '');
        $stmt->bindParam(':country', '');
        $stmt->bindParam(':phone_number', '');

        $result = $stmt->execute();

        if ($result) {
            return [
                'success' => true,
                'message' => 'User registered successfully',
                'verification_token' => $result['verification_token']
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to register user'];
        }
    }
}
