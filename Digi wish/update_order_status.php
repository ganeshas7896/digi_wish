<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$orderId = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
$status = $_POST['status'] ?? 'Delivered';

if ($orderId <= 0 || !in_array($status, ['Pending', 'In Progress', 'Delivered'], true)) {
    header('Location: dashboard.php?error=missing_fields');
    exit;
}

$stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ? AND user_id = ?');
$stmt->bind_param('sii', $status, $orderId, $userId);

if ($stmt->execute()) {
    header('Location: dashboard.php?success=order_completed');
} else {
    header('Location: dashboard.php?error=db_error');
}

$stmt->close();
$conn->close();
