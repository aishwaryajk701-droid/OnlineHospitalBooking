<?php
session_start();
include("../connection.php");

// ✅ Ensure admin is logged in
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'a') {
    header("location: ../login.php");
    exit();
}

$message = "";
$error = "";

// ✅ Handle delete requests securely
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    switch ($action) {
        case 'delete_doctor':
            $stmt = $database->prepare("DELETE FROM doctor WHERE doctor_id = ?");
            $stmt->bind_param("i", $id);
            $message = $stmt->execute() ? "Doctor deleted successfully!" : "Error deleting doctor!";
            break;

        case 'delete_patient':
            $stmt = $database->prepare("DELETE FROM patient WHERE patient_id = ?");
            $stmt->bind_param("i", $id);
            $message = $stmt->execute() ? "Patient deleted successfully!" : "Error deleting patient!";
            break;

        case 'delete_session':
            $stmt = $database->prepare("DELETE FROM schedule WHERE scheduleid = ?");
            $stmt->bind_param("i", $id);
            $message = $stmt->execute() ? "Session deleted successfully!" : "Error deleting session!";
            break;

        default:
            $error = "Invalid action!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Delete Management</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/animations.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f3f6fd;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 94%;
            height:auto;
            margin: 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
            padding: 30px 40px;
            animation: transitionIn-Y-over 0.6s;
        }
        h1 {
            text-align: center;
            color: #1d3557;
            font-weight: 700;
            margin-bottom: 25px;
        }
        h2 {
            color: #264653;
            font-size: 20px;
            margin-top: 45px;
            border-left: 6px solid #3b82f6;
            padding-left: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #3b82f6;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .delete-btn {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .delete-btn:hover {
            background: #dc2626;
        }
        .message {
            text-align: center;
            color: green;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .error {
            text-align: center;
            color: red;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .back-btn {
            display: inline-block;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            text-decoration: none;
            font-size: 15px;
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #1e40af;
        }
        .no-data {
            text-align: center;
            color: gray;
            font-style: italic;
        }
        .section-divider {
            height: 2px;
            background: #e5e7eb;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑 Admin Delete Management</h1>
        <div class="container">      
            <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
         <div class="section-divider"></div>

        <!-- Doctor Management Section -->
        <h2>Doctors</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Doctor Name</th>
                <th>Email</th>
                <th>Specialty</th>
                <th>Action</th>
            </tr>
            <?php
            $docResult = $database->query("SELECT doctor_id, dfname, dlname, docemail, specialties FROM doctor ORDER BY doctor_id DESC");
            if ($docResult->num_rows > 0):
                while ($row = $docResult->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['doctor_id']) ?></td>
                    <td><?= htmlspecialchars($row['dfname'] . ' ' . $row['dlname']) ?></td>
                    <td><?= htmlspecialchars($row['docemail']) ?></td>
                    <td><?= htmlspecialchars($row['specialties']) ?></td>
                    <td>
                        <a href="?action=delete_doctor&id=<?= htmlspecialchars($row['doctor_id']) ?>" 
                           onclick="return confirm('Are you sure you want to delete this doctor?')">
                           <button class="delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5" class="no-data">No doctors found</td></tr>
            <?php endif; ?>
        </table>

        <div class="section-divider"></div>

        <!-- Patient Management Section -->
        <h2>Patients</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php
            $patResult = $database->query("SELECT patient_id, pfname, plname, pemail FROM patient ORDER BY patient_id DESC");
            if ($patResult->num_rows > 0):
                while ($row = $patResult->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['patient_id']) ?></td>
                    <td><?= htmlspecialchars($row['pfname'] . ' ' . $row['plname']) ?></td>
                    <td><?= htmlspecialchars($row['pemail']) ?></td>
                    <td>
                        <a href="?action=delete_patient&id=<?= htmlspecialchars($row['patient_id']) ?>" 
                           onclick="return confirm('Are you sure you want to delete this patient?')">
                           <button class="delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="4" class="no-data">No patients found</td></tr>
            <?php endif; ?>
        </table>

        <div class="section-divider"></div>

        <!-- Session Management Section -->
        <h2>Doctor Sessions</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
            <?php
            $sessionResult = $database->query("
                SELECT s.scheduleid, s.scheduledate, s.scheduletime, d.dfname, d.dlname
                FROM schedule s
                INNER JOIN doctor d ON s.docid = d.doctor_id
                ORDER BY s.scheduleid DESC
            ");
            if ($sessionResult->num_rows > 0):
                while ($row = $sessionResult->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['scheduleid']) ?></td>
                    <td><?= htmlspecialchars($row['dfname'] . ' ' . $row['dlname']) ?></td>
                    <td><?= htmlspecialchars($row['scheduledate']) ?></td>
                    <td><?= htmlspecialchars($row['scheduletime']) ?></td>
                    <td>
                        <a href="?action=delete_session&id=<?= htmlspecialchars($row['scheduleid']) ?>" 
                           onclick="return confirm('Are you sure you want to delete this session?')">
                           <button class="delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5" class="no-data">No sessions found</td></tr>
            <?php endif; ?>
        </table>

        <!-- Back Button -->
        <center>
            <a href="javascript:history.back()" class="back-btn">⬅ Back</a>
        </center>

    </div>
</body>
</html>
