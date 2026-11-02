<?php
/* ==================================================================
   login.php — handles the form POSTed from index.html
   ------------------------------------------------------------------
   Recommendation: the login form asks for BOTH username and gmail
   as the brief requested. This checks the row matches on username
   AND gmail together, then verifies the password hash.
   ================================================================== */
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$gmail    = trim($_POST['gmail'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $gmail === '' || $password === '') {
    header('Location: index.html?error=missing_fields');
    exit;
}

$stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ? AND gmail = ? LIMIT 1');
$stmt->bind_param('ss', $username, $gmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // ---- success: start the session and go to the dashboard ----
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: dashboard.php');
} else {
    header('Location: index.html?error=invalid_credentials');
}

$stmt->close();
$conn->close();
