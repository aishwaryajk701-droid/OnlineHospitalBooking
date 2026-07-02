<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Schedule</title>
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
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }

    include("../connection.php");

    // FIXED doctor columns
    $userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["doctor_id"];
    $username = $userfetch["dfname"] . " " . $userfetch["dlname"];

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
                     <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div>
                 </td>
             </tr>
             <tr class="menu-row">
                 <td class="menu-btn menu-icon-appoinment">
                     <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
                 </td>
             </tr>
             
             <tr class="menu-row">
                 <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                     <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Sessions</p></div></a>
                 </td>
             </tr>
             <tr class="menu-row">
                 <td class="menu-btn menu-icon-patient">
                     <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My Patients</p></a></div>
                 </td>
             </tr>
             <tr class="menu-row">
                 <td class="menu-btn menu-icon-settings">
                     <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                 </td>
             </tr>
             
         </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                    <a href="schedule.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">My Sessions</p>                 
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding:0;margin:0;text-align:right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding:0;margin:0;">
                            <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            $today = date('Y-m-d');
                            echo $today;

                            $list110 = $database->query("SELECT * FROM schedule WHERE docid=$userid");
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" style="padding-top:10px;width:100%;">
                        <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">My Sessions (<?php echo $list110->num_rows; ?>)</p>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" style="padding-top:0;width:100%;">
                        <center>
                        <table class="filter-container" border="0">
                        <tr>
                           <td width="10%"></td> 
                        <td width="5%" style="text-align:center;">Date:</td>
                        <td width="30%">
                        <form action="" method="post">
                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin:0;width:95%;">
                        </td>
                        
                    <td width="12%">
                        <input type="submit" name="filter" value="Filter" class="btn-primary-soft btn button-icon btn-filter" style="padding:15px;margin:0;width:100%;">
                        </form>
                    </td>

                    </tr>
                            </table>

                        </center>
                    </td>
                    
                </tr>

                <?php

                // FIXED doctor column names
                $sqlmain = "
                    SELECT schedule.scheduleid,
                           schedule.title,
                           doctor.dfname,
                           doctor.dlname,
                           schedule.scheduledate,
                           schedule.scheduletime,
                           schedule.nop
                    FROM schedule
                    INNER JOIN doctor ON schedule.docid = doctor.doctor_id
                    WHERE doctor.doctor_id = $userid
                ";

                if($_POST){
                    if(!empty($_POST["sheduledate"])){
                        $sheduledate = $_POST["sheduledate"];
                        $sqlmain .= " AND schedule.scheduledate='$sheduledate'";
                    }
                }
                ?>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                                <th class="table-headin">Session Title</th>
                                <th class="table-headin">Scheduled Date & Time</th>
                                <th class="table-headin">Max Bookings</th>
                                <th class="table-headin">Events</th>
                        </tr>
                        </thead>
                        <tbody>
                        
                            <?php
                                $result = $database->query($sqlmain);

                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="4"><br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    <br>
                                    <p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">No Sessions Found!</p>
                                    <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="margin-left:20px;">Show all Sessions</button></a>
                                    </center><br><br><br><br></td>
                                    </tr>';
                                }
                                else{
                                    while($row=$result->fetch_assoc()){
                                        $scheduleid=$row["scheduleid"];
                                        $title=$row["title"];
                                        $scheduledate=$row["scheduledate"];
                                        $scheduletime=$row["scheduletime"];
                                        $nop=$row["nop"];

                                        echo '<tr>
                                            <td>&nbsp;'.substr($title,0,30).'</td>
                                            <td style="text-align:center;">'.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'</td>
                                            <td style="text-align:center;">'.$nop.'</td>
                                            <td>
                                            <div style="display:flex;justify-content:center;">
                                                <a href="?action=view&id='.$scheduleid.'" class="non-style-link">
                                                    <button class="btn-primary-soft btn button-icon btn-view" style="padding-left:40px;padding-top:12px;padding-bottom:12px;margin-top:10px;">View</button>
                                                </a>
                                                &nbsp;&nbsp;&nbsp;
                                                <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link">
                                                    <button class="btn-primary-soft btn button-icon btn-delete" style="padding-left:40px;padding-top:12px;padding-bottom:12px;margin-top:10px;">Cancel Session</button>
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
    
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];

        if($action=='drop'){
            $nameget=$_GET["name"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                            Delete this record ('.substr($nameget,0,40).').
                        </div>
                        <div style="display:flex;justify-content:center;">
                            <a href="delete-session.php?id='.$id.'" class="non-style-link">
                                <button class="btn-primary btn" style="margin:10px;padding:10px;">Yes</button>
                            </a>
                            <a href="schedule.php" class="non-style-link">
                                <button class="btn-primary btn" style="margin:10px;padding:10px;">No</button>
                            </a>
                        </div>
                    </center>
                </div>
            </div>';
        }

        elseif($action=='view'){

            // FIXED doctor name fields
            $sqlmain = "
                SELECT schedule.scheduleid,
                       schedule.title,
                       doctor.dfname,
                       doctor.dlname,
                       schedule.scheduledate,
                       schedule.scheduletime,
                       schedule.nop
                FROM schedule
                INNER JOIN doctor ON schedule.docid=doctor.doctor_id
                WHERE schedule.scheduleid=$id
            ";

            $result = $database->query($sqlmain);
            $row = $result->fetch_assoc();

            $docname = $row["dfname"] . " " . $row["dlname"];
            $title = $row["title"];
            $scheduledate = $row["scheduledate"];
            $scheduletime = $row["scheduletime"];
            $nop = $row["nop"];

            // FIXED patient table fields
            $sqlmain12 = "
                SELECT appointment.apponum,
                       patient.patient_id,
                       patient.pfname,
                       patient.plname,
                       patient.ptel
                FROM appointment
                INNER JOIN patient ON patient.patient_id = appointment.pid
                WHERE appointment.scheduleid = $id
            ";

            $result12 = $database->query($sqlmain12);

            echo '
            <div id="popup1" class="overlay">
                <div class="popup" style="width:70%;">
                    <center>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="abc scroll" style="display:flex;justify-content:center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr><td><p style="font-size:25px;font-weight:500;">View Details.</p><br></td></tr>
                            
                            <tr><td class="label-td">Session Title:</td></tr>
                            <tr><td class="label-td">'.$title.'<br><br></td></tr>

                            <tr><td class="label-td">Doctor:</td></tr>
                            <tr><td class="label-td">'.$docname.'<br><br></td></tr>

                            <tr><td class="label-td">Scheduled Date:</td></tr>
                            <tr><td class="label-td">'.$scheduledate.'<br><br></td></tr>

                            <tr><td class="label-td">Scheduled Time:</td></tr>
                            <tr><td class="label-td">'.$scheduletime.'<br><br></td></tr>

                            <tr>
                                <td class="label-td">
                                    <b>Registered Patients: ('.$result12->num_rows.'/'.$nop.')</b><br><br>
                                </td>
                            </tr>

                            <tr><td>
                            <table width="100%" class="sub-table scrolldown" border="0">
                                <thead>
                                <tr>
                                    <th class="table-headin">Patient ID</th>
                                    <th class="table-headin">Patient Name</th>
                                    <th class="table-headin">Appointment No</th>
                                    <th class="table-headin">Phone</th>
                                </tr>
                                </thead>
                                <tbody>';

                                if($result12->num_rows==0){
                                    echo '<tr><td colspan="4"><center>No patients found.</center></td></tr>';
                                } else {
                                    while($row=$result12->fetch_assoc()){
                                        echo '<tr style="text-align:center;">
                                            <td>'.$row["patient_id"].'</td>
                                            <td>'.$row["pfname"].' '.$row["plname"].'</td>
                                            <td>'.$row["apponum"].'</td>
                                            <td>'.$row["ptel"].'</td>
                                        </tr>';
                                    }
                                }

                            echo '</tbody></table>
                            </td></tr>

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
