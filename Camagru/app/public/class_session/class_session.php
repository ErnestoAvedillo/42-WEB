<?php
class SessionManager
{
    private $logFile = '/tmp/session_manager.log';
    private static $instance = null;

    // Constructor privado para patrón Singleton
    private function __construct()
    {
        $this->initializeSession();
    }

    // Método para obtener la única instancia
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function initializeSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $_SESSION['session_started'] = true; // Indicate that the session has started
            $_SESSION['message'] = "New session started successfully.<br>";
            //$_SESSION['logged_in'] = false; // Default to not logged in
        } else {
            $_SESSION['message'] = "Session is already started.";
        }
    }

    public static function isSessionActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
        // return isset($_SESSION['session_started']) && $_SESSION['session_started'] === true;
    }

    public static function setSessionActive($active)
    {
        $_SESSION['session_started'] = $active;
    }

    public static function destroySession()
    {
        if (self::isSessionActive()) {
            session_unset();
            session_destroy();
        }
    }
    public static function getSessionData($key)
    {
        return $_SESSION[$key] ?? null;
    }
    public static function setSessionKey($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public static function getSessionKey($key)
    {
        return $_SESSION[$key] ?? null;
    }
    public static function saveDataSession($data)
    {
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
        // Compatibilidad: si existe 'id', también guardarlo como 'user_id'
        if (isset($data['id'])) {
            $_SESSION['user_id'] = $data['id'];
        }
        //para visualizar lo que guarda
        $_SESSION['logged_in'] = true;
    }
    public static function clearSessionData()
    {
        session_unset();
    }
    // set cookies after lognin
    public static function setCookie($name, $value, $expires = 3600)
    {
        setcookie($name, $value, time() + $expires, "/");
    }

    public static function getCookie($name)
    {
        return $_COOKIE[$name] ?? null;
    }

    public static function deleteCookie($name)
    {
        setcookie($name, "", time() - 3600, "/");
    }
    public static function clearCookies()
    {
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, "", time() - 3600, "/");
        }
    }
    public static function printSessionData()
    {
        echo "<h2>Session Data</h2>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
    }
    public static function printCookies()
    {
        echo "<h2>Cookies</h2>";
        echo "<pre>";
        print_r($_COOKIE);
        echo "</pre>";
    }
    public static function destroy()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        self::clearCookies();
    }
    public static function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
}
