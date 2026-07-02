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
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
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
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }
    }else{
        header("location: ../login.php");
    }

    include("../connection.php");
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
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@edoc.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor ">
                        <a href="doctors.php" class="non-style-link-menu "><div><p class="menu-text">Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="delete_management.php" class="non-style-link-menu"><div><p class="menu-text">Delete Management</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%">
                        
                        <tr>
                            <td colspan="2" class="nav-bar">
                                <form action="doctors.php" method="post" class="header-search">
        
                                    <input type="search" name="search" class="input-text header-searchbar" 
                                    placeholder="Search Doctor name or Email" list="doctors">&nbsp;&nbsp;
                                    
                                    <?php
                                        echo '<datalist id="doctors">';
                                        $list11 = $database->query("SELECT dfname, dlname, docemail FROM doctor;");

                                        while($row00 = $list11->fetch_assoc()){
                                            $d = $row00["dfname"] . " " . $row00["dlname"];
                                            $c = $row00["docemail"];
                                            echo "<option value='$d'>";
                                            echo "<option value='$c'>";
                                        }
                                        echo '</datalist>';
                                    ?>
                                    
                                    <input type="Submit" value="Search" class="login-btn btn-primary-soft btn">
                                </form>
                            </td>

                            <td width="15%">
                                <p style="font-size: 14px;color: gray;text-align: right;">Today's Date</p>
                                <p class="heading-sub12">

                                    <?php 
                                        date_default_timezone_set('Asia/Kolkata');

                                        $today = date('Y-m-d');
                                        echo $today;

                                        $patientrow = $database->query("SELECT * FROM patient;");
                                        $doctorrow = $database->query("SELECT * FROM doctor;");
                                        $appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate>='$today';");
                                        $schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate='$today';");
                                    ?>

                                </p>
                            </td>

                            <td width="10%">
                                <button class="btn-label"><img src="../img/calendar.svg" width="100%"></button>
                            </td>
                        </tr>

                <tr>
                    <td colspan="4">
                        
                        <center>
                        <table class="filter-container" border="0">
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 25%;">
                                    <div class="dashboard-items" style="padding:20px;">
                                        <div>
                                            <div class="h1-dashboard">
                                                <?php echo $doctorrow->num_rows ?>
                                            </div><br>
                                            <div class="h3-dashboard">Doctors</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" 
                                        style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                    </div>
                                </td>

                                <td style="width: 25%;">
                                    <div class="dashboard-items" style="padding:20px;">
                                        <div>
                                            <div class="h1-dashboard">
                                                <?php echo $patientrow->num_rows ?>
                                            </div><br>
                                            <div class="h3-dashboard">Patients</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" 
                                        style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                    </div>
                                </td>

                                <td style="width: 25%;">
                                    <div class="dashboard-items" style="padding:20px;">
                                        <div>
                                            <div class="h1-dashboard">
                                                <?php echo $appointmentrow->num_rows ?>
                                            </div><br>
                                            <div class="h3-dashboard">NewBooking</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" 
                                        style="background-image: url('../img/icons/book-hover.svg');"></div>
                                    </div>
                                </td>

                                <td style="width: 25%;">
                                    <div class="dashboard-items" style="padding:20px;">
                                        <div>
                                            <div class="h1-dashboard">
                                                <?php echo $schedulerow->num_rows ?>
                                            </div><br>
                                            <div class="h3-dashboard">Today Sessions</div>
                                        </div>
                                        <div class="btn-icon-back dashboard-icons" 
                                        style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        </center>

                    </td>
                </tr>






                <tr>
                    <td colspan="4">
                        <table width="100%" class="dashbord-tables">
                            <tr>
                                <td>
                                    <p style="padding:10px;font-size:23px;font-weight:700;color:var(--primarycolor);padding-left:48px;">
                                        Upcoming Appointments until Next <?php echo date("l",strtotime("+1 week")); ?>
                                    </p>
                                    <p style="padding-left:50px;font-size:15px;font-weight:500;">
                                        Quick access to appointments scheduled within the next 7 days.
                                    </p>
                                </td>

                                <td>
                                    <p style="text-align:right;padding:10px;font-size:23px;font-weight:700;color:var(--primarycolor);padding-right:48px;">
                                        Upcoming Sessions until Next <?php echo date("l",strtotime("+1 week")); ?>
                                    </p>
                                    <p style="text-align:right;padding-right:50px;font-size:15px;font-weight:500;">
                                        Quick access to sessions scheduled in the next 7 days.
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td width="50%">
                                    <center>
                                        <div class="abc scroll" style="height: 200px;">
                                        <table width="85%" class="sub-table">

                                        <thead>
                                            <tr>
                                                <th class="table-headin">Appointment number</th>
                                                <th class="table-headin">Patient name</th>
                                                <th class="table-headin">Doctor</th>
                                                <th class="table-headin">Session</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));

                                            $sqlmain = "
                                                SELECT 
                                                    appointment.appoid,
                                                    appointment.apponum,
                                                    patient.pfname,
                                                    patient.plname,
                                                    doctor.dfname,
                                                    doctor.dlname,
                                                    schedule.title
                                                FROM schedule 
                                                INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid 
                                                INNER JOIN patient ON patient.patient_id=appointment.pid 
                                                INNER JOIN doctor ON doctor.doctor_id=schedule.docid  
                                                WHERE schedule.scheduledate>='$today'
                                                AND schedule.scheduledate<='$nextweek'
                                                ORDER BY schedule.scheduledate DESC
                                            ";

                                            $result = $database->query($sqlmain);

                                            if($result->num_rows == 0){
                                                echo '<tr><td colspan="4"><center>No upcoming appointments.</center></td></tr>';
                                            } else {
                                                while($row = $result->fetch_assoc()){
                                                    $pname = $row["pfname"] . " " . $row["plname"];
                                                    $docname = $row["dfname"] . " " . $row["dlname"];
                                                    echo "
                                                    <tr>
                                                        <td style='text-align:center;font-size:23px;font-weight:500;'>".$row['apponum']."</td>
                                                        <td>".$pname."</td>
                                                        <td>".$docname."</td>
                                                        <td>".$row['title']."</td>
                                                    </tr>";
                                                }
                                            }
                                        ?>
                                        </tbody>

                                        </table>
                                        </div>
                                    </center>
                                </td>






                                <td width="50%">
                                    <center>
                                        <div class="abc scroll" style="height: 200px;">
                                            <table width="85%" class="sub-table">
                                            <thead>
                                                <tr>
                                                    <th class="table-headin">Session Title</th>
                                                    <th class="table-headin">Doctor</th>
                                                    <th class="table-headin">Date & Time</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            <?php
                                                $sqlmain = "
                                                SELECT 
                                                    schedule.title,
                                                    doctor.dfname,
                                                    doctor.dlname,
                                                    schedule.scheduledate,
                                                    schedule.scheduletime
                                                FROM schedule 
                                                INNER JOIN doctor ON schedule.docid=doctor.doctor_id  
                                                WHERE schedule.scheduledate>='$today'
                                                AND schedule.scheduledate<='$nextweek'
                                                ORDER BY schedule.scheduledate DESC
                                            ";

                                            $result = $database->query($sqlmain);

                                            if($result->num_rows == 0){
                                                echo '<tr><td colspan="4"><center>No sessions found.</center></td></tr>';
                                            } else {
                                                while($row = $result->fetch_assoc()){
                                                    $docname = $row["dfname"] . " " . $row["dlname"];
                                                    echo "
                                                    <tr>
                                                        <td>".$row['title']."</td>
                                                        <td>".$docname."</td>
                                                        <td style='text-align:center;'>"
                                                            .$row['scheduledate']." ".$row['scheduletime'].
                                                        "</td>
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

                            <tr>
                                <td><center><a href="patient.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Show all Appointments</button></a></center></td>
                                <td><center><a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Show all Sessions</button></a></center></td>
                            </tr>

                        </table>
                    </td>

                </tr>
            </table>
        </div>
    </div>


</body>
</html>
