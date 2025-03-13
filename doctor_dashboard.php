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

// First, check if you need to add a 'message' column to the appointments table
$check_column = $conn->query("SHOW COLUMNS FROM appointments LIKE 'message'");
if ($check_column->num_rows == 0) {
    // Add message column if it doesn't exist
    $conn->query("ALTER TABLE appointments ADD COLUMN message TEXT AFTER status");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
    </style>
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
                <th>Message</th>
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
                        <td id='message-{$row['id']}'>" . (empty($row['message']) ? "-" : $row['message']) . "</td>
                        <td>";

                if (strtolower($row['status']) === "pending") {
                    echo "<button class='btn approve-btn' onclick='updateStatus({$row['id']}, \"approve\")'>Approve</button> 
                          <button class='btn reject-btn' onclick='showRejectModal({$row['id']})'>Reject</button>";
                } else {
                    echo "-";
                }

                echo "</td></tr>";
            }
            ?>
        </table>
    </main>
    
    <!-- Rejection Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Reject Appointment</h2>
            <p>Please provide a reason for rejecting this appointment:</p>
            <textarea id="rejectMessage" rows="4" style="width: 100%;" placeholder="Enter your message here..."></textarea>
            <input type="hidden" id="currentAppointmentId" value="">
            <div style="margin-top: 15px;">
                <button onclick="submitRejection()">Submit</button>
                <button onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>
    
    <script>
        function updateStatus(appointmentId, action) {
            if (action === "approve") {
                fetch("approve.php?id=" + appointmentId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("status-" + appointmentId).innerText = "Approved";
                        document.getElementById("message-" + appointmentId).innerText = "See you at the hospital";
                        document.getElementById("row-" + appointmentId).querySelector("td:last-child").innerHTML = "-"; 
                        alert(data.message);
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        }

        function showRejectModal(appointmentId) {
            document.getElementById("currentAppointmentId").value = appointmentId;
            document.getElementById("rejectModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("rejectModal").style.display = "none";
            document.getElementById("rejectMessage").value = "";
        }

        function submitRejection() {
            const appointmentId = document.getElementById("currentAppointmentId").value;
            const message = document.getElementById("rejectMessage").value;
            
            if (!message.trim()) {
                alert("Please provide a reason for rejection");
                return;
            }
            
            const formData = new FormData();
            formData.append("message", message);
            
            fetch("reject.php?id=" + appointmentId, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    document.getElementById("status-" + appointmentId).innerText = "Rejected";
                    document.getElementById("message-" + appointmentId).innerText = message;
                    document.getElementById("row-" + appointmentId).querySelector("td:last-child").innerHTML = "-"; 
                    alert(data.message);
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