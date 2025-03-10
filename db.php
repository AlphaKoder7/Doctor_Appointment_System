<?php
$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "hospital_db";
$_SESSION["role"] = "admin";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$new_password = password_hash('12345678', PASSWORD_BCRYPT);
$conn->query("UPDATE users SET password='$new_password' WHERE role='doctor'");

?>

