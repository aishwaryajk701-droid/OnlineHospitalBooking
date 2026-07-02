<?php
include('../connection.php');
session_start();
$success = $error = "";

// Use new patient_id, doctor_id and time/date attributes as per database schema.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['booking'])) {
    $booking = $_SESSION['booking'];
    // Booking attributes from session/previous POST
    $doctor_id = intval($booking['doctor_id']);
    $scheduleid = intval($booking['scheduleid']); // Use this if your scheduling logic requires
    $appodate = $booking['appodate']; // formatted as 'YYYY-MM-DD'
    $appotime = $booking['appotime']; // formatted as 'HH:MM:SS'
    $pid = intval($booking['patient_id']); // patient id

    // Payment details from submitted form
    $card_number = $_POST['card_number'];
    $card_name = $_POST['card_name'];
    $expiry = $_POST['expiry'];
    $cvv = $_POST['cvv'];
    $masked_card = substr($card_number, -4);

    // Generate next appointment number for this patient
    // (This example assumes apponum is serial per patient)
    $stmt = $database->prepare("SELECT MAX(apponum) AS maxnum FROM appointment WHERE pid = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $next_apponum = $result["maxnum"] ? $result["maxnum"] + 1 : 1;

    // Insert appointment: Make sure all FK values exist or are set in session
    $ins_stmt = $database->prepare("INSERT INTO appointment (
        pid, apponum, scheduleid, appodate, appotime, docid, status
    ) VALUES (?, ?, ?, ?, ?, ?, 'Active')");
    $ins_stmt->bind_param("iisssi", $pid, $next_apponum, $scheduleid, $appodate, $appotime, $doctor_id);

    if ($ins_stmt->execute()) {
        // Insert payment info
        $appointment_id = $ins_stmt->insert_id;
        $pay_stmt = $database->prepare("INSERT INTO payments (
            appointment_id, card_last4, card_name, expiry, status
        ) VALUES (?, ?, ?, ?, 'Success')");
        $pay_stmt->bind_param("isss", $appointment_id, $masked_card, $card_name, $expiry);

        if ($pay_stmt->execute()) {
            $success = "Payment received and appointment confirmed!<br>Your appointment is booked.";
            unset($_SESSION['booking']);
        } else {
            $error = "Payment info not saved. Contact admin.";
        }
        $pay_stmt->close();
    } else {
        $error = "Appointment not booked. Contact admin.";
    }
    $ins_stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head><title>Booking Status</title></head>
<body>
    <?php if ($success) echo "<div style='color: green;'>$success</div>"; ?>
    <?php if ($error) echo "<div style='color: red;'>$error</div>"; ?>
</body>
</html>
