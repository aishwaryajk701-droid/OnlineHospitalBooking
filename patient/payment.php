<?php
// payment.php
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

// Get appointment ID from URL
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if ($appointment_id == 0) {
    header("location: appointment.php"); // redirect to doctors.php if no appointment id
    exit();
}

// ------------------------------------------------------
// FETCH appointment + doctor details
// ------------------------------------------------------
$stmt = $database->prepare("
    SELECT a.*, p.pfname, p.plname, p.pemail, p.ptel, 
           d.dfname, d.dlname, d.specialties, d.doctor_id,
           s.scheduledate, s.scheduletime
    FROM appointment a
    JOIN patient p ON a.pid = p.patient_id
    JOIN doctor d ON a.docid = d.doctor_id
    JOIN schedule s ON a.scheduleid = s.scheduleid
    WHERE a.appoid = ?
");

if (!$stmt) {
    die("SQL ERROR (appointment query): " . $database->error);
}

$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Appointment not found.");
}

$appointment = $result->fetch_assoc();
$stmt->close();

$doctor_id = $appointment['doctor_id'];

// ----------------------------------------------------------------------
// FETCH DOCTOR PAYMENT DETAILS
// ----------------------------------------------------------------------
$payQuery = $database->prepare("SELECT * FROM doctor_payment_details WHERE doctor_id = ?");

if (!$payQuery) {
    die("SQL ERROR (payment details query): " . $database->error);
}

$payQuery->bind_param("i", $doctor_id);
$payQuery->execute();
$payResult = $payQuery->get_result();

if ($payResult->num_rows > 0) {
    $paymentData = $payResult->fetch_assoc();
    $upi_id = $paymentData["upi_id"];
    $upi_name = $paymentData["payment_name"];
    $qr_code_path = $paymentData["qr_code_path"];
} else {
    $upi_id = "Not Added";
    $upi_name = "Not Set";
    $qr_code_path = "";
}

$payQuery->close();

// Appointment fee
$channeling_fee = 1.00;

// ------------------------------------------------------------------
// PAYMENT CONFIRMATION — UPDATE APPOINTMENT
// ------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_payment'])) {

    $transaction_id = trim($_POST['transaction_id']);

    if (!empty($transaction_id)) {

        $payment_date = date("Y-m-d H:i:s"); // current datetime

        $updateStmt = $database->prepare("
            UPDATE appointment 
            SET payment_status = 'Paid',
                transaction_id = ?,
                payment_date = ?
            WHERE appoid = ?
        ");

        if (!$updateStmt) {
            die("SQL ERROR (update query): " . $database->error);
        }

        $updateStmt->bind_param("ssi", $transaction_id, $payment_date, $appointment_id);

        if ($updateStmt->execute()) {
            // Redirect to doctors.php after successful payment
            echo "<script>
                    alert('✅ Payment confirmed successfully!');
                    window.location.href='doctors.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('❌ Failed to update payment status.');</script>";
        }

        $updateStmt->close();
    } else {
        echo "<script>alert('⚠️ Please enter transaction ID.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment - Book Appointment</title>
<link rel="stylesheet" href="../css/main.css">
<style>
/* ------------------ STYLING ------------------ */
body { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 20px;
}

.container { 
    max-width: 600px;
    margin: 40px auto;
    background: #fff;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.header { text-align: center; margin-bottom: 30px; }
.header h2 { color: #333; margin: 0 0 10px 0; font-size: 28px; }
.header p { color: #666; margin: 0; font-size: 14px; }

.appointment-details .detail-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.detail-label { font-weight: 500; color: #555; }
.detail-value { font-weight: 600; color: #111; }

.payment-section { margin-top: 30px; text-align: center; }
.payment-section h3 { margin-bottom: 15px; color: #333; }
.payment-amount { font-size: 24px; font-weight: 700; margin-bottom: 20px; color: #222; }

.upi-details { text-align: left; margin-bottom: 20px; }
.upi-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
.upi-label { font-weight: 500; color: #555; }
.upi-value { font-weight: 600; color: #111; }
.copy-btn { background: #667eea; color: #fff; border: none; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 12px; }

.qr-code { margin-top: 15px; text-align: center; }
.qr-code img { width: 200px; height: 200px; border-radius: 8px; }
.qr-placeholder { width: 200px; height: 200px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 8px; color: #999; font-weight: 500; }

.payment-form { margin-top: 30px; text-align: left; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
.form-group input { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px; }

.btn-confirm { width: 100%; padding: 12px; background: #667eea; color: #fff; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin-top: 10px; }
.btn-confirm:hover { background: #5a67d8; }
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>💳 Payment Details</h2>
        <p>Complete your appointment booking</p>
    </div>

    <div class="appointment-details">
        <div class="detail-row"><span class="detail-label">Patient Name:</span><span class="detail-value"><?php echo htmlspecialchars($appointment['pfname'] . ' ' . $appointment['plname']); ?></span></div>
        <div class="detail-row"><span class="detail-label">Doctor:</span><span class="detail-value"><?php echo htmlspecialchars($appointment['dfname'] . ' ' . $appointment['dlname']); ?></span></div>
        <div class="detail-row"><span class="detail-label">Specialization:</span><span class="detail-value"><?php echo htmlspecialchars($appointment['specialties']); ?></span></div>
        <div class="detail-row"><span class="detail-label">Appointment Date:</span><span class="detail-value"><?php echo date('d M Y', strtotime($appointment['scheduledate'])); ?></span></div>
        <div class="detail-row"><span class="detail-label">Appointment Time:</span><span class="detail-value"><?php echo date('h:i A', strtotime($appointment['scheduletime'])); ?></span></div>
        <div class="detail-row"><span class="detail-label">Appointment Number:</span><span class="detail-value">#<?php echo $appointment['apponum']; ?></span></div>
    </div>

    <div class="payment-section">
        <h3>Pay via UPI</h3>
        <div class="payment-amount">₹<?php echo number_format($channeling_fee, 2); ?></div>

        <div class="upi-details">
            <div class="upi-row">
                <span class="upi-label">UPI ID:</span>
                <span class="upi-value" id="upiId"><?php echo $upi_id; ?></span>
                <button class="copy-btn" type="button" onclick="copyUPI()">📋 Copy</button>
            </div>
            <div class="upi-row">
                <span class="upi-label">Name:</span>
                <span class="upi-value"><?php echo $upi_name; ?></span>
            </div>
            <div class="upi-row">
                <span class="upi-label">Amount:</span>
                <span class="upi-value">₹<?php echo number_format($channeling_fee, 2); ?></span>
            </div>
        </div>

        <div class="qr-code">
            <?php if (!empty($qr_code_path)) { ?>
                <img src="<?php echo $qr_code_path; ?>" alt="UPI QR Code">
            <?php } else { ?>
                <div class="qr-placeholder">QR Code<br>Not Uploaded</div>
            <?php } ?>
        </div>
    </div>

    <form method="POST" class="payment-form">
        <div class="form-group">
            <label>Transaction ID *</label>
            <input type="text" name="transaction_id" autocomplete="off" required placeholder="Enter your transaction ID">
        </div>
        <button type="submit" name="confirm_payment" class="btn-confirm">✓ Confirm Payment</button>
    </form>
</div>

<script>
function copyUPI() {
    navigator.clipboard.writeText(document.getElementById('upiId').textContent)
        .then(() => alert('✓ UPI ID copied to clipboard!'));
}
</script>

</body>
</html>
