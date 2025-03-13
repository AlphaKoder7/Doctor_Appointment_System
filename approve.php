<?php
session_start();
include "db.php";

header('Content-Type: application/json');

// Check if user is logged in as doctor
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "doctor") {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

if (!isset($_GET["id"])) {
    echo json_encode(["success" => false, "message" => "No appointment ID provided"]);
    exit;
}

$appointment_id = $_GET["id"];
$message = "See you at the hospital"; // Default approval message

// Update the appointment status and add message
$stmt = $conn->prepare("UPDATE appointments SET status = 'approved', message = ? WHERE id = ? AND doctor_id = ?");
$stmt->bind_param("sii", $message, $appointment_id, $_SESSION["user_id"]);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Appointment approved successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>