<?php
require_once __DIR__ . '/../EnvLoader.php';
require_once __DIR__ . '/pg_database.php';

class User
{
  private $pdo;
  private $logfile = '/tmp/Camagru.log';
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

  public function copyRegisterFromPending($username)
  {
    try {
      $stmt = $this->pdo->prepare("
                INSERT INTO users (uuid, username, email, password, first_name, last_name) 
                SELECT gen_random_uuid(), username, email, password, first_name, last_name
                FROM pending_registrations 
                WHERE username = :username
            ");

      $result = $stmt->execute([':username' => $username]);

      if ($result) {
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e) {
      file_put_contents($this->logfile, "Error copying registration from pending: " . $e->getMessage() . "\n", FILE_APPEND);
      return false;
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
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e) {
      error_log("Error registering user: " . $e->getMessage());
      return false;
    } catch (Exception $e) {
      error_log("Error registering user: " . $e->getMessage());
      return false;
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
                SELECT id, uuid, username, email, password, two_factor_enabled
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
  public function userExists($username, $email)
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

  /**
   * Get user by UUID
   */
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
   * Get user by email
   */
  public function getUserByEmail($email)
  {
    try {
      $stmt = $this->pdo->prepare("
              SELECT username, email, uuid
              FROM users 
              WHERE email = :email
          ");

      $stmt->execute([':email' => $email]);
      return $stmt->fetch();
    } catch (PDOException $e) {
      return false;
    }
  }
  /**
   * Get user data by username
   */
  public function getUserByUsername($username)
  {
    try {
      $stmt = $this->pdo->prepare("
              SELECT username, email, uuid
              FROM users 
              WHERE username = :username
          ");

      $stmt->execute([':username' => $username]);
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
  public function isUsernameTaken($username, $uuid = null)
  {
    file_put_contents($this->logfile, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " make checks 1\n", FILE_APPEND);

    $params = [':username' => $username];
    $sql = "SELECT COUNT(*) FROM users WHERE username = :username";

    if ($uuid) {
      $sql .= " AND uuid != :uuid";
      $params[':uuid'] = $uuid;
    }

    $stmt = $this->pdo->prepare($sql);
    file_put_contents($this->logfile, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " make checks 2\n", FILE_APPEND);
    $stmt->execute($params);
    file_put_contents($this->logfile, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " make checks 3\n", FILE_APPEND);
    return $stmt->fetchColumn() > 0;
  }
  /**
   * Check if email is already taken
   */
  public function isEmailTaken($email, $uuid = null)
  {
    $params = [':email' => $email];
    $sql = "SELECT COUNT(*) FROM users WHERE email = :email";

    if ($uuid) {
      $sql .= " AND uuid != :uuid";
      $params[':uuid'] = $uuid;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
  }
  /**
   * Update user profile
   */
  public function updateUserProfile($user_uuid, $data)
  {
    try {
      $allowedFields = [
        'username',
        'email',
        'send_notifications',
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
        'profile_uuid'
      ];
      $updates = [];
      $params = [':uuid' => $user_uuid];

      foreach ($data as $field => $value) {
        if (in_array($field, $allowedFields)) {
          $updates[] = "$field = :$field";
          // Handle empty date fields - convert empty strings to NULL for date columns
          if ($field === 'date_of_birth' && (empty($value) || $value === '')) {
            $params[":$field"] = null;
          }
          // Handle empty UUID fields - convert empty strings to NULL for UUID columns
          elseif ($field === 'profile_uuid' && (empty($value) || $value === '')) {
            $params[":$field"] = null;
          } elseif ($field === 'send_notifications') {
            $params[":$field"] = $value ? true : false;
          } else {
            $params[":$field"] = $value;
          }
        }
      }

      if (empty($updates)) {
        return false;
      }

      $sql = "UPDATE users SET " . implode(', ', $updates) . ", updated_at = CURRENT_TIMESTAMP WHERE uuid = :uuid";
      $stmt = $this->pdo->prepare($sql);
      error_log("Executing SQL: $sql with params: " . json_encode($params));

      // Bind parameters with explicit PDO types to avoid type conversion issues
      // (e.g. PHP boolean false -> empty string when sent as PDO::PARAM_STR)
      foreach ($params as $pname => $pvalue) {
        // Determine PDO param type
        if (is_null($pvalue)) {
          $type = PDO::PARAM_NULL;
        } elseif (is_bool($pvalue)) {
          $type = PDO::PARAM_BOOL;
        } elseif (is_int($pvalue)) {
          $type = PDO::PARAM_INT;
        } else {
          $type = PDO::PARAM_STR;
        }

        // bindValue expects parameter name without any change (we already include the leading colon)
        $stmt->bindValue($pname, $pvalue, $type);
      }

      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("Error updating user profile: " . $e->getMessage());
      return false;
    }
  }
  public function getUserProfile($user_uuid)
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT profile_uuid FROM USERS WHERE uuid = :uuid
            ");
      $stmt->execute([':uuid' => $user_uuid]);
      $profile = $stmt->fetch();
      if (!$profile) {
        return null; // User not found
      }
      return $profile['profile_uuid'];
    } catch (PDOException $e) {
      return null;
    }
  }
  public function getAllUsers()
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT id, uuid, username, email 
                FROM users 
                ORDER BY username ASC
            ");

      $stmt->execute();
      return $stmt->fetchAll();
    } catch (PDOException $e) {
      return [];
    }
  }
  public function activate2FA($userId)
  {
    try {
      $stmt = $this->pdo->prepare("
                UPDATE users 
                SET two_factor_enabled = TRUE
                WHERE uuid = :userId
            ");
      $stmt->execute([':userId' => $userId]);
      return ['success' => true, 'message' => '2FA activated successfully'];
    } catch (PDOException $e) {
      return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }
  public function save2FAsecret($userId, $secret, $enabled = true)
  {
    try {
      $stmt = $this->pdo->prepare("
                UPDATE users 
                SET two_factor_secret = :secret , two_factor_enabled = :enabled
                WHERE uuid = :userId
            ");
      $stmt->execute([
        ':secret' => $secret,
        ':userId' => $userId,
        ':enabled' => $enabled
      ]);
      return ['success' => true, 'message' => '2FA activated successfully'];
    } catch (PDOException $e) {
      return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }

  public function disable2FA($userId)
  {
    try {
      $stmt = $this->pdo->prepare("
                UPDATE users 
                SET two_factor_secret = NULL, two_factor_enabled = FALSE
                WHERE uuid = :userId
            ");

      $stmt->execute([':userId' => $userId]);

      return ['success' => true, 'message' => '2FA deactivated successfully'];
    } catch (PDOException $e) {
      return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }
  public function is2FAEnabled($userId)
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT two_factor_enabled
                FROM users 
                WHERE uuid = :userId
            ");

      $stmt->execute([':userId' => $userId]);
      $result = $stmt->fetch();

      return $result ? (bool)$result['two_factor_enabled'] : false;
    } catch (PDOException $e) {
      return false;
    } catch (Exception $e) {
      return false;
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }
  public function get2FASecret($userId)
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT two_factor_secret
                FROM users 
                WHERE uuid = :userId
            ");

      $stmt->execute([':userId' => $userId]);
      $result = $stmt->fetch();

      return $result ? $result['two_factor_secret'] : null;
    } catch (PDOException $e) {
      return null;
    } catch (Exception $e) {
      return null;
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }
  public function setRecoveryToken($userId, $token)
  {
    try {
      $stmt = $this->pdo->prepare("
                UPDATE users 
                SET verification_token = :token, token_created_at = CURRENT_TIMESTAMP
                WHERE uuid = :userId
            ");
      $stmt->execute([
        ':token' => $token,
        ':userId' => $userId
      ]);
      return ['success' => true, 'message' => 'Recovery token saved successfully'];
    } catch (PDOException $e) {
      return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }
  /**
   * Check if the recovery token is valid
   * @param string $username
   * @param string $token
   * @return null Token is invalid expired or user not found
   *       array Token is valid (user data)
   */
  public function isRecoveryTokenValid($username, $token)
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT verification_token, token_created_at
                FROM users 
                WHERE username = :username
            ");
      $stmt->execute([':username' => $username]);
      $result = $stmt->fetch();
      $tokenCreatedAt = $result['token_created_at']; // Acceso manual
      if ($result['verification_token'] === null) {
        error_log("No token found for user $username");
        return null; // No token set
      }
      error_log("Token creation time: " . var_export($tokenCreatedAt, true));
      if ($tokenCreatedAt) {
        $tokenAge = time() - strtotime($tokenCreatedAt);
        if ($tokenAge > 3600) { // 1 hour expiration
          error_log("Token expired: age $tokenAge seconds");
          return null; // Token expired
        }
      } else {
        error_log("No token creation time found");
        return null; // No token creation time found
      }
      return $result && $result['verification_token'] === $token ? $result : null;
    } catch (PDOException $e) {
      error_log("Database error: " . $e->getMessage());
      return null;
    } catch (Exception $e) {
      error_log("Error: " . $e->getMessage());
      return null;
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }
  public function updatePassword($userId, $newPassword)
  {
    try {
      // Hash the new password
      $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

      $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password = :password, verification_token = NULL, token_created_at = NULL
                WHERE uuid = :userId
            ");

      return $stmt->execute([
        ':password' => $hashedPassword,
        ':userId' => $userId
      ]);
    } catch (PDOException $e) {
      return false;
    } catch (Exception $e) {
      return false;
    } finally {
      // Close the database connection
      $this->pdo = null;
    }
  }
  public function send_notification_enabled($user_uuid)
  {
    try {
      $stmt = $this->pdo->prepare("
                SELECT send_notifications 
                FROM users 
                WHERE uuid = :user_uuid
            ");
      $stmt->execute([
        ':user_uuid' => $user_uuid
      ]);
      $result = $stmt->fetch();
      return $result ? (bool)$result['send_notifications'] : false;
    } catch (PDOException $e) {
      return false;
    }
  }
  public function setUserEmailByOldEmail($old_email, $new_email)
  {
    try {
      $stmt = $this->pdo->prepare("
                UPDATE users 
                SET email = :new_email
                WHERE email = :old_email
            ");

      $stmt->execute([
        ':new_email' => $new_email,
        ':old_email' => $old_email
      ]);

      return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
      return false;
    }
  }
}
