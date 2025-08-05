<?php
require_once __DIR__ . '/class_session.php';
SessionManager::getInstance();
// Crear una instancia global de SessionManager si no existe
if (!SessionManager::isSessionActive()) {
    if (SessionManager::getInstance()) { // Esto iniciará la sesión desde el constructor
        SessionManager::setSessionKey('message', "SessionManager instance created.<br>");
        SessionManager::setSessionKey('session_started', true); // Indicate that the session has started
        SessionManager::setSessionKey('logged_in', false); // Default to not logged in
    } else {
        SessionManager::setSessionKey('message', "Failed to create SessionManager instance.<br>");
        SessionManager::setSessionKey('session_started', false); // Indicate that the session has not started
        exit("Error: Unable to create session manager instance.");
    }
} else {
    SessionManager::setSessionKey('message', "SessionManager instance already exists.<br>");
}
// Puedes registrar mensajes si necesitas
