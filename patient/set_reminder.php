<?php
include("../connection.php");
date_default_timezone_set('Asia/Kolkata');

// Look for appointments 15 min from now
$now = date('Y-m-d H:i:s');
$windowStart = date('Y-m-d H:i:s', strtotime('+15 minutes'));
$windowDate = date('Y-m-d', strtotime('+15 minutes'));

$stmt = $database->prepare("
    SELECT a.appodate, a.appotime, p.pemail, p.pfname, d.dfname, d.dlname
    FROM appointment a
    JOIN patient p ON a.pid = p.patient_id
    JOIN doctor d ON a.docid = d.doctor_id
    WHERE a.status = 'Active'
        AND CONCAT(a.appodate, ' ', a.appotime) BETWEEN ? AND ?
");
$stmt->bind_param("ss", $now, $windowStart);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $subject = "Your Appointment Reminder";
    $to = $row['pemail'];
    $doctor = $row['dfname'] . ' ' . $row['dlname'];
    $name = $row['pfname'];
    $time = $row['appodate'] . " at " . $row['appotime'];
    $msg = "Dear $name,\n\nThis is a reminder for your upcoming appointment with Dr. $doctor on $time. Please arrive on time.\n\nThank you.";
    $headers = "From: hospital@edoc.com";

    // Send email
    mail($to, $subject, $msg, $headers);
}
$stmt->close();
?>
