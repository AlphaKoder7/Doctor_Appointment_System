<?php
session_start();
include "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: admin.php?error=noid");
    exit;
}

$id = $_GET["id"];

if ($id == $_SESSION["user_id"]) {
    header("Location: admin.php?error=selfdelete");
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: admin.php?success=deleted");
} else {

    header("Location: admin.php?error=deletefailed");
}

$stmt->close();
$conn->close();
?>