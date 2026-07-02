<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != "d") {
    header("location: ../login.php");
    exit;
}

$useremail = $_SESSION["user"];

// DB
include("../connection.php");

// doctor info
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();

$userid = $userfetch["doctor_id"];
$username = $userfetch["dfname"] . " " . $userfetch["dlname"];

// --------------------------------------------------------------------

$selecttype = "My";
$current = "My patients Only";

if ($_POST) {

    // SEARCH
    if (isset($_POST["search"])) {
        $keyword = $_POST["search12"];

        $sqlmain = "
            SELECT * FROM patient 
            WHERE pemail LIKE '%$keyword%' 
               OR pfname LIKE '%$keyword%'
               OR plname LIKE '%$keyword%'
        ";
        $selecttype = "My";
    }

    // FILTER
    if (isset($_POST["filter"])) {
        if ($_POST["showonly"] == "all") {
            $sqlmain = "SELECT * FROM patient";
            $selecttype = "All";
            $current = "All patients";
        } else {
            // My patients only
            $sqlmain = "
                SELECT * FROM appointment 
                INNER JOIN patient ON patient.patient_id = appointment.pid 
                INNER JOIN schedule ON schedule.scheduleid = appointment.scheduleid 
                WHERE schedule.docid = $userid
            ";
            $selecttype = "My";
            $current = "My patients Only";
        }
    }

} else {

    // default = my patients
    $sqlmain = "
        SELECT * FROM appointment 
        INNER JOIN patient ON patient.patient_id = appointment.pid 
        INNER JOIN schedule ON schedule.scheduleid = appointment.scheduleid 
        WHERE schedule.docid = $userid
    ";
    $selecttype = "My";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patients</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">

    <style>
        .popup { animation: transitionIn-Y-bottom 0.5s; }
        .sub-table { animation: transitionIn-Y-bottom 0.5s; }
    </style>
</head>
<body>

<div class="container">
    <div class="menu">
        <table class="menu-container" border="0">

            <tr>
                <td colspan="2">
                    <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px">
                                <img src="../img/user.png" width="100%" style="border-radius:50%">
                            </td>

                            <td>
                                <p class="profile-title"><?php echo substr($username, 0, 13); ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail, 0, 22); ?></p>
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
                    <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a>
                </td>
            </tr>

            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></div></a>
                </td>
            </tr>

            <tr class="menu-row">
                <td class="menu-btn menu-icon-session">
                    <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                </td>
            </tr>

            <tr class="menu-row">
                <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                    <a href="patient.php" class="non-style-link-menu non-style-link-menu-active">
                        <div><p class="menu-text">My Patients</p></div>
                    </a>
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

        <table border="0" width="100%" style="margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="patient.php">
                        <button class="login-btn btn-primary-soft btn btn-icon-back"
                                style="padding:11px;margin-left:20px;width:125px;">
                            Back
                        </button>
                    </a>
                </td>

                <td>
                    <form action="" method="post" class="header-search">
                        <input type="search" name="search12"
                               class="input-text header-searchbar"
                               placeholder="Search Patient name or Email">

                        <input type="submit" name="search"
                               value="Search"
                               class="login-btn btn-primary btn"
                               style="padding:10px 25px;">
                    </form>
                </td>

                <td width="15%">
                    <p style="text-align:right;font-size:14px;color:#777">Today's Date</p>
                    <p class="heading-sub12"><?php echo date('Y-m-d'); ?></p>
                </td>

                <td width="10%">
                    <button class="btn-label"><img src="../img/calendar.svg"></button>
                </td>
            </tr>

            <tr>
                <td colspan="4" style="padding-top:10px;">
                    <p class="heading-main12"
                       style="margin-left:45px;font-size:18px;color:#313131;">
                        <?php echo $selecttype . " Patients (" . $database->query($sqlmain)->num_rows . ")"; ?>
                    </p>
                </td>
            </tr>

            <tr>
                <td colspan="4">
                    <center>
                        <table class="filter-container" border="0">
                            <form action="" method="post">
                                <td style="text-align:right;">Show Details About : </td>

                                <td width="30%">
                                    <select name="showonly" class="box filter-container-items"
                                            style="width:90%;height:37px;">
                                        <option selected hidden><?php echo $current; ?></option>
                                        <option value="my">My Patients Only</option>
                                        <option value="all">All Patients</option>
                                    </select>
                                </td>

                                <td width="12%">
                                    <input type="submit" name="filter" value=" Filter"
                                           class="btn-primary-soft btn button-icon btn-filter"
                                           style="padding:15px;width:100%;">
                                </td>
                            </form>
                        </table>
                    </center>
                </td>
            </tr>

            <!-- RESULT TABLE -->
            <tr>
                <td colspan="4">
                    <center>
                    <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown">

                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Telephone</th>
                                <th>Email</th>
                                <th>Date of Birth</th>
                                <th>Events</th>
                            </tr>
                            </thead>

                            <tbody>

<?php
$result = $database->query($sqlmain);

if ($result->num_rows == 0) {
    echo '
        <tr><td colspan="5"><br><br><center>
        <img src="../img/notfound.svg" width="25%">
        <p class="heading-main12">No patients found</p>
        <a href="patient.php"><button class="login-btn btn-primary-soft btn">Show All</button></a>
        <br><br></center></td></tr>';
} else {

    while ($row = $result->fetch_assoc()) {

        $pid = $row["patient_id"];
        $name = $row["pfname"] . " " . $row["plname"];
        $email = $row["pemail"];
        $dob = $row["pdob"];
        $tel = $row["ptel"];

        echo "
            <tr>
                <td>$name</td>
                <td>$tel</td>
                <td>$email</td>
                <td>$dob</td>

                <td>
                    <center>
                    <a href='?action=view&id=$pid' class='non-style-link'>
                        <button class='btn-primary-soft btn button-icon btn-view'>View</button>
                    </a>
                    </center>
                </td>
            </tr>
        ";
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
// VIEW POPUP
if ($_GET) {

    $id = $_GET["id"];

    $result = $database->query("SELECT * FROM patient WHERE patient_id=$id");
    $row = $result->fetch_assoc();

    $name = $row["pfname"] . " " . $row["plname"];
    $email = $row["pemail"];
    $dob = $row["pdob"];
    $tele = $row["ptel"];
    $address = $row["paddress"];

    echo "
    <div id='popup1' class='overlay'>
        <div class='popup'>
            <center>
                <a class='close' href='patient.php'>&times;</a>

                <table width='80%' class='sub-table add-doc-form-container'>

                    <tr><td><p style='font-size:25px;font-weight:500;'>View Details</p><br></td></tr>

                    <tr><td>Patient ID: P-$id<br><br></td></tr>
                    <tr><td>Name: $name<br><br></td></tr>
                    <tr><td>Email: $email<br><br></td></tr>
                    <tr><td>Telephone: $tele<br><br></td></tr>
                    <tr><td>Address: $address<br><br></td></tr>
                    <tr><td>Date of Birth: $dob<br><br></td></tr>

                    <tr>
                        <td>
                            <a href='patient.php'><button class='login-btn btn-primary-soft btn'>OK</button></a>
                        </td>
                    </tr>
                </table>

            </center>
        </div>
    </div>
    ";
}
?>

</body>
</html>
