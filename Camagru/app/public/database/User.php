<?php
require_once 'database.php';

class User
{
    private $manager;
    private $database;
    private $collection;

    public function __construct()
    {
        $db = new Database();
        $this->manager = $db->connect();
        $this->database = $db->getDatabase();
        $this->collection = 'users';
    }

    /**
     * Registrar un nuevo usuario
     */
    public function register($username, $email, $password, $firstName = '', $lastName = '')
    {
        try {
            // Validar datos
            if (!$this->validateInput($username, $email, $password)) {
                return ['success' => false, 'message' => 'Datos de entrada inválidos'];
            }

            // Verificar si el usuario ya existe
            if ($this->userExists($username, $email)) {
                return ['success' => false, 'message' => 'El usuario o email ya existe'];
            }

            // Preparar documento del usuario
            $userDocument = [
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'is_active' => true,
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'updated_at' => new MongoDB\BSON\UTCDateTime(),
                'profile_picture' => null,
                'bio' => '',
                'email_verified' => false,
                'verification_token' => $this->generateVerificationToken()
            ];

            // Insertar usuario
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->insert($userDocument);

            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            $result = $this->manager->executeBulkWrite($this->database . '.' . $this->collection, $bulk, $writeConcern);

            if ($result->getInsertedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente',
                    'user_id' => (string)$userDocument['_id'] ?? null
                ];
            } else {
                return ['success' => false, 'message' => 'Error al registrar usuario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
        }
    }

    /**
     * Verificar si un usuario ya existe
     */
    private function userExists($username, $email)
    {
        try {
            $filter = [
                '$or' => [
                    ['username' => $username],
                    ['email' => $email]
                ]
            ];

            $query = new MongoDB\Driver\Query($filter, ['limit' => 1]);
            $cursor = $this->manager->executeQuery($this->database . '.' . $this->collection, $query);

            return count($cursor->toArray()) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validar datos de entrada
     */
    private function validateInput($username, $email, $password)
    {
        // Validar username
        if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
            return false;
        }

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Validar password
        if (strlen($password) < 8) {
            return false;
        }

        // Verificar que contenga al menos una mayúscula, minúscula y número
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Autenticar usuario
     */
    public function login($username, $password)
    {
        try {
            $filter = [
                '$or' => [
                    ['username' => $username],
                    ['email' => $username]
                ],
                'is_active' => true
            ];

            $query = new MongoDB\Driver\Query($filter, ['limit' => 1]);
            $cursor = $this->manager->executeQuery($this->database . '.' . $this->collection, $query);
            $users = $cursor->toArray();

            if (count($users) === 0) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }

            $user = $users[0];

            if (password_verify($password, $user->password)) {
                // Actualizar último login
                $this->updateLastLogin($user->_id);

                return [
                    'success' => true,
                    'message' => 'Login exitoso',
                    'user' => [
                        'id' => (string)$user->_id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'first_name' => $user->first_name ?? '',
                        'last_name' => $user->last_name ?? ''
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'Contraseña incorrecta'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar último login
     */
    private function updateLastLogin($userId)
    {
        try {
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['_id' => $userId],
                ['$set' => ['last_login' => new MongoDB\BSON\UTCDateTime()]],
                ['multi' => false, 'upsert' => false]
            );

            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            $this->manager->executeBulkWrite($this->database . '.' . $this->collection, $bulk, $writeConcern);
        } catch (Exception $e) {
            // Log error but don't fail login
            error_log("Error updating last login: " . $e->getMessage());
        }
    }

    /**
     * Generar token de verificación
     */
    private function generateVerificationToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Obtener usuario por ID
     */
    public function getUserById($userId)
    {
        try {
            $filter = ['_id' => new MongoDB\BSON\ObjectId($userId)];
            $query = new MongoDB\Driver\Query($filter, ['limit' => 1]);
            $cursor = $this->manager->executeQuery($this->database . '.' . $this->collection, $query);
            $users = $cursor->toArray();

            if (count($users) > 0) {
                $user = $users[0];
                return [
                    'success' => true,
                    'user' => [
                        'id' => (string)$user->_id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'first_name' => $user->first_name ?? '',
                        'last_name' => $user->last_name ?? '',
                        'bio' => $user->bio ?? '',
                        'created_at' => $user->created_at,
                        'email_verified' => $user->email_verified ?? false
                    ]
                ];
            }

            return ['success' => false, 'message' => 'Usuario no encontrado'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar perfil de usuario
     */
    public function updateProfile($userId, $data)
    {
        try {
            $updateData = [];

            if (isset($data['first_name'])) $updateData['first_name'] = $data['first_name'];
            if (isset($data['last_name'])) $updateData['last_name'] = $data['last_name'];
            if (isset($data['bio'])) $updateData['bio'] = $data['bio'];

            $updateData['updated_at'] = new MongoDB\BSON\UTCDateTime();

            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectId($userId)],
                ['$set' => $updateData],
                ['multi' => false, 'upsert' => false]
            );

            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
            $result = $this->manager->executeBulkWrite($this->database . '.' . $this->collection, $bulk, $writeConcern);

            if ($result->getModifiedCount() > 0) {
                return ['success' => true, 'message' => 'Perfil actualizado exitosamente'];
            } else {
                return ['success' => false, 'message' => 'No se realizaron cambios'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
