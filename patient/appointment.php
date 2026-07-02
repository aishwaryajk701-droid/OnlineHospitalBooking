<?php
session_start();
include("../connection.php");

// Check session
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];

// Fetch patient info
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["patient_id"];
$username = $userfetch["pfname"] . " " . $userfetch["plname"];

// -------------------------------------------------
// UPDATE STATUS: Completed or Missed (Matches ENUM exactly)
// -------------------------------------------------
if (isset($_GET['mark']) && isset($_GET['id'])) {
    $mark = $_GET['mark'];
    $id = intval($_GET['id']);

    // STRICT ENUM VALUES FIX
    if ($mark == "completed") {
        $update = $database->prepare("UPDATE appointment SET status='completed' WHERE appoid=?");
        $update->bind_param("i", $id);
        $update->execute();
    }

    if ($mark == "missed") {
        $update = $database->prepare("UPDATE appointment SET status='missed' WHERE appoid=?");
        $update->bind_param("i", $id);
        $update->execute();
    }

    echo "<script>
            alert('Status updated successfully.');
            window.location.href='appointment.php';
          </script>";
    exit();
}

// DELETE appointment
if (isset($_GET['deleteid'])) {
    $deleteid = intval($_GET['deleteid']);
    $delete_sql = "DELETE FROM appointment WHERE appoid=?";
    $stmt = $database->prepare($delete_sql);
    $stmt->bind_param("i", $deleteid);
    $stmt->execute();
    echo "<script>
        alert('Appointment cancelled successfully.');
        window.location.href='appointment.php';
    </script>";
    exit();
}

// Fetch all appointments
$sqlmain = "SELECT 
                a.appoid, 
                a.scheduleid, 
                s.title, 
                CONCAT(d.dfname, ' ', d.dlname) AS doctor_name, 
                CONCAT(p.pfname, ' ', p.plname) AS patient_name,
                s.scheduledate, 
                s.scheduletime, 
                a.apponum, 
                a.appodate,
                a.transaction_id,
                a.payment_status,
                a.status
            FROM appointment a
            LEFT JOIN schedule s ON a.scheduleid = s.scheduleid
            LEFT JOIN patient p ON p.patient_id = a.pid
            LEFT JOIN doctor d ON a.docid = d.doctor_id
            WHERE p.patient_id = ?
            ORDER BY a.appodate DESC, a.appotime DESC";

$stmt = $database->prepare($sqlmain);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Appointments | SmartMeet</title>
<link rel="stylesheet" href="../css/animations.css">  
<link rel="stylesheet" href="../css/main.css">  
<link rel="stylesheet" href="../css/admin.css">
<style>
    body { background-color: #f9fafb; font-family: 'Poppins', sans-serif; }
    .menu { background-color: #ffffff; border-right: 1.5px solid rgb(235, 235, 235); }
    .btn-primary { background-color: #6366f1; border: none; color: white; cursor: pointer; border-radius: 6px; transition: background-color 0.3s ease; }
    .btn-primary:hover { background-color: #4f46e5; }
</style>
</head>
<body>
<div class="container">
    <div class="menu">
        <table class="menu-container" border="0">
            <tr>
                <td style="padding:10px" colspan="2">
                    <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px">
                                <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                            </td>
                            <td style="padding:0px;margin:0px;">
                                <p class="profile-title"><?php echo htmlspecialchars(substr($username, 0, 13)); ?>..</p>
                                <p class="profile-subtitle"><?php echo htmlspecialchars(substr($useremail, 0, 22)); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="../logout.php">
                                    <input type="button" value="Log out" class="logout-btn btn-primary-soft btn">
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="menu-row"><td class="menu-btn menu-icon-home"><a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-doctor"><a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active"><a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Bookings</p></div></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-settings"><a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a></td></tr>
        </table>
    </div>

    <div class="dash-body">
        <table border="0" width="100%" style="border-spacing:0;margin-top:25px;">
            <tr>
                <td width="13%">
                    <button onclick="history.back()" class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px 0;margin-left:20px;width:125px">
                        <font class="tn-in-text">Back</font>
                    </button>
                </td>
                <td><p style="font-size:23px;padding-left:12px;font-weight:600;">My Bookings History</p></td>
                <td width="15%">
                    <p style="font-size:14px;color:rgb(119,119,119);padding:0;margin:0;text-align:right;">Today's Date</p>
                    <p class="heading-sub12"><?php echo date('Y-m-d'); ?></p>
                </td>
            </tr>

            <tr><td colspan="three">
                <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">
                    Total Bookings: <?php echo $result->num_rows; ?>
                </p>
            </td></tr>

            <tr><td colspan="3">
                <center>
                    <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                            <tbody>

<?php
$currentDate = date('Y-m-d');
$now = time();

if ($result->num_rows == 0) {
    echo '<tr><td colspan="7"><center><br><br><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="font-size:20px;color:rgb(49,49,49)">No appointments found!</p></center><br><br></td></tr>';
} else {
    while ($row = $result->fetch_assoc()) {

        $appoid = intval($row["appoid"]);
        $scheduleDate = $row["scheduledate"];
        $scheduleTime = $row["scheduletime"];
        $appointmentDateTime = strtotime("$scheduleDate $scheduleTime");
        $status = strtolower($row["status"] ?? "");

        // AUTO EXPIRE (ENUM FIX)
        if ($appointmentDateTime < $now && 
            !in_array($status, ["completed", "missed", "cancelled", "expired"])) 
        {
            $update = $database->prepare("UPDATE appointment SET status='expired' WHERE appoid=?");
            $update->bind_param("i", $appoid);
            $update->execute();
            $status = "expired";
        }

        echo '<tr><td><div class="dashboard-items search-items"><div>';

        echo '<div class="h3-search">Booking Date: '.$currentDate.'<br>Reference Number: OC-000-'.$appoid.'</div>';
        echo '<div class="h3-search">Transaction ID: <b>'.htmlspecialchars($row["transaction_id"] ?? "N/A").'</b></div>';

        echo '<div class="h1-search">'.htmlspecialchars($row["title"]).'</div>';
        echo '<div class="h3-search">Appointment No: <b>0'.$row["apponum"].'</b></div>';
        echo '<div class="h3-search">Doctor: '.htmlspecialchars($row["doctor_name"]).'</div>';
        echo '<div class="h4-search">Scheduled: '.$scheduleDate.' @ '.substr($scheduleTime,0,5).'</div><br>';

        // STATUS BUTTONS
        if ($status == "completed") {
            echo '<button class="login-btn btn-primary" style="background:#22c55e;width:100%;">Completed</button>';
        }
        else if ($status == "missed") {
            echo '<button class="login-btn btn-primary" style="background:#ef4444;width:100%;">Missed</button>';
        }
        else if ($status == "expired") {
            echo '<button class="login-btn btn-primary" style="background:#9ca3af;width:100%;">Expired</button>';
        }
        else if ($status == "cancelled") {
            echo '<button class="login-btn btn-primary" style="background:#ef4444;width:100%;">Cancelled</button>';
        }
        else {
            if (strtolower($row["payment_status"]) == "paid") {
                echo '
                <a href="appointment.php?mark=completed&id='.$appoid.'">
                    <button class="login-btn btn-primary" style="background:#22c55e;width:100%;margin-bottom:8px;">Mark as Completed</button>
                </a>
                <a href="appointment.php?mark=missed&id='.$appoid.'">
                    <button class="login-btn btn-primary" style="background:#ef4444;width:100%">Mark as Missed</button>
                </a>';
            } else {
                echo '<a href="?deleteid='.$appoid.'">
                        <button class="login-btn btn-primary-soft btn" style="width:100%">Cancel Booking</button>
                      </a>';
            }
        }

        echo '</div></div></td></tr>';
    }
}
?>

                            </tbody>
                        </table>
                    </div>
                </center>
            </td></tr>
        </table>
    </div>
</div>
</body>
</html>
