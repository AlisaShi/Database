<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "20240608";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
