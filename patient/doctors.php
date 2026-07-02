<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Doctors</title>
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

// Fetch patient details
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["patient_id"];
$username = $userfetch["pfname"] . " " . $userfetch["plname"];
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
                <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active">
                    <a href="doctors.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">All Doctors</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings">
                    <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body">
        <table border="0" width="100%" style="border-spacing: 0;margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="doctors.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px 0;width:125px"><font class="tn-in-text">Back</font></button></a>
                </td>
                <td>
                    <form action="" method="post" class="header-search">
                        <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors">&nbsp;&nbsp;
                        <?php
                        echo '<datalist id="doctors">';
                        $list11 = $database->query("SELECT CONCAT(dfname, ' ', dlname) AS docname, docemail FROM doctor;");
                        while ($row00 = $list11->fetch_assoc()) {
                            echo "<option value='{$row00["docname"]}'>";
                            echo "<option value='{$row00["docemail"]}'>";
                        }
                        echo '</datalist>';
                        ?>
                        <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding:10px 25px;">
                    </form>
                </td>
                <td width="15%">
                    <p style="font-size:14px;color:#777;text-align:right;">Today's Date</p>
                    <p class="heading-sub12"><?php date_default_timezone_set('Asia/Kolkata'); echo date('Y-m-d'); ?></p>
                </td>
                <td width="10%">
                    <button class="btn-label"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:10px;">
                    <p class="heading-main12" style="margin-left:45px;font-size:18px;color:#313131">
                        All Doctors (<?php echo $list11->num_rows; ?>)
                    </p>
                </td>
            </tr>

            <?php
            if ($_POST) {
                $keyword = $_POST["search"];
                $sqlmain = "SELECT * FROM doctor WHERE docemail='$keyword' 
                            OR CONCAT(dfname, ' ', dlname)='$keyword' 
                            OR dfname LIKE '%$keyword%' 
                            OR dlname LIKE '%$keyword%'";
            } else {
                $sqlmain = "SELECT * FROM doctor ORDER BY doctor_id ASC";
            }

            $result = $database->query($sqlmain);
            ?>
            <tr>
                <td colspan="4">
                    <center>
                        <div class="abc scroll">
                            <table width="93%" class="sub-table scrolldown" border="0">
                                <thead>
                                    <tr>
                                        <th class="table-headin">Doctor Name</th>
                                        <th class="table-headin">Email</th>
                                        <th class="table-headin">Specialties</th>
                                        <th class="table-headin">Events</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($result->num_rows == 0) {
                                    echo '<tr><td colspan="4"><center><br><br><img src="../img/notfound.svg" width="25%">
                                    <p class="heading-main12" style="font-size:20px;color:#313131">No matching doctors found!</p>
                                    <a href="doctors.php"><button class="login-btn btn-primary-soft btn">&nbsp;Show all Doctors&nbsp;</button></a>
                                    <br><br><br></center></td></tr>';
                                } else {
                                    $counter = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        $docid = $row["doctor_id"];
                                        $name = $row["dfname"] . " " . $row["dlname"];
                                        $email = $row["docemail"];
                                        $specialty = $row["specialties"];

                                        if ($counter == 1) {
                                            $sessionFile = "test_session.php";
                                        } elseif ($counter == 2) {
                                            $sessionFile = "smith_session.php";
                                        } elseif ($counter == 3) {
                                            $sessionFile = "lee_session.php";
                                        } else {
                                            $sessionFile = "rajesh_session.php";
                                        }

                                        echo '<tr>
                                            <td>' . htmlspecialchars($name) . '</td>
                                            <td>' . htmlspecialchars($email) . '</td>
                                            <td>' . htmlspecialchars($specialty) . '</td>
                                            <td>
                                                <div style="display:flex;justify-content:center;">
                                                    <a href="?action=view&id=' . $docid . '" class="non-style-link">
                                                        <button class="btn-primary-soft btn button-icon btn-view" style="margin-top:10px;">View</button>
                                                    </a>
                                                    &nbsp;&nbsp;&nbsp;
                                                    <a href="' . $sessionFile . '" class="non-style-link">
                                                        <button class="btn-primary-soft btn button-icon menu-icon-session-active" style="margin-top:10px;">Sessions</button>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>';
                                        $counter++;
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
if ($_GET) {
    $id = $_GET["id"] ?? '';
    $action = $_GET["action"] ?? '';

    if ($action == 'view') {
        $stmt = $database->prepare("SELECT * FROM doctor WHERE doctor_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $name = $row["dfname"] . " " . $row["dlname"];
        $email = $row["docemail"];
        $specialty = $row["specialties"];
        $tele = $row["doctel"];

        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="doctors.php">&times;</a>
                    <div class="content">SmartMeet Web App</div>
                    <div style="display:flex;justify-content:center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                            <tr><td><p style="font-size:25px;font-weight:500;">View Details</p><br><br></td></tr>
                            <tr><td><b>Name:</b> ' . $name . '<br><br></td></tr>
                            <tr><td><b>Email:</b> ' . $email . '<br><br></td></tr>
                            <tr><td><b>Telephone:</b> ' . $tele . '<br><br></td></tr>
                            <tr><td><b>Specialties:</b> ' . $specialty . '<br><br></td></tr>
                            <tr><td><a href="doctors.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a></td></tr>
                        </table>
                    </div>
                </center>
            </div>
        </div>';
    }
}
?>
</body>
</html>
