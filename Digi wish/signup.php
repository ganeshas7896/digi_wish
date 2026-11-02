<?php
/* ==================================================================
   signup.php — handles the form POSTed from signup.html
   ================================================================== */
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.html');
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$gmail    = trim($_POST['gmail'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirmPassword'] ?? '';

// ---- basic validation ----
if ($fullname === '' || $username === '' || $gmail === '' || $password === '') {
    header('Location: signup.html?error=missing_fields');
    exit;
}

if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
    header('Location: signup.html?error=missing_fields');
    exit;
}

if (strlen($password) < 6) {
    header('Location: signup.html?error=missing_fields');
    exit;
}

if ($password !== $confirm) {
    header('Location: signup.html?error=password_mismatch');
    exit;
}

// ---- check for existing username / email ----
$check = $conn->prepare('SELECT id FROM users WHERE username = ? OR gmail = ? LIMIT 1');
$check->bind_param('ss', $username, $gmail);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Recommendation: for a friendlier UX you could tell the user
    // specifically whether it was the username or the email that
    // clashed by running two separate SELECTs.
    header('Location: signup.html?error=username_taken');
    exit;
}
$check->close();

// ---- insert the new user (password is hashed, never stored raw) ----
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare('INSERT INTO users (fullname, username, gmail, password) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $fullname, $username, $gmail, $hashedPassword);

if ($stmt->execute()) {
    header('Location: index.html?success=account_created');
} else {
    header('Location: signup.html?error=db_error');
}

$stmt->close();
$conn->close();
