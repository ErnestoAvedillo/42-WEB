<?php
require_once '../EnvLoader.php';

class User
{
  private $pdo;
  public function __construct()
  {
    try {
      // Get database configuration from environment variables
      $host = EnvLoader::get('PG_HOST', 'postgre');
      $port = EnvLoader::get('PG_PORT', '5432');
      $database = EnvLoader::get('PG_DATABASE', 'camagru_db');
      $username = EnvLoader::get('PG_USER', 'camagru');
      $password = EnvLoader::get('PG_PASSWORD', 'camagru');
      $dsn = EnvLoader::get('PG_DSN', 'pgsql:host=postgre;port=5432;dbname=camagru_db');
      if (!$dsn || !$username || !$password) {
        throw new Exception("Database connection parameters are not set correctly.");
      }
      $this->pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    } catch (PDOException $e) {
      throw new Exception("Database connection failed: " . $e->getMessage());
    }
  }

  /**
   * Register a new user
   */
  public function register($username, $email, $password, $firstName = '', $lastName = '')
  {
    try {
      // Validate input
      if (!$this->validateInput($username, $email, $password)) {
        return ['success' => false, 'message' => 'Invalid input data'];
      }

      // Check if user already exists
      if ($this->userExists($username, $email)) {
        return ['success' => false, 'message' => 'Username or email already exists'];
      }

      // Hash password
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // Generate verification token
      $verificationToken = bin2hex(random_bytes(32));

      // Insert user into database
      $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password, first_name, last_name, verification_token) 
                VALUES (:username, :email, :password, :first_name, :last_name, :verification_token)
            ");

      $result = $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':verification_token' => $verificationToken
      ]);

      if ($result) {
        return [
          'success' => true,
          'message' => 'User registered successfully',
          'verification_token' => $verificationToken
        ];
      } else {
        return ['success' => false, 'message' => 'Failed to register user'];
      }
    } catch (PDOException $e) {
      return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
  }

  /**
   * Login user
   */
  public function login($username, $password)
  {
    try {
      // Find user by username or email
      $stmt = $this->pdo->prepare("
                SELECT id, username, email, password, first_name, last_name, email_verified 
                FROM users 
                WHERE username = :login OR email = :login
            ");

      $stmt->execute([':login' => $username]);
      $user = $stmt->fetch();

      if (!$user) {
        return ['success' => false, 'message' => 'Invalid username or password'];
      }

      // Verify password
      if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid username or password'];
      }

      // Remove password from returned data
      unset($user['password']);

      return [
        'success' => true,
        'message' => 'Login successful',
        'user' => $user
      ];
    } catch (PDOException $e) {
      return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
  }

  /**
   * Check if user exists
   */
  private function userExists($username, $email)
  {
    $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM users 
            WHERE username = :username OR email = :email
        ");

    $stmt->execute([
      ':username' => $username,
      ':email' => $email
    ]);

    return $stmt->fetchColumn() > 0;
  }

  /**
   * Validate input data
   */
  private function validateInput($username, $email, $password)
  {
    // Username validation
    if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
      return false;
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      return false;
    }

    // Email validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }

    // Password validation
    if (empty($password) || strlen($password) < 8) {
      return false;
    }

    // Check password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
      return false;
    }

    return true;
  }

  /**
   * Get user by ID
   */
  public function getUserById($id)
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT id, username, email, first_name, last_name, email_verified, created_at 
                FROM users 
                WHERE id = :id
            ");

      $stmt->execute([':id' => $id]);
      return $stmt->fetch();
    } catch (PDOException $e) {
      return false;
    }
  }

  /**
   * Verify email
   */
  public function verifyEmail($token)
  {
    try {
      $stmt = $this->pdo->prepare("
                UPDATE users 
                SET email_verified = TRUE, verification_token = NULL 
                WHERE verification_token = :token
            ");

      $result = $stmt->execute([':token' => $token]);

      return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
      return false;
    }
  }

  /**
   * Update user information
   */
  public function updateUser($id, $data)
  {
    try {
      $allowedFields = ['first_name', 'last_name', 'email'];
      $updates = [];
      $params = [':id' => $id];

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
}
