<?php
include("../connection.php");

if($_POST){
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $stmt = $database->prepare("UPDATE appointment SET status=? WHERE appointment_id=?");
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();

    header("Location: schedule.php?action=view&id=".$_GET['schedule_id']);
}
?>
