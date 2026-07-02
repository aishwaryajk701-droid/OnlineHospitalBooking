<?php
session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");

// Fetch logged-in patient
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$result = $stmt->get_result();
$userfetch = $result->fetch_assoc();

$userid = $userfetch["patient_id"];
$username = $userfetch["pfname"] . " " . $userfetch["plname"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Settings</title>
    <style>
        .dashbord-tables { animation: transitionIn-Y-over 0.5s; }
        .filter-container { animation: transitionIn-X 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
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
                                <p class="profile-title"><?php echo substr($username, 0, 13); ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail, 0, 22); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="menu-row">
                <td class="menu-btn menu-icon-home">
                    <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor">
                    <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                    <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></div></a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body" style="margin-top: 15px">
        <table border="0" width="100%">
            <tr>
                <td width="13%">
                    <a href="../patient/index.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                </td>
                <td>
                    <p style="font-size: 23px;padding-left:12px;font-weight:600;">Settings</p>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: #777;">Today's Date</p>
                    <p class="heading-sub12">
                        <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $today = date('Y-m-d');
                            echo $today;
                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button class="btn-label" style="display:flex;justify-content:center;align-items:center;">
                        <img src="../img/calendar.svg" width="100%">
                    </button>
                </td>
            </tr>

            <tr>
                <td colspan="4">
                    <center>
                        <table class="filter-container" border="0">

                            <tr><td><p style="font-size:20px">&nbsp;</p></td></tr>

                            <tr>
                                <td>
                                    <a href="?action=edit&id=<?php echo $userid ?>&error=0" class="non-style-link">
                                        <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display:flex">
                                            <div class="btn-icon-back dashboard-icons-setting" style="background-image:url('../img/icons/doctors-hover.svg');"></div>
                                            <div>
                                                <div class="h1-dashboard">Account Settings</div><br>
                                                <div class="h3-dashboard" style="font-size:15px;">Edit your Account Details & Change Password</div>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>

                            <tr><td><p style="font-size:5px">&nbsp;</p></td></tr>

                            <tr>
                                <td>
                                    <a href="?action=view&id=<?php echo $userid ?>" class="non-style-link">
                                        <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display:flex;">
                                            <div class="btn-icon-back dashboard-icons-setting" style="background-image:url('../img/icons/view-iceblue.svg');"></div>
                                            <div>
                                                <div class="h1-dashboard">View Account Details</div><br>
                                                <div class="h3-dashboard" style="font-size:15px;">View Personal information About Your Account</div>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>

                            <tr><td><p style="font-size:5px">&nbsp;</p></td></tr>

                            <tr>
                                <td>
                                    <a href="?action=drop&id=<?php echo $userid.'&name='.$username ?>" class="non-style-link">
                                        <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display:flex;">
                                            <div class="btn-icon-back dashboard-icons-setting" style="background-image:url('../img/icons/patients-hover.svg');"></div>
                                            <div>
                                                <div class="h1-dashboard" style="color:#ff5050;">Delete Account</div><br>
                                                <div class="h3-dashboard" style="font-size:15px;">Will Permanently Remove your Account</div>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>

                        </table>
                    </center>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
// POPUPS
if (isset($_GET["action"])) {

    $id = $_GET["id"];
    $action = $_GET["action"];

    /* ================= DELETE ACCOUNT ================= */
    if ($action == 'drop') {
        $nameget = $_GET["name"];
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Are you sure?</h2>
                    <a class="close" href="settings.php">&times;</a>
                    <div class="content">You want to delete Your Account<br>(' . substr($nameget, 0, 40) . ').</div>
                    <div style="display:flex;justify-content:center;">
                        <a href="delete-account.php?id=' . $id . '" class="non-style-link">
                            <button class="btn-primary btn" style="margin:10px;padding:10px;">Yes</button>
                        </a>
                        <a href="settings.php" class="non-style-link">
                            <button class="btn-primary btn" style="margin:10px;padding:10px;">No</button>
                        </a>
                    </div>
                </center>
            </div>
        </div>';
    }

    /* ================= VIEW ACCOUNT ================= */
    if ($action == 'view') {
        $sqlmain = "SELECT * FROM patient WHERE patient_id=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Account Details</h2>
                    <a class="close" href="settings.php">&times;</a>
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr><td><strong>Name:</strong> '.$row["pfname"].' '.$row["plname"].'</td></tr>
                        <tr><td><strong>Email:</strong> '.$row["pemail"].'</td></tr>
                        <tr><td><strong>Address:</strong> '.$row["paddress"].'</td></tr>
                        <tr><td><strong>Date of Birth:</strong> '.$row["pdob"].'</td></tr>
                        <tr><td><strong>Telephone:</strong> '.$row["ptel"].'</td></tr>
                        <tr><td><a href="settings.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a></td></tr>
                    </table>
                </center>
            </div>
        </div>';
    }

    /* ================= EDIT ACCOUNT ================= */
    if ($action == 'edit') {

        $sqlmain = "SELECT * FROM patient WHERE patient_id=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $error = $_GET["error"];

        $errorMsg = "";
        if ($error == 1) $errorMsg = "<p style='color:red;'>Email already exists</p>";
        if ($error == 2) $errorMsg = "<p style='color:red;'>Password mismatch</p>";

        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="settings.php">&times;</a>

                    <h2>Edit Account Details</h2>
                    '.$errorMsg.'
                    
                    <form action="edit-account.php" method="POST" class="add-new-form">
                        
                        <input type="hidden" name="id" value="'.$row["patient_id"].'">
                        <input type="hidden" name="oldemail" value="'.$row["pemail"].'">

                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">

                            <tr><td>Email:</td></tr>
                            <tr><td><input type="email" name="email" class="input-text" value="'.$row["pemail"].'" required></td></tr>

                            <tr><td>First Name:</td></tr>
                            <tr><td><input type="text" name="fname" class="input-text" value="'.$row["pfname"].'" required></td></tr>

                            <tr><td>Last Name:</td></tr>
                            <tr><td><input type="text" name="lname" class="input-text" value="'.$row["plname"].'" required></td></tr>

                            <tr><td>Address:</td></tr>
                            <tr><td><input type="text" name="address" class="input-text" value="'.$row["paddress"].'" required></td></tr>

                            <tr><td>Date of Birth:</td></tr>
                            <tr><td><input type="date" name="dob" class="input-text" value="'.$row["pdob"].'" required></td></tr>

                            <tr><td>Telephone:</td></tr>
                            <tr><td><input type="tel" name="Tele" class="input-text" value="'.$row["ptel"].'" required></td></tr>

                            <tr><td>New Password:</td></tr>
                            <tr><td><input type="password" name="password" class="input-text" required></td></tr>

                            <tr><td>Confirm Password:</td></tr>
                            <tr><td><input type="password" name="cpassword" class="input-text" required></td></tr>

                            <tr>
                                <td>
                                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                                    <input type="submit" value="Save" class="login-btn btn-primary btn">
                                </td>
                            </tr>

                        </table>
                    </form>

                </center>
            </div>
        </div>';
    }
}
?>
</body>
</html>
