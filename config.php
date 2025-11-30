<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'peaceconnect';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die(json_encode(['error' => 'Connection failed: ' . mysqli_connect_error()]));
}

mysqli_set_charset($conn, "utf8mb4");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}
?>
