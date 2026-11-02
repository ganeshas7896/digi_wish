<?php
/* ==================================================================
   submit_order.php — handles the "Order Details" form on
   dashboard.php and saves it into the `orders` table.
   ------------------------------------------------------------------
   Recommendation: image/video/audio file UPLOADS aren't wired in
   yet (the form only records Yes/No for whether they're needed).
   To accept real file uploads later, add
   enctype="multipart/form-data" to #orderForm in dashboard.php and
   read files from $_FILES here — see README.md "Adding real file
   uploads" for a step-by-step note.
   ================================================================== */
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html?error=missing_fields');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$userId         = $_SESSION['user_id'];
$festival       = trim($_POST['festival'] ?? '');
$recipientName  = trim($_POST['recipientName'] ?? '');
$wishType       = trim($_POST['wishType'] ?? '');
$dob            = $_POST['dob'] ?: null;
$content        = trim($_POST['content'] ?? '');
$extra          = trim($_POST['extra'] ?? '');
$needImage      = $_POST['needImage'] ?? 'No';
$needVideo      = $_POST['needVideo'] ?? 'No';
$needAudio      = $_POST['needAudio'] ?? 'No';
$songDetails    = trim($_POST['songDetails'] ?? '');
$mobile         = trim($_POST['mobile'] ?? '');
$email          = trim($_POST['email'] ?? '');
$orderDate      = $_POST['orderDate'] ?: date('Y-m-d');
$deliveryDate   = $_POST['deliveryDate'] ?: null;

if ($festival === '' || $recipientName === '' || $mobile === '' ||
    !preg_match('/^[0-9]{10}$/', $mobile) || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $deliveryDate === null || !in_array($needImage, ['Yes', 'No'], true) ||
    !in_array($needVideo, ['Yes', 'No'], true) || !in_array($needAudio, ['Yes', 'No'], true) ||
    $deliveryDate < $orderDate) {
    header('Location: dashboard.php?error=missing_fields');
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO orders
     (user_id, festival, recipient_name, wish_type, dob, content, extra,
      need_image, need_video, need_audio, song_details,
      mobile, email, order_date, delivery_date)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$stmt->bind_param(
    'issssssssssssss',
    $userId, $festival, $recipientName, $wishType, $dob, $content, $extra,
    $needImage, $needVideo, $needAudio, $songDetails,
    $mobile, $email, $orderDate, $deliveryDate
);

if ($stmt->execute()) {
    $orderId = $conn->insert_id;
    $adminEmail = 'digiwish11@gmail.com';
    $adminSubject = '[DigiWish] New order received';
    $adminMessage = "A new DigiWish order has been placed.\n\n" .
        "Order ID: {$orderId}\n" .
        "Festival: {$festival}\n" .
        "Recipient: {$recipientName}\n" .
        "Mobile: {$mobile}\n" .
        "Email: {$email}\n" .
        "Order date: {$orderDate}\n" .
        "Delivery date: {$deliveryDate}\n\n" .
        "Please review the order in the dashboard.";

    $headers = "From: DigiWish <no-reply@digiwish.local>\r\n" .
        "Reply-To: {$email}\r\n" .
        "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail($adminEmail, $adminSubject, $adminMessage, $headers);
    @mail($email, '[DigiWish] Your order has been placed', "Hi,\n\nYour DigiWish request has been received and is now in the review queue.\n\nOrder ID: {$orderId}\nFestival: {$festival}\nDelivery date: {$deliveryDate}\n\nThank you for choosing DigiWish.", $headers);

    header('Location: dashboard.php?success=order_placed');
} else {
    header('Location: dashboard.php?error=db_error');
}

$stmt->close();
$conn->close();
