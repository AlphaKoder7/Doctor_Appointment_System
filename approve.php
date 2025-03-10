<?php
session_start();
include "db.php";

if ($_SESSION["role"] != "doctor") {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if (isset($_GET["id"])) {
    $appointment_id = $_GET["id"];
    $sql = "UPDATE appointments SET status='approved' WHERE id='$appointment_id'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Appointment Approved"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
}
?>
