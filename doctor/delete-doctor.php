<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='d'){
    header("location: ../.php");
    exit();
}

include("../connection.php");

$id = intval($_GET['id']);

// Delete related appointments
$stmt = $database->prepare("DELETE FROM appointment WHERE docid=?");
$stmt->bind_param("i",$id);
$stmt->execute();

// Delete related schedules
$stmt = $database->prepare("DELETE FROM schedule WHERE docid=?");
$stmt->bind_param("i",$id);
$stmt->execute();

// Delete doctor record
$stmt = $database->prepare("DELETE FROM doctor WHERE doctor_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

// Destroy session
session_destroy();

// Redirect to category.php
header("Location: ../category.html");
exit();
?>
