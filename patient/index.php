<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Dashboard</title>
    <style>
        .dashbord-tables{ animation: transitionIn-Y-over 0.5s; }
        .filter-container{ animation: transitionIn-Y-bottom  0.5s; }
        .sub-table,.anime{ animation: transitionIn-Y-bottom 0.5s; }
    </style>
</head>
<body>
<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    } else {
        $useremail=$_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");

$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
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
            <tr class="menu-row">
                <td class="menu-btn menu-icon-home menu-active menu-icon-home-active">
                    <a href="index.php" class="non-style-link-menu non-style-link-menu-active">
                        <div><p class="menu-text">Home</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor">
                    <a href="doctors.php" class="non-style-link-menu">
                        <div><p class="menu-text">All Doctors</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu">
                        <div><p class="menu-text">My Bookings</p></div>
                    </a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings">
                    <a href="settings.php" class="non-style-link-menu">
                        <div><p class="menu-text">Settings</p></div>
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body" style="margin-top: 15px">
        <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
            <tr>
                <td colspan="1" class="nav-bar">
                    <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Home</p>
                </td>
                <td width="25%"></td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119,119,119);padding:0;margin:0;text-align:right;">
                        Today's Date
                    </p>
                    <p class="heading-sub12" style="padding:0;margin:0;">
                        <?php 
                        date_default_timezone_set('Asia/Kolkata');
                        $today = date('Y-m-d');
                        echo date('d-m-Y');

                        $patientrow = $database->query("SELECT * FROM patient;");
                        $doctorrow = $database->query("SELECT * FROM doctor;");
                        $appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$today';");
                        $schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate = '$today';");
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
                        <table class="filter-container doctor-header patient-header" style="border:none;width:95%" border="0">
                            <tr>
                                <td>
                                    <h3>Welcome!</h3>
                                    <h1><?php echo $username ?>.</h1>
                                    <p>Haven't any idea about doctors? no problem let's jump to 
                                        <a href="doctors.php" class="non-style-link"><b>"All Doctors"</b></a> section or 
                                        <a href="schedule.php" class="non-style-link"><b>"Sessions"</b></a><br>
                                        Track your past and future appointments history.<br>
                                        Also find out the expected arrival time of your doctor or medical consultant.<br><br>
                                    </p>
                                    
                                    <h3>Channel a Doctor Here</h3>

                                    <!-- Search Form with Category -->
                                    <form action="schedule.php" method="post" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;" id="searchForm">
                                        <select name="specialty" class="input-text" style="width:25%;padding:10px;">
                                            <option value="">Select Category</option>
                                            <?php
                                            $specialties = $database->query("SELECT id, sname FROM specialties ORDER BY sname ASC;");
                                            if ($specialties->num_rows > 0) {
                                                while ($spec = $specialties->fetch_assoc()) {
                                                    echo "<option value='{$spec['id']}'>{$spec['sname']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>

                                        <input type="search" name="search" class="input-text" id="searchInput"
                                            placeholder="Search Doctor and We will Find The Session Available" list="doctors" style="width:45%;">
                                        
                                        <?php
                                        echo '<datalist id="doctors">';
                                        $list11 = $database->query("SELECT CONCAT(dfname, ' ', dlname) AS fullname FROM doctor;");
                                        while($row00=$list11->fetch_assoc()){
                                            echo "<option value='{$row00["fullname"]}'>";
                                        }
                                        echo '</datalist>';
                                        ?>

                                        <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding:10px 25px;">
                                    </form>
                                    <br><br>
                                </td>
                            </tr>
                        </table>
                    </center>
                </td>
            </tr>

            <tr>
                <td colspan="4">
                    <table border="0" width="100%">
                        <tr>
                            <td width="50%">
                                <center>
                                    <table class="filter-container" style="border:none;" border="0">
                                        <tr>
                                            <td colspan="4">
                                                <p style="font-size:20px;font-weight:600;padding-left:12px;">Status</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:25%;">
                                                <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                                                    <div>
                                                        <div class="h1-dashboard"><?php echo $doctorrow->num_rows ?></div><br>
                                                        <div class="h3-dashboard">All Doctors</div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/doctors-hover.svg');"></div>
                                                </div>
                                            </td>
                                            <td style="width:25%;">
                                                <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                                                    <div>
                                                        <div class="h1-dashboard"><?php echo $patientrow->num_rows ?></div><br>
                                                        <div class="h3-dashboard">All Patients</div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/patients-hover.svg');"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:25%;">
                                                <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                                                    <div>
                                                        <div class="h1-dashboard"><?php echo $appointmentrow->num_rows ?></div><br>
                                                        <div class="h3-dashboard">New Booking</div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/book-hover.svg');"></div>
                                                </div>
                                            </td>
                                            <td style="width:25%;">
                                                <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display:flex;">
                                                    <div>
                                                        <div class="h1-dashboard"><?php echo $schedulerow->num_rows ?></div><br>
                                                        <div class="h3-dashboard" style="font-size:15px">Today Sessions</div>
                                                    </div>
                                                    <div class="btn-icon-back dashboard-icons" style="background-image:url('../img/icons/session-iceblue.svg');"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </center>
                            </td>

                            <td>
                                <p style="font-size:20px;font-weight:600;padding-left:40px;" class="anime">Your Upcoming Booking</p>
                                <center>
                                    <div class="abc scroll" style="height:250px;padding:0;margin:0;">
                                        <table width="85%" class="sub-table scrolldown" border="0">
                                            <thead>
                                                <tr>
                                                    <th class="table-headin">Appoint. Number</th>
                                                    <th class="table-headin">Session Title</th>
                                                    <th class="table-headin">Doctor</th>
                                                    <th class="table-headin">Scheduled Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $sqlmain = "SELECT appointment.apponum, schedule.title, doctor.dfname, doctor.dlname, 
                                                        schedule.scheduledate, schedule.scheduletime 
                                                        FROM schedule
                                                        INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid
                                                        INNER JOIN patient ON patient.patient_id = appointment.pid
                                                        INNER JOIN doctor ON doctor.doctor_id = schedule.docid
                                                        WHERE patient.patient_id = ? AND schedule.scheduledate >= ?
                                                        ORDER BY schedule.scheduledate ASC;";
                                            $stmt = $database->prepare($sqlmain);
                                            $stmt->bind_param("is", $userid, $today);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if($result->num_rows == 0){
                                                echo '<tr><td colspan="4"><center><img src="../img/notfound.svg" width="25%"><p class="heading-main12">Nothing to show here!</p><a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn">Channel a Doctor</button></a></center></td></tr>';
                                            } else {
                                                while($row = $result->fetch_assoc()){
                                                    echo '<tr>
                                                        <td style="padding:30px;font-size:25px;font-weight:700;">'.$row["apponum"].'</td>
                                                        <td style="padding:20px;">'.substr($row["title"],0,30).'</td>
                                                        <td>'.$row["dfname"].' '.$row["dlname"].'</td>
                                                        <td style="text-align:center;">'.substr($row["scheduledate"],0,10).' '.substr($row["scheduletime"],0,5).'</td>
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
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', function(e){
    const val = document.getElementById('searchInput').value.trim();
    if(val === "Test Doctor"){
        e.preventDefault();
        window.location.href = "test_session.php";
    } else if(val === "Dr. John Smith"){
        e.preventDefault();
        window.location.href = "smith_session.php";
    } else if(val === "Dr. Alice Lee"){
        e.preventDefault();
        window.location.href = "lee_session.php";
    } else if(val === "Dr. Rajesh Kumar"){
        e.preventDefault();
        window.location.href = "rajesh_session.php";
    }
});
</script>
</body>
</html>
