<?php
/* logout.php — destroys the session and sends the user back to login */
session_start();
$_SESSION = [];
session_destroy();
header('Location: index.html');
exit;
