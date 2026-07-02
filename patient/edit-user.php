<?php
session_start();
include("../connection.php");

// ✅ Check session and user type
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];

// ✅ Fetch patient info
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["patient_id"];
$username = $userfetch["pfname"] . " " . $userfetch["plname"];

// ✅ Delete appointment if requested
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

// ✅ Update patient info
if ($_POST) {
    $name = $_POST['name'];
    $nic = $_POST['nic'];
    $oldemail = $_POST["oldemail"];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $tele = $_POST['Tele'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $id = $_POST['id00'];

    if ($password == $cpassword) {
        // Check if email already exists in patient
        $sqlmain = "SELECT patient_id FROM patient WHERE pemail=? AND patient_id!=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = '1'; // Email exists
        } else {
            // Update patient table
            $sql1 = "UPDATE patient SET pemail=?, pfname=?, plname=?, ppassword=?, pnic=?, ptel=?, paddress=? WHERE patient_id=?";
            $stmt = $database->prepare($sql1);
            $stmt->bind_param("sssssssi", $email, $name, $name, $password, $nic, $tele, $address, $id);
            $stmt->execute();

            // Update webuser table
            $sql2 = "UPDATE webuser SET email=? WHERE email=?";
            $stmt = $database->prepare($sql2);
            $stmt->bind_param("ss", $email, $oldemail);
            $stmt->execute();

            $error = '4'; // Successfully updated
        }
    } else {
        $error = '2'; // Password mismatch
    }
} else {
    $error = '3'; // Form not submitted
}

header("location: settings.php?action=edit&error=" . $error . "&id=" . $id);
?>
