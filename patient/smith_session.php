<?php
session_start();
if(!isset($_SESSION["user"])) header("location: ../login.php");
include("../connection.php");

date_default_timezone_set('Asia/Kolkata');

// Fetch user info
$useremail = $_SESSION["user"];
$result = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$user = $result->fetch_assoc();
$username = $user["pfname"]." ".$user["plname"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Doctor Session</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        body {
            background-color: #e8f4fa;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .menu {
            width: 22%;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .dash-body {
            flex: 1;
            padding: 30px;
            background-color: #e8f4fa;
        }
        .session-container {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            margin: 50px auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .session-container h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .login-btn, .back-btn {
            margin-top: 15px;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-btn {
            background-color: #007bff;
        }
        .login-btn.clicked {
            background-color: #0056b3;
        }
        .back-btn {
            background-color: #28a745;
            margin-left: 15px;
        }
        .back-btn.clicked {
            background-color: #19692c;
        }
    </style>
</head>
<body>

<div class="container"> 
    <!-- Left Menu -->
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
                                <p class="profile-title"><?php echo substr($username,0,13); ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail,0,22); ?></p>
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
                <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                    <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a>
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

    <!-- Right Main Body -->
    <div class="dash-body">
        <div class="session-container">
            <center>
                <h1>Dr.John Smith-Cardiology</h1>
                <form id="bookingForm">
                    <input type="button" value="Book Appointment" class="login-btn btn-primary" id="bookBtn">
                    <button type="button" class="back-btn" id="backBtn">Back</button>
                </form>
            </center>
        </div>
    </div>
</div>

<script>
    // Set schedule ID as 102 (your requested value)
    const scheduleId = 102;

    // Book button click
    const bookBtn = document.getElementById("bookBtn");
    bookBtn.addEventListener("click", function() {
        bookBtn.classList.add("clicked");
        setTimeout(() => {
            bookBtn.classList.remove("clicked");
            window.location.href = `booking.php?scheduleid=${scheduleId}`;
        }, 300);
    });

    // Back button click
    const backBtn = document.getElementById("backBtn");
    backBtn.addEventListener("click", function() {
        backBtn.classList.add("clicked");
        setTimeout(() => {
            backBtn.classList.remove("clicked");
            window.location.href = "doctors.php";
        }, 300);
    });
</script>

</body>
</html>
