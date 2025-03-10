<?php
session_start();
include "db.php";

if ($_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

if (isset($_GET["id"])) {
    $user_id = $_GET["id"];
    $sql = "DELETE FROM users WHERE id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "User deleted! <a href='admin_panel.php'>Go Back</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

