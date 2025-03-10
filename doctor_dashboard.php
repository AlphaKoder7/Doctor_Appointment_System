<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php";

// Redirect if not logged in or not a doctor
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "doctor") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Doctor Dashboard</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Welcome, Doctor <?php echo isset($_SESSION["name"]) ? $_SESSION["name"] : "Unknown"; ?>!</h2>
        <p>Here are your upcoming appointments:</p>

        <table>
    <tr>
        <th>Patient Name</th>
        <th>Date & Time</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    $doctor_id = $_SESSION["user_id"];
    $result = $conn->query("SELECT a.*, u.name AS patient_name 
                            FROM appointments a 
                            JOIN users u ON a.patient_id=u.id 
                            WHERE a.doctor_id='$doctor_id'");

    while ($row = $result->fetch_assoc()) {
        echo "<tr id='row-{$row['id']}'>
                <td>{$row['patient_name']}</td>
                <td>{$row['appointment_date']}</td>
                <td id='status-{$row['id']}'>" . ucfirst($row['status']) . "</td>
                <td>";

        if (strtolower($row['status']) === "pending") {
            echo "<button class='btn approve-btn' onclick='updateStatus({$row['id']}, \"approve\")'>Approve</button> 
                  <button class='btn reject-btn' onclick='updateStatus({$row['id']}, \"reject\")'>Reject</button>";
        } else {
            echo "-";
        }

        echo "</td></tr>";
    }
    ?>
</table>
    </main>
    <script>
function updateStatus(appointmentId, action) {
    let url = action === "approve" ? "approve.php" : "reject.php";

    fetch(url + "?id=" + appointmentId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById("status-" + appointmentId).innerText = action === "approve" ? "Approved" : "Rejected";
            document.getElementById("row-" + appointmentId).querySelector("td:last-child").innerHTML = "-"; 
            alert(data.message); // Popup Message
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}
</script>
<?php include "footer.php"; ?>
</body>
</html>
