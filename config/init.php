<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'is_admin' => $_SESSION['user_is_admin'] ?? 0,
        'loyalty_points' => $_SESSION['user_loyalty_points'] ?? 0
    ];
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['user_is_admin'] ?? 0) == 1;
}

function requireAdmin() {
    if (!isAdmin()) {
        setFlash('error', 'Access denied.');
        header('Location: ' . SITE_URL . '/pages/home.php');
        exit;
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        header('Location: ' . SITE_URL . '/pages/login.php');
        exit;
    }
}

function setFlash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function getFlash($type) {
    $key = 'flash_' . $type;
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}
