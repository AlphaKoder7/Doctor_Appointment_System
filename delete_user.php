<?php
session_start();
include "db.php";

// Check if user is logged in as admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET["id"])) {
    header("Location: admin.php?error=noid");
    exit;
}

$id = $_GET["id"];

// Don't allow admin to delete themselves
if ($id == $_SESSION["user_id"]) {
    header("Location: admin.php?error=selfdelete");
    exit;
}

// Prepare and execute the delete statement
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Success
    header("Location: admin.php?success=deleted");
} else {
    // Error
    header("Location: admin.php?error=deletefailed");
}

$stmt->close();
$conn->close();
?>