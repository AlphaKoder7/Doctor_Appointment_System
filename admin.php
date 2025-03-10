<?php
session_start();
include "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Users List</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT id, name, email, role FROM users");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['role']}</td>
                    <td>
                        <a href='delete_user.php?id={$row['id']}' onclick='return confirmDelete()'>Delete</a>
                    </td>
                  </tr>";
        }
        $stmt->close();
        ?>
    </table>

    <h2>All Appointments</h2>
    <table border="1">
        <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php
        $stmt = $conn->prepare("
            SELECT a.appointment_date, a.status, 
                   p.name AS patient_name, d.name AS doctor_name 
            FROM appointments a 
            JOIN users p ON a.patient_id = p.id 
            JOIN users d ON a.doctor_id = d.id
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['patient_name']}</td>
                    <td>{$row['doctor_name']}</td>
                    <td>{$row['appointment_date']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        $stmt->close();
        ?>
    </table>
</main>

<?php include "footer.php"; ?>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this user?");
    }
</script>

</body>
</html>
