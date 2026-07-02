<?php
// booking.php
session_start();
include("../connection.php");

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Fallback connection
if (!isset($database) || !($database instanceof mysqli)) {
    $database = new mysqli("localhost", "root", "", "edoc");
    if ($database->connect_error) {
        die("Database connection failed: " . $database->connect_error);
    }
}

// Check patient login
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];

// Fetch patient info
$pstmt = $database->prepare("SELECT * FROM patient WHERE pemail = ?");
$pstmt->bind_param("s", $useremail);
$pstmt->execute();
$presult = $pstmt->get_result();
$patient = $presult->fetch_assoc();
$pstmt->close();

if (!$patient) die("Patient not found.");

$userid = $patient["patient_id"];
$username = $patient["pfname"] . " " . $patient["plname"];

// Check if scheduleid is in URL
$scheduleid = isset($_GET['scheduleid']) ? intval($_GET['scheduleid']) : 0;
$selectedDoctor = null;

if ($scheduleid > 0) {
    $sched_stmt = $database->prepare("
        SELECT s.scheduleid, s.scheduledate, s.scheduletime, d.doctor_id, d.dfname, d.dlname, d.specialties 
        FROM schedule s 
        JOIN doctor d ON s.docid=d.doctor_id 
        WHERE s.scheduleid=?
    ");
    $sched_stmt->bind_param("i", $scheduleid);
    $sched_stmt->execute();
    $sched_result = $sched_stmt->get_result();
    if ($sched_result->num_rows > 0) {
        $selectedDoctor = $sched_result->fetch_assoc();
    }
    $sched_stmt->close();
}

// Fixed channeling fee
$channeling_fee = $selectedDoctor ? "₹250.00" : "₹200.00";

// Generate time slots
function generateTimeSlots($start, $end, $interval = 30) {
    $slots = [];
    $current = strtotime($start);
    $endTime = strtotime($end);
    while ($current <= $endTime) {
        $slots[] = date('H:i', $current);
        $current = strtotime("+$interval minutes", $current);
    }
    return $slots;
}

// Morning and evening slots
$morningSlots = generateTimeSlots('10:00', '13:00', 30);
$eveningSlots = generateTimeSlots('18:00', '19:30', 30);
$allSlots = array_merge($morningSlots, $eveningSlots);

// Fetch all doctors including Test Doctor
if (!$selectedDoctor) {
    $doctors_result = $database->query("
        SELECT doctor_id, dfname, dlname, specialties 
        FROM doctor 
        ORDER BY dfname ASC
    ");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['docid'], $_POST['selectedDate'], $_POST['scheduletime'])) {
    $docid = intval($_POST['docid']);
    $selectedDate = $_POST['selectedDate'];
    $scheduletime = $_POST['scheduletime'];

    // Total appointments check
    $apptCountStmt = $database->prepare("SELECT COUNT(*) AS total FROM appointment WHERE docid=? AND appodate=?");
    $apptCountStmt->bind_param("is", $docid, $selectedDate);
    $apptCountStmt->execute();
    $totalAppt = $apptCountStmt->get_result()->fetch_assoc()['total'];
    $apptCountStmt->close();

    if ($totalAppt >= 10) {
        echo "<script>alert('⚠️ Maximum 10 appointments reached for this doctor today.'); window.history.back();</script>";
        exit();
    }

    // Check if time already booked
    $checkTimeStmt = $database->prepare("SELECT appoid FROM appointment WHERE docid=? AND appodate=? AND appotime=?");
    $checkTimeStmt->bind_param("iss", $docid, $selectedDate, $scheduletime);
    $checkTimeStmt->execute();
    if($checkTimeStmt->get_result()->num_rows > 0){
        echo "<script>alert('⚠️ This time slot is already booked. Please select another slot.'); window.history.back();</script>";
        exit();
    }
    $checkTimeStmt->close();

    // Always create new schedule entry for user-selected date
    $title = "Appointment";
    $nop = 1;
    $insertSchedule = $database->prepare("INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES (?, ?, ?, ?, ?)");
    $insertSchedule->bind_param("isssi", $docid, $title, $selectedDate, $scheduletime, $nop);
    $insertSchedule->execute();
    $scheduleid = $insertSchedule->insert_id;
    $insertSchedule->close();

    // Next appointment number
    $apponum_result = $database->query("SELECT COUNT(appoid)+1 AS apponum FROM appointment WHERE scheduleid = $scheduleid");
    $apponum = ($apponum_result->fetch_assoc()['apponum']) ?? 1;

    // Insert appointment — FIXED STATUS
    $istmt = $database->prepare("INSERT INTO appointment (pid, apponum, scheduleid, appodate, appotime, docid, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $istmt->bind_param("iiissi", $userid, $apponum, $scheduleid, $selectedDate, $scheduletime, $docid);

    if($istmt->execute()){
        header("Location: payment.php?appointment_id=" . $istmt->insert_id);
        exit();
    } else {
        echo "<script>alert('❌ Failed to book appointment. Try again later.');</script>";
    }
    $istmt->close();
}

// Get selected date from GET parameter for dynamic loading
$selectedDateForSlots = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$docForSlots = $selectedDoctor['doctor_id'] ?? (isset($_GET['docid']) ? intval($_GET['docid']) : 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment</title>
<link rel="stylesheet" href="../css/main.css">
<style>
body { background:#d8efff; font-family:'Poppins',sans-serif; }
.container { max-width:520px; margin:80px auto; background:#fff; border-radius:12px; padding:36px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
h2 { text-align:center; color:#333; margin-bottom:18px; }
.detail { display:flex; justify-content:space-between; margin:12px 0; font-size:16px; color:#333; }
.label { font-weight:600; color:#222; }
.value { color:#555; }
.btn-book { display:block; width:100%; margin-top:28px; background:#007bff; color:#fff; border:none; padding:12px; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; }
.btn-book:hover { background:#0056b3; }
.btn-back { display:block; text-align:center; margin-top:14px; color:#007bff; text-decoration:none; }
</style>
<script>
function reloadTimeSlots() {
    const selectedDate = document.getElementById('selectedDate').value;
    const docid = document.querySelector('select[name="docid"]') ? document.querySelector('select[name="docid"]').value : '<?php echo $selectedDoctor['doctor_id'] ?? 0; ?>';
    const scheduleid = '<?php echo $scheduleid; ?>';
    
    window.location.href = '?scheduleid=' + scheduleid + '&date=' + selectedDate + '&docid=' + docid;
}
</script>
</head>
<body>
<div class="container">
<h2>Book an Appointment</h2>

<form method="POST">
    <div class="detail">
        <span class="label">Select Doctor:</span>
        <?php if($selectedDoctor): ?>
            <div class="value">
                <?php echo $selectedDoctor['dfname'] . " " . $selectedDoctor['dlname']; ?> (<?php echo $selectedDoctor['specialties']; ?>)
            </div>
            <input type="hidden" name="docid" value="<?php echo $selectedDoctor['doctor_id']; ?>">
        <?php else: ?>
            <select name="docid" required>
                <?php
                while($doc = $doctors_result->fetch_assoc()){
                    echo "<option value='".$doc['doctor_id']."'>".$doc['dfname']." ".$doc['dlname']." (".$doc['specialties'].")</option>";
                }
                ?>
            </select>
        <?php endif; ?>
    </div>

    <?php if($selectedDoctor): ?>
        <div class="detail">
            <span class="label">Price:</span>
            <div class="value"><?php echo $channeling_fee; ?></div>
        </div>
    <?php endif; ?>

    <div class="detail">
        <span class="label">Select Date:</span>
        <input type="date" name="selectedDate" id="selectedDate" required value="<?php echo $selectedDateForSlots; ?>" min="<?php echo date('Y-m-d'); ?>" onchange="reloadTimeSlots()">
    </div>

    <div class="detail">
        <span class="label">Select Time:</span>
        <select name="scheduletime" required>
            <?php
            $currentTime = date('H:i');
            $today = date('Y-m-d');

            $bookedTimes = [];
            if($docForSlots){
                $res = $database->prepare("SELECT appotime FROM appointment WHERE docid=? AND appodate=?");
                $res->bind_param("is", $docForSlots, $selectedDateForSlots);
                $res->execute();
                $timesResult = $res->get_result();
                while($t = $timesResult->fetch_assoc()){
                    $bookedTimes[] = $t['appotime'];
                }
                $res->close();
            }

            foreach($allSlots as $slot){
                $disabled = "";
                if($selectedDateForSlots == $today && strtotime($slot) <= strtotime($currentTime)){
                    $disabled = "disabled";
                }
                if(in_array($slot, $bookedTimes)){
                    $disabled = "disabled";
                }
                echo "<option value='$slot' $disabled>$slot</option>";
            }
            ?>
        </select>
    </div>

    <div class="detail"><div class="label">Patient:</div><div class="value"><?php echo htmlspecialchars($username); ?></div></div>

    <button type="submit" class="btn-book">Confirm Booking</button>
</form>

<a class="btn-back" href="index.php">← Back to Home</a>
</div>
</body>
</html>
