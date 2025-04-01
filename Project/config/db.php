<?php
$host = 'localhost';
$db = 'livestock_monitoring';
$user = 'root';
$pass = 'David';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

global $conn;
?>
