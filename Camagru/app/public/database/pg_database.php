<?php
require_once __DIR__ . "/../EnvLoader.php";
require_once __DIR__ . '/pg_database.php';

class PGDatabase
{
    private $pdo;

    public function __construct()
    {
        try {
            $pgdatabase = new PGDatabase();
            $this->pdo = $pgdatabase->getConnection();
            if (!$this->pdo) {
                throw new Exception("Failed to connect to database");
            }
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
    public function register($uuid, $national_id_nr = "", $nationality = "", $date_of_birth = "", $street = "", $city = "", $state = "", $zip_code = "", $country = "", $phone_number = "", $profile_picture = "")
    {
        $stmt = $this->pdo->prepare("
        insert into profiles (uuid,national_id_nr,nationality,date_of_birth,street,city,state,zip_code,country,phone_number,profile_picture)
        values (:uuid,:national_id_nr,:nationality,:date_of_birth,:street,:city,:state,:zip_code,:country,:phone_number,:profile_picture)");

        $stmt->bindParam(':uuid', $uuid);
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
