<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "patient") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Patient Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <!-- Book an Appointment -->
        <h2>Book an Appointment</h2>
        <form method="POST" action="book_appointment.php">
    <label>Select Doctor:</label>
    <select name="doctor_id" required>
        <option value="" disabled selected>-- Select a Doctor --</option>
        <?php
        $doctors = $conn->query("SELECT * FROM users WHERE role='doctor'");
        while ($doc = $doctors->fetch_assoc()) {
            echo "<option value='{$doc["id"]}'>{$doc["name"]}</option>";
        }
        ?>
    </select>
    
    <label>Date & Time:</label>
    <input type="datetime-local" name="appointment_date" required>
    <button type="submit">Book</button>
</form>


        <!-- Display Appointments -->
        <h2>My Appointments</h2>
        <table>
            <tr>
                <th>Doctor Name</th>
                <th>Date & Time</th>
                <th>Status</th>
            </tr>
            <?php
            $patient_id = $_SESSION["user_id"];
            $result = $conn->query("SELECT a.*, u.name AS doctor_name 
                                    FROM appointments a 
                                    JOIN users u ON a.doctor_id=u.id 
                                    WHERE a.patient_id='$patient_id'");

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['doctor_name']}</td>
                        <td>{$row['appointment_date']}</td>
                        <td>{$row['status']}</td>
                      </tr>";
            }
            ?>
        </table>
    </main>
    <?php include "footer.php"; ?>

</body>
</html>
