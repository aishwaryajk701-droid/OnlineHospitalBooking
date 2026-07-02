<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Appointments</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
</style>
</head>
<body>
    <?php
    session_start();
    if(isset($_SESSION["user"])){
        if($_SESSION["user"]=="" || $_SESSION["usertype"]!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }

    include("../connection.php");

    // Correct doctor columns
    $userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["doctor_id"];
    $username=$userfetch["dfname"]." ".$userfetch["dlname"];
    ?>
    <div class="container">
        <div class="menu">
        <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
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
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu">
                            <div><p class="menu-text">Dashboard</p></div>
                        </a>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active">
                            <div><p class="menu-text">My Appointments</p></div>
                        </a>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu">
                            <div><p class="menu-text">My Sessions</p></div>
                        </a>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu">
                            <div><p class="menu-text">My Patients</p></div>
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

        <div class="dash-body">
            <table border="0" width="100%" style="margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="appointment.php">
                            <button class="login-btn btn-primary-soft btn btn-icon-back" style="margin-left:20px;width:125px">
                                <font>Back</font>
                            </button>
                        </a>
                    </td>
                    <td>
                        <p style="font-size:23px;padding-left:12px;font-weight:600;">Appointment Manager</p>
                    </td>

                    <td width="15%">
                        <p style="font-size:14px;color:#777;text-align:right;">Today's Date</p>
                        <p class="heading-sub12">

                        <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $today = date('Y-m-d');
                            echo $today;

                            // Correct JOIN with correct column names
                            $list110 = $database->query("
                                SELECT * FROM schedule 
                                INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid
                                INNER JOIN patient ON patient.patient_id = appointment.pid
                                INNER JOIN doctor ON schedule.docid = doctor.doctor_id
                                WHERE doctor.doctor_id = $userid
                            ");
                        ?>
                        </p>
                    </td>

                    <td width="10%">
                        <button class="btn-label">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left:45px;font-size:18px;">
                            My Appointments (<?php echo $list110->num_rows; ?>)
                        </p>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <center>
                        <table class="filter-container">
                            <tr>
                                <td width="10%"></td>
                                <td width="5%" style="text-align:center;">Date:</td>
                                <td width="30%">
                                    <form action="" method="post">
                                    <input type="date" name="sheduledate" class="input-text filter-container-items" style="width:95%;">
                                </td>
                                <td width="12%">
                                    <input type="submit" name="filter" value="Filter" class="btn-primary-soft btn button-icon btn-filter" style="width:100%;">
                                    </form>
                                </td>
                            </tr>
                        </table>
                        </center>
                    </td>
                </tr>

                <?php
                // Correct main SQL
                $sqlmain = "
                    SELECT 
                        appointment.appoid,
                        schedule.scheduleid,
                        schedule.title,
                        patient.pfname,
                        patient.plname,
                        schedule.scheduledate,
                        schedule.scheduletime,
                        appointment.apponum,
                        appointment.appodate
                    FROM schedule
                    INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid
                    INNER JOIN patient ON patient.patient_id = appointment.pid
                    WHERE schedule.docid = $userid
                ";

                if($_POST){
                    if(!empty($_POST["sheduledate"])){
                        $sheduledate = $_POST["sheduledate"];
                        $sqlmain .= " AND schedule.scheduledate='$sheduledate'";
                    }
                }

                $result = $database->query($sqlmain);
                ?>

                <tr>
                    <td colspan="4">
                        <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Appointment Number</th>
                                <th>Session Title</th>
                                <th>Session Date & Time</th>
                                <th>Appointment Date</th>
                                <th>Events</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        if($result->num_rows == 0){
                            echo '
                            <tr><td colspan="6"><center>
                            <img src="../img/notfound.svg" width="25%">
                            <p>No Appointments Found</p>
                            <a href="appointment.php"><button class="btn-primary-soft btn">Show All</button></a>
                            </center></td></tr>';
                        }
                        else{
                            while($row = $result->fetch_assoc()){
                                $appoid     = $row["appoid"];
                                $scheduleid = $row["scheduleid"];
                                $title      = $row["title"];
                                $pname      = $row["pfname"]." ".$row["plname"];
                                $scheduledate = $row["scheduledate"];
                                $scheduletime = $row["scheduletime"];
                                $apponum    = $row["apponum"];
                                $appodate   = $row["appodate"];

                                echo "
                                <tr>
                                    <td><b>$pname</b></td>
                                    <td style='text-align:center;font-size:20px;'>$apponum</td>
                                    <td>$title</td>
                                    <td style='text-align:center;'>$scheduledate @ $scheduletime</td>
                                    <td style='text-align:center;'>$appodate</td>
                                    <td>
                                        <div style='text-align:center;'>
                                            <a href='?action=drop&id=$appoid&name=$pname&session=$title&apponum=$apponum'>
                                                <button class='btn-primary-soft btn button-icon btn-delete'>Cancel</button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>";
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
// ================= Popup Actions ================= //
if($_GET){
    $id = $_GET["id"];
    $action = $_GET["action"];

    if($action=='drop'){
        $nameget=$_GET["name"];
        $session=$_GET["session"];
        $apponum=$_GET["apponum"];

        echo "
        <div id='popup1' class='overlay'>
            <div class='popup'>
                <center>
                    <h2>Are you sure?</h2>
                    <a class='close' href='appointment.php'>&times;</a>
                    <div class='content'>
                        Patient: <b>$nameget</b><br>
                        Appointment No: <b>$apponum</b><br><br>
                    </div>
                    <div>
                        <a href='delete-appointment.php?id=$id'>
                            <button class='btn-primary btn'>Yes</button>
                        </a>
                        <a href='appointment.php'>
                            <button class='btn-primary btn'>No</button>
                        </a>
                    </div>
                </center>
            </div>
        </div>";
    }
}
?>

</body>
</html>
