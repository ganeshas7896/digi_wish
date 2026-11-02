<?php
/* ================================================================
    config.php — DigiWish MySQL connection

    These defaults match the current InfinityFree database. For local
    XAMPP, set the DIGIWISH_DB_* environment variables or change the
    fallback values below.
    DB_HOST is the MySQL host shown under MySQL Databases (not the
    website or dashboard URL), DB_USER and DB_NAME are the database
    credentials shown there, and DB_PASS is the database password.
    ================================================================ */

define('DB_HOST', getenv('DIGIWISH_DB_HOST') ?: 'sql200.infinityfree.com');
define('DB_USER', getenv('DIGIWISH_DB_USER') ?: 'if0_42848682');
define('DB_PASS', getenv('DIGIWISH_DB_PASS') ?: 'Asganesh1234');
define('DB_NAME', getenv('DIGIWISH_DB_NAME') ?: 'if0_42848682_digiwish');
define('DB_PORT', (int) (getenv('DIGIWISH_DB_PORT') ?: 3306));

$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    DB_PORT
);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
?>