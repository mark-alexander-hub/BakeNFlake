<?php
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/pages/home.php');
    exit;
}

$action = $_POST['action'] ?? '';
$token = $_POST['csrf_token'] ?? '';

if (!validateCSRFToken($token)) {
    setFlash('error', 'Invalid request. Please try again.');
    header('Location: ' . SITE_URL . '/pages/login.php');
    exit;
}

$conn = getDBConnection();

if ($action === 'signup') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        setFlash('error', 'All fields are required.');
        header('Location: ' . SITE_URL . '/pages/signup.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Please enter a valid email address.');
        header('Location: ' . SITE_URL . '/pages/signup.php');
        exit;
    }

    if (strlen($password) < 8) {
        setFlash('error', 'Password must be at least 8 characters.');
        header('Location: ' . SITE_URL . '/pages/signup.php');
        exit;
    }

    if ($password !== $confirmPassword) {
        setFlash('error', 'Passwords do not match.');
        header('Location: ' . SITE_URL . '/pages/signup.php');
        exit;
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        setFlash('error', 'An account with this email already exists.');
        header('Location: ' . SITE_URL . '/pages/signup.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $email, $hash);
    $stmt->execute();

    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_is_admin'] = 0;
    $_SESSION['user_loyalty_points'] = 0;

    setFlash('success', 'Welcome to Bake\'n Flake, ' . $name . '!');
    header('Location: ' . SITE_URL . '/pages/menu.php');
    exit;

} elseif ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        setFlash('error', 'Email and password are required.');
        header('Location: ' . SITE_URL . '/pages/login.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, email, password_hash, is_admin, loyalty_points FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_is_admin'] = $user['is_admin'];
        $_SESSION['user_loyalty_points'] = $user['loyalty_points'];

        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        header('Location: ' . SITE_URL . '/pages/home.php');
        exit;
    } else {
        setFlash('error', 'Invalid email or password.');
        header('Location: ' . SITE_URL . '/pages/login.php');
        exit;
    }
}

$conn->close();
header('Location: ' . SITE_URL . '/pages/home.php');
exit;
