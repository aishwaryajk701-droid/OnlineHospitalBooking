<?php
// doctor/payment_settings.php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// fetch logged-in doctor correctly using prepared statement
$useremail = $_SESSION["user"];

$stmt = $database->prepare("SELECT * FROM doctor WHERE docemail = ?");
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();

if ($userrow->num_rows === 0) {
    die("Doctor not found.");
}

$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["doctor_id"];
$username = trim(($userfetch["dfname"] ?? '') . ' ' . ($userfetch["dlname"] ?? ''));

// fetch existing payment details
$payment_query = $database->prepare("SELECT * FROM doctor_payment_details WHERE doctor_id = ?");
$payment_query->bind_param("i", $userid);
$payment_query->execute();
$payment_result = $payment_query->get_result();
$payment_data = $payment_result->fetch_assoc();

$payment_name   = $payment_data ? $payment_data["payment_name"] : $username;
$payment_mobile = $payment_data ? $payment_data["payment_mobile"] : ($userfetch["doctel"] ?? '');
$upi_id         = $payment_data ? $payment_data["upi_id"] : "";
$qr_code        = $payment_data ? $payment_data["qr_code_path"] : "";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $doctor_id = intval($_POST["doctor_id"] ?? $userid);
    $payment_name_post = trim($_POST["payment_name"] ?? "");
    $payment_mobile_post = trim($_POST["payment_mobile"] ?? "");
    $upi_id_post = trim($_POST["upi_id"] ?? "");

    if ($payment_name_post === "") $errors[] = "Payment name is required.";
    if ($payment_mobile_post === "") $errors[] = "Payment mobile is required.";
    if ($upi_id_post === "") $errors[] = "UPI ID is required.";

    // QR upload
    $uploaded_qr_path = $qr_code;

    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] !== UPLOAD_ERR_NO_FILE) {
        $fileErr = $_FILES['qr_code']['error'];

        if ($fileErr !== UPLOAD_ERR_OK) {
            $errors[] = "QR upload failed (error code $fileErr).";
        } else {
            $tmpName = $_FILES['qr_code']['tmp_name'];
            $origName = basename($_FILES['qr_code']['name']);
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (!in_array($ext, $allowed)) {
                $errors[] = "QR image must be JPG, PNG or WEBP.";
            } else {
                $uploadDir = __DIR__ . "/../uploads/doctor_qr/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $newFilename = "qr_doctor_" . $doctor_id . "_" . time() . "." . $ext;
                $targetFull = $uploadDir . $newFilename;

                if (!move_uploaded_file($tmpName, $targetFull)) {
                    $errors[] = "Failed to save uploaded file.";
                } else {
                    $uploaded_qr_path = "../uploads/doctor_qr/" . $newFilename;

                    // delete old QR if replaced
                    if (!empty($qr_code) && $qr_code !== $uploaded_qr_path) {
                        $oldFull = __DIR__ . "/" . $qr_code;
                        if (file_exists($oldFull)) @unlink($oldFull);
                    }
                }
            }
        }
    }

    if (empty($errors)) {

        /*  
        ========================================================
            ✅ UPDATE doctel IN doctor TABLE (NEWLY ADDED)
        ========================================================
        */
        $updateDoctorMobile = $database->prepare("UPDATE doctor SET doctel=? WHERE doctor_id=?");
        $updateDoctorMobile->bind_param("si", $payment_mobile_post, $doctor_id);
        $updateDoctorMobile->execute();
        // -------------------------------------------------------


        // check if row exists
        $checkStmt = $database->prepare("SELECT id FROM doctor_payment_details WHERE doctor_id = ?");
        $checkStmt->bind_param("i", $doctor_id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();

        if ($checkRes->num_rows > 0) {
            // UPDATE
            $updateStmt = $database->prepare(
                "UPDATE doctor_payment_details 
                 SET payment_name=?, payment_mobile=?, upi_id=?, qr_code_path=?, updated_at=CURRENT_TIMESTAMP 
                 WHERE doctor_id=?"
            );
            $updateStmt->bind_param("ssssi", $payment_name_post, $payment_mobile_post, $upi_id_post, $uploaded_qr_path, $doctor_id);
            $ok = $updateStmt->execute();

            if ($ok) {
                header("Location: payment_settings.php?action=success");
                exit;
            } else {
                $errors[] = "Database update failed: " . $database->error;
            }
        } else {
            // INSERT
            $insertStmt = $database->prepare(
                "INSERT INTO doctor_payment_details (doctor_id, payment_name, payment_mobile, upi_id, qr_code_path)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $insertStmt->bind_param("issss", $doctor_id, $payment_name_post, $payment_mobile_post, $upi_id_post, $uploaded_qr_path);
            $ok = $insertStmt->execute();

            if ($ok) {
                header("Location: payment_settings.php?action=success");
                exit;
            } else {
                $errors[] = "Database insert failed: " . $database->error;
            }
        }
    } else {
        // retain posted values
        $payment_name = htmlspecialchars($payment_name_post);
        $payment_mobile = htmlspecialchars($payment_mobile_post);
        $upi_id = htmlspecialchars($upi_id_post);
    }
}

// refresh data
$payment_query = $database->prepare("SELECT * FROM doctor_payment_details WHERE doctor_id = ?");
$payment_query->bind_param("i", $userid);
$payment_query->execute();
$payment_result = $payment_query->get_result();
$payment_data = $payment_result->fetch_assoc();

$payment_name   = $payment_data ? $payment_data["payment_name"] : $payment_name;
$payment_mobile = $payment_data ? $payment_data["payment_mobile"] : $payment_mobile;
$upi_id         = $payment_data ? $payment_data["upi_id"] : $upi_id;
$qr_code        = $payment_data ? $payment_data["qr_code_path"] : $qr_code;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .qr-preview { max-width: 200px; border: 2px solid #ddd; border-radius: 8px; margin-top: 10px; }
        .error-list { color: #b00020; margin-bottom: 12px; text-align: center; }
        .success-msg { color: green; margin-bottom: 12px; text-align: center; }
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
                            <td>
                                <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
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

            <tr class="menu-row"><td class="menu-btn menu-icon-dashbord"><a href="index.php" class="non-style-link-menu"><p class="menu-text">Dashboard</p></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-appoinment"><a href="appointment.php" class="non-style-link-menu"><p class="menu-text">My Appointments</p></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-session"><a href="schedule.php" class="non-style-link-menu"><p class="menu-text">My Sessions</p></a></td></tr>
            <tr class="menu-row"><td class="menu-btn menu-icon-patient"><a href="patient.php" class="non-style-link-menu"><p class="menu-text">My Patients</p></a></td></tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                    <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><p class="menu-text">Settings</p></a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body" style="margin-top: 15px">

        <table border="0" width="100%">
            <tr>
                <td width="13%">
                    <a href="settings.php">
                        <button class="login-btn btn-primary-soft btn btn-icon-back" style="width:125px">Back</button>
                    </a>
                </td>
                <td><p style="font-size: 23px; padding-left:12px; font-weight: 600;">Payment Settings</p></td>
                <td width="15%">
                    <p style="font-size: 14px; color: #777;">Today's Date</p>
                    <p class="heading-sub12"><?php echo date('Y-m-d'); ?></p>
                </td>
                <td width="10%">
                    <button class="btn-label"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>

            <tr>
                <td colspan="4">
                    <center>
                        <div style="margin-top: 30px;">
                            <table width="60%" class="sub-table" border="0" style="padding: 50px;">

                                <?php
                                if (!empty($errors)) {
                                    echo '<div class="error-list"><ul style="list-style:none;">';
                                    foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
                                    echo '</ul></div>';
                                }
                                if (isset($_GET['action']) && $_GET['action'] === 'success') {
                                    echo '<div class="success-msg">Payment details saved successfully.</div>';
                                }
                                ?>

                                <form action="" method="POST" enctype="multipart/form-data">

                                    <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($userid); ?>">

                                    <tr><td><label>Name (as per UPI):</label></td></tr>
                                    <tr><td><input type="text" name="payment_name" class="input-text" value="<?php echo htmlspecialchars($payment_name); ?>" required></td></tr>

                                    <tr><td><label>Mobile Number (UPI linked):</label></td></tr>
                                    <tr><td><input type="tel" name="payment_mobile" class="input-text" value="<?php echo htmlspecialchars($payment_mobile); ?>" required></td></tr>

                                    <tr><td><label>UPI ID:</label></td></tr>
                                    <tr><td><input type="text" name="upi_id" class="input-text" value="<?php echo htmlspecialchars($upi_id); ?>" required></td></tr>

                                    <tr><td><label>Upload QR Code:</label></td></tr>
                                    <tr>
                                        <td>
                                            <input type="file" name="qr_code" class="input-text" accept="image/*" <?php echo empty($qr_code) ? 'required' : ''; ?>>
                                            <?php if (!empty($qr_code)): ?>
                                                <br><br>
                                                <p>Current QR Code:</p>
                                                <img src="<?php echo htmlspecialchars($qr_code); ?>" class="qr-preview">
                                                <p style="font-size: 12px; color: #777;">Upload new to replace</p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">&nbsp;&nbsp;
                                            <input type="submit" value="Save Payment Details" class="login-btn btn-primary btn">
                                        </td>
                                    </tr>

                                </form>

                            </table>
                        </div>
                    </center>
                </td>
            </tr>

        </table>

    </div>
</div>

<?php
if(isset($_GET['action'])){
    $action = $_GET['action'];
    if($action == 'success'){
        echo '<div id="popup1" class="overlay"><div class="popup"><center><h2>Payment Details Saved Successfully!</h2><a class="close" href="payment_settings.php">&times;</a><div class="content">Your payment details have been updated.</div></center></div></div>';
    }
}
?>

</body>
</html>
