<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('You must be logged in to book an appointment.'); window.location.href='login.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_SESSION["user_id"];
    $doctor_id = $_POST["doctor_id"];
    $appointment_date = $_POST["appointment_date"];

    if (empty($doctor_id) || empty($appointment_date)) {
        echo "<script>alert('Please select a doctor and appointment date.'); window.location.href='patient_dashboard.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iis", $patient_id, $doctor_id, $appointment_date);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='patient_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error booking appointment: " . $stmt->error . "'); window.location.href='patient_dashboard.php';</script>";
    }

    $stmt->close();
}
?>
