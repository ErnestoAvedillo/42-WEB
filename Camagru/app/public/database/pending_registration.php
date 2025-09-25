<?php
require_once __DIR__ . '/../EnvLoader.php';
require_once __DIR__ . '/pg_database.php';

class pendingRegistration
{
    private $pdo;
    private $logfile = '/tmp/camagru.log';
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
    public function createPendingRegistration($username, $email, $password, $first_name, $last_name, $validation_token)
    {
        try {
            if ($this->userExists($username)) {
                $stmt = $this->pdo->prepare("DELETE FROM pending_registrations WHERE username = :username");
                $stmt->execute([':username' => $username]);
            }
            $stmt = $this->pdo->prepare("
                        INSERT INTO pending_registrations (username, email, password, first_name, last_name, validation_token) 
                        VALUES (:username, :email, :password, :first_name, :last_name, :validation_token)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':validation_token' => $validation_token
            ]);
            return true;
        } catch (PDOException $e) {
            file_put_contents($this->logfile, "Error creating pending registration: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
    public function getPendingRegistrationByToken($token)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM pending_registrations WHERE validation_token = :token");
            $stmt->execute([':token' => $token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            file_put_contents($this->logfile, "Error fetching pending registration: " . $e->getMessage() . "\n", FILE_APPEND);
            return null;
        }
    }
    public function deletePendingRegistration($username)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pending_registrations WHERE username = :username");
            $stmt->execute([':username' => $username]);
            return true;
        } catch (PDOException $e) {
            file_put_contents($this->logfile, "Error deleting pending registration: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
    public function confirmRegistration($username, $token)
    {
        try {
            $stmt = $this->pdo->prepare("
                        UPDATE pending_registrations 
                        SET token_validated = TRUE 
                        WHERE username = :username AND validation_token = :token");
            $stmt->execute([
                ':username' => $username,
                ':token' => $token
            ]);
            $registration = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($registration) {
                // Aquí podrías agregar lógica para mover los datos a la tabla de usuarios
                $this->deletePendingRegistration($username);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            file_put_contents($this->logfile, "Error confirming registration: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
    public function userExists($username)
    {
        try {
            $stmt = $this->pdo->prepare("
                        SELECT * FROM pending_registrations 
                        WHERE username = :username");
            $stmt->execute([
                ':username' => $username,
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            file_put_contents($this->logfile, "Error checking user existence: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
    public function emailExists($email)
    {
        try {
            $stmt = $this->pdo->prepare("
                        SELECT * FROM pending_registrations 
                        WHERE email = :email");
            $stmt->execute([
                ':email' => $email,
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            file_put_contents($this->logfile, "Error checking email existence: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
    public function unsernameExists($username)
    {
        try {
            $stmt = $this->pdo->prepare("
                        SELECT * FROM pending_registrations 
                        WHERE username = :username");
            $stmt->execute([
                ':username' => $username,
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC) === false;
        } catch (PDOException $e) {
            file_put_contents($this->logfile, "Error checking username non-existence: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}
