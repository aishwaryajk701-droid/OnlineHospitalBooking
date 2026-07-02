<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Patients</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
<?php
session_start();

// ✅ Check admin login
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'a') {
    header("location: ../login.php");
    exit();
}

// ✅ Include database connection
include("../connection.php");
?>

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
                                <p class="profile-title">Administrator</p>
                                <p class="profile-subtitle">admin@edoc.com</p>
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

            <tr class="menu-row">
                <td class="menu-btn menu-icon-dashbord">
                    <a href="index.php" class="non-style-link-menu">
                        <div><p class="menu-text">Dashboard</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor">
                    <a href="doctors.php" class="non-style-link-menu">
                        <div><p class="menu-text">Doctors</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-schedule">
                    <a href="schedule.php" class="non-style-link-menu">
                        <div><p class="menu-text">Schedule</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu">
                        <div><p class="menu-text">Appointment</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                    <a href="patient.php" class="non-style-link-menu non-style-link-menu-active">
                        <div><p class="menu-text">Patients</p></div>
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body">
        <table border="0" width="100%" style="border-spacing: 0;margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="index.php">
                        <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px;margin-left:20px;width:125px">
                            <font class="tn-in-text">Back</font>
                        </button>
                    </a>
                </td>
                <td>
                    <form action="" method="post" class="header-search">
                        <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Patient name or Email" list="patient">
                        &nbsp;&nbsp;
                        <?php
                        echo '<datalist id="patient">';
                        $list11 = $database->query("SELECT CONCAT(pfname, ' ', plname) as pname, pemail FROM patient;");
                        while ($row00 = $list11->fetch_assoc()) {
                            echo "<option value='{$row00['pname']}'>";
                            echo "<option value='{$row00['pemail']}'>";
                        }
                        echo '</datalist>';
                        ?>
                        <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding:10px 25px;">
                    </form>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119,119,119);text-align: right;">Today's Date</p>
                    <p class="heading-sub12" style="margin: 0;">
                        <?php 
                        date_default_timezone_set('Asia/Kolkata');
                        echo date('Y-m-d');
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
                <td colspan="4" style="padding-top:10px;">
                    <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">
                        All Patients (<?php echo $list11->num_rows; ?>)
                    </p>
                </td>
            </tr>

            <?php
            if ($_POST) {
                $keyword = $_POST["search"];
                $sqlmain = "SELECT patient_id, CONCAT(pfname, ' ', plname) as pname, pemail, ptel, pdob FROM patient WHERE pemail='$keyword' OR CONCAT(pfname, ' ', plname)='$keyword' OR CONCAT(pfname, ' ', plname) LIKE '%$keyword%'";
            } else {
                $sqlmain = "SELECT patient_id, CONCAT(pfname, ' ', plname) as pname, pemail, ptel, pdob FROM patient ORDER BY patient_id DESC";
            }
            $result = $database->query($sqlmain);
            ?>

            <tr>
                <td colspan="4">
                    <center>
                    <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" style="border-spacing:0;">
                            <thead>
                                <tr>
                                    <th class="table-headin">Name</th>
                                    <th class="table-headin">Telephone</th>
                                    <th class="table-headin">Email</th>
                                    <th class="table-headin">Date of Birth</th>
                                    <th class="table-headin">Events</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($result->num_rows == 0) {
                                echo '<tr><td colspan="5">
                                <center><br><br><img src="../img/notfound.svg" width="25%">
                                <p class="heading-main12" style="font-size:20px;color:rgb(49,49,49)">No matching patients found!</p>
                                <a href="patient.php"><button class="login-btn btn-primary-soft btn">&nbsp; Show all Patients &nbsp;</button></a>
                                <br><br><br></center></td></tr>';
                            } else {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>
                                        <td>&nbsp;' . htmlspecialchars($row["pname"]) . '</td>
                                        <td>' . htmlspecialchars($row["ptel"]) . '</td>
                                        <td>' . htmlspecialchars($row["pemail"]) . '</td>
                                        <td>' . htmlspecialchars($row["pdob"]) . '</td>
                                        <td>
                                            <div style="display:flex;justify-content:center;">
                                                <a href="?action=view&id=' . $row["patient_id"] . '" class="non-style-link">
                                                    <button class="btn-primary-soft btn button-icon btn-view" style="padding:12px 40px;margin-top:10px;">
                                                        <font class="tn-in-text">View</font>
                                                    </button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>';
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    </center>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php 
if (isset($_GET["action"]) && $_GET["action"] == "view") {
    $id = $_GET["id"];
    $sqlmain = "SELECT * FROM patient WHERE patient_id='$id'";
    $result = $database->query($sqlmain);
    $row = $result->fetch_assoc();
    $name = $row["pfname"] . ' ' . $row["plname"];
    $email = $row["pemail"];
    $dob = $row["pdob"];
    $tele = $row["ptel"];
    $address = $row["paddress"];

    echo '
    <div id="popup1" class="overlay">
        <div class="popup">
            <center>
                <a class="close" href="patient.php">&times;</a>
                <div class="content"></div>
                <div style="display:flex;justify-content:center;">
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr><td><p style="font-size:25px;font-weight:500;">View Details</p><br><br></td></tr>
                        <tr><td class="label-td"><label class="form-label">Patient ID:</label></td></tr>
                        <tr><td class="label-td">P-' . $id . '<br><br></td></tr>
                        <tr><td class="label-td"><label class="form-label">Name:</label></td></tr>
                        <tr><td class="label-td">' . $name . '<br><br></td></tr>
                        <tr><td class="label-td"><label class="form-label">Email:</label></td></tr>
                        <tr><td class="label-td">' . $email . '<br><br></td></tr>
                        <tr><td class="label-td"><label class="form-label">Telephone:</label></td></tr>
                        <tr><td class="label-td">' . $tele . '<br><br></td></tr>
                        <tr><td class="label-td"><label class="form-label">Address:</label></td></tr>
                        <tr><td class="label-td">' . $address . '<br><br></td></tr>
                        <tr><td class="label-td"><label class="form-label">Date of Birth:</label></td></tr>
                        <tr><td class="label-td">' . $dob . '<br><br></td></tr>
                        <tr>
                            <td>
                                <a href="patient.php">
                                    <input type="button" value="OK" class="login-btn btn-primary-soft btn">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </center>
        </div>
    </div>';
}
?>
</body>
</html>