<?php
session_start();
include("../connection.php");

// ✅ Only admin can access this page
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'a') {
    header("Location: login.php");
    exit();
}

// ✅ Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// ✅ Handle Delete Appointment
if (isset($_POST["delete_appointment"])) {
    $csrf = $_POST["csrf_token"] ?? "";
    if (!hash_equals($_SESSION["csrf_token"], $csrf)) {
        die("Security check failed!");
    }

    $id = filter_var($_POST["id"], FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $database->prepare("DELETE FROM appointment WHERE appoid = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "<script>alert('Appointment deleted successfully!'); window.location='admin_manage_delete.php';</script>";
        exit();
    }
}

// ✅ Handle Delete Session
if (isset($_POST["delete_session"])) {
    $csrf = $_POST["csrf_token"] ?? "";
    if (!hash_equals($_SESSION["csrf_token"], $csrf)) {
        die("Security check failed!");
    }

    $id = filter_var($_POST["id"], FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $database->prepare("DELETE FROM schedule WHERE scheduleid = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "<script>alert('Session deleted successfully!'); window.location='appointment.php';</script>";
        exit();
    }
}

// ✅ Fetch all appointments
$appQuery = $database->prepare("
    SELECT a.appoid, 
           CONCAT(p.pfname, ' ', p.plname) as patient_name, 
           CONCAT(d.dfname, ' ', d.dlname) as doctor_name, 
           a.appodate, a.appotime, a.status 
    FROM appointment a 
    INNER JOIN patient p ON a.pid = p.patient_id 
    INNER JOIN doctor d ON a.docid = d.doctor_id 
    ORDER BY a.appodate DESC
");
$appQuery->execute();
$appResult = $appQuery->get_result();

// ✅ Fetch all sessions
$sessionQuery = $database->prepare("
    SELECT s.scheduleid, CONCAT(d.dfname, ' ', d.dlname) as doctor_name, 
           s.scheduledate, s.scheduletime, s.title 
    FROM schedule s 
    INNER JOIN doctor d ON s.docid = d.doctor_id 
    ORDER BY s.scheduledate DESC
");
$sessionQuery->execute();
$sessionResult = $sessionQuery->get_result();

function h($val) {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

// ✅ Function to return color for status
function status_color($status) {
    return match($status) {
        'Active' => '#16a34a',     // green
        'Cancelled' => '#dc2626',  // red
        'Completed' => '#2563eb',  // blue
        default => '#6b7280',       // gray
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin | Delete Appointment & Session</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f1f5f9;
  margin: 0;
  padding: 30px;
}
.container {
  max-width: 1200px;
  margin: auto;
  background: #ffffff;
  padding: 25px 35px;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
h1 {
  text-align: center;
  color: #1e293b;
  margin-bottom: 40px;
}
h2 {
  color: #334155;
  border-left: 5px solid #6366f1;
  padding-left: 10px;
  margin-top: 40px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  margin-bottom: 25px;
}
th, td {
  padding: 10px 12px;
  border-bottom: 1px solid #e2e8f0;
  text-align: left;
}
th {
  background-color: #e2e8f0;
}
.btn {
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}
.delete-btn {
  background-color: #ef4444;
  color: #fff;
}
.back-btn {
  background-color: #6366f1;
  color: #fff;
  text-decoration: none;
}
.btn:hover {
  opacity: 0.9;
}
.status-badge {
  padding: 4px 8px;
  border-radius: 6px;
  color: white;
  font-weight: 600;
  display: inline-block;
}
</style>
</head>
<body>
<div class="container">
  <h1>Admin — Manage & Delete Records</h1>

  <!-- ✅ Appointments Section -->
  <h2>Appointments</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Patient</th>
      <th>Doctor</th>
      <th>Date</th>
      <th>Time</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
    <?php if ($appResult->num_rows > 0): ?>
      <?php while ($a = $appResult->fetch_assoc()): ?>
      <tr>
        <td><?=h($a['appoid'])?></td>
        <td><?=h($a['patient_name'])?></td>
        <td><?=h($a['doctor_name'])?></td>
        <td><?=h($a['appodate'])?></td>
        <td><?=h($a['appotime'])?></td>
        <td>
          <span class="status-badge" style="background:<?=status_color($a['status'])?>">
            <?=h($a['status'])?>
          </span>
        </td>
        <td>
          <form method="POST" onsubmit="return confirm('Delete appointment ID <?=h($a['appoid'])?>?');">
            <input type="hidden" name="id" value="<?=h($a['appoid'])?>">
            <input type="hidden" name="csrf_token" value="<?=h($_SESSION['csrf_token'])?>">
            <button type="submit" name="delete_appointment" class="btn delete-btn">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="7" style="text-align:center;">No appointments found.</td></tr>
    <?php endif; ?>
  </table>

  <!-- ✅ Sessions Section -->
  <h2>Doctor Sessions</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Doctor</th>
      <th>Title</th>
      <th>Date</th>
      <th>Time</th>
      <th>Action</th>
    </tr>
    <?php if ($sessionResult->num_rows > 0): ?>
      <?php while ($s = $sessionResult->fetch_assoc()): ?>
      <tr>
        <td><?=h($s['scheduleid'])?></td>
        <td><?=h($s['doctor_name'])?></td>
        <td><?=h($s['title'])?></td>
        <td><?=h($s['scheduledate'])?></td>
        <td><?=h($s['scheduletime'])?></td>
        <td>
          <form method="POST" onsubmit="return confirm('Delete session ID <?=h($s['scheduleid'])?>?');">
            <input type="hidden" name="id" value="<?=h($s['scheduleid'])?>">
            <input type="hidden" name="csrf_token" value="<?=h($_SESSION['csrf_token'])?>">
            <button type="submit" name="delete_session" class="btn delete-btn">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" style="text-align:center;">No sessions found.</td></tr>
    <?php endif; ?>
  </table>

  <!-- ✅ Back Button -->
  <button class="btn back-btn" onclick="history.back()">⬅ Go Back</button>
</div>
</body>
</html>
