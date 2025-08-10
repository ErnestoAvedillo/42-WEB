<?php
require_once __DIR__ . '/../EnvLoader.php';
require_once __DIR__ . '/pg_database.php';

class User
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
                INSERT INTO users (uuid, username, email, password, first_name, last_name, verification_token) 
                VALUES (gen_random_uuid(), :username, :email, :password, :first_name, :last_name, :verification_token)
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
        // Return success with user UUID
        $id = $this->pdo->lastInsertId();
        $data = $this->getUserById($id);
        //echo "<p>✓ Registro de usuario exitoso. ID: " . $id . ", UUID: " . $data['uuid'] . "</p>";
        if (!$data['uuid']) {
          return ['success' => false, 'message' => 'Failed to retrieve user UUID'];
        }
        return [
          'id' => $id,
          'uuid' => $data['uuid'],
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
                SELECT id, uuid, username, email, password
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
                SELECT id, username, email, uuid
                FROM users 
                WHERE id = :id
            ");

      $stmt->execute([':id' => $id]);
      return $stmt->fetch();
    } catch (PDOException $e) {
      return false;
    }
  }

  public function getUserByUUID($uuid)
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT id, uuid, username, email 
                FROM users 
                WHERE uuid = :uuid
            ");

      $stmt->execute([':uuid' => $uuid]);
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
  public function updateUser($uuid, $data)
  {
    try {
      $allowedFields = ['username', 'first_name', 'last_name', 'email'];
      $updates = [];
      $params = [':uuid' => $uuid];

      foreach ($data as $field => $value) {
        if (in_array($field, $allowedFields)) {
          $updates[] = "$field = :$field";
          $params[":$field"] = $value;
        }
      }

      if (empty($updates)) {
        return false;
      }

      $sql = "UPDATE users SET " . implode(', ', $updates) . ", updated_at = CURRENT_TIMESTAMP WHERE uuid = :uuid";
      $stmt = $this->pdo->prepare($sql);

      return $stmt->execute($params);
    } catch (PDOException $e) {
      return false;
    }
  }
  public function getUserData($userId)
  {
    if (!$userId) {
      return null;
    }

    $stmt = $this->pdo->prepare("
            SELECT * 
            FROM users 
            WHERE id = :id
        ");
    $stmt->execute([':id' => $userId]);
    return $stmt->fetch();
  }
  /**
   * Check if username is already taken
   */
  public function isUsernameTaken($username)
  {
    $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM users 
            WHERE username = :username
        ");
    $stmt->execute([':username' => $username]);
    return $stmt->fetchColumn() > 0;
  }
  /**
   * Check if email is already taken
   */
  public function isEmailTaken($email)
  {
    $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM users 
            WHERE email = :email
        ");
    $stmt->execute([':email' => $email]);
    return $stmt->fetchColumn() > 0;
  }
  /**
   * Update user profile
   */
  public function updateUserProfile($user_uuid, $data)
  {
    try {
      $allowedFields = [
        'first_name',
        'last_name',
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
      $params = [':uuid' => $user_uuid];

      foreach ($data as $field => $value) {
        if (in_array($field, $allowedFields)) {
          $updates[] = "$field = :$field";
          $params[":$field"] = $value;
        }
      }

      if (empty($updates)) {
        return false;
      }

      $sql = "UPDATE users SET " . implode(', ', $updates) . ", updated_at = CURRENT_TIMESTAMP WHERE uuid = :uuid";
      $stmt = $this->pdo->prepare($sql);

      return $stmt->execute($params);
    } catch (PDOException $e) {
      return false;
    }
  }
}
