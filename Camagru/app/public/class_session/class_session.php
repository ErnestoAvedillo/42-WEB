<?php
class SessionManager
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $_SESSION['session_started'] = true; // Indicate that the session has started
            // Check if there is an existing coockie
            if (!isset($_COOKIE['session_started'])) {
                setcookie('session_started', 'true', time() + 3600, '/');
            }
        } else {
            $_SESSION['session_started'] = false; // Indicate that the session has not started
        }
    }

    public static function isSessionActive()
    {
        return isset($_SESSION['session_started']) && $_SESSION['session_started'] === true;
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
    public static function setSessionData($key, $value)
    {
        $_SESSION[$key] = $value;
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
