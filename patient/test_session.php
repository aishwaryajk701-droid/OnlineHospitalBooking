<?php
session_start();
if (!isset($_SESSION["user"])) header("location: ../login.php");
include("../connection.php");

date_default_timezone_set('Asia/Kolkata');

// Fetch logged-in patient details
$useremail = $_SESSION["user"];
$result = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$user = $result->fetch_assoc();
$username = $user["pfname"] . " " . $user["plname"];

// Fetch doctor info (you can pass doctor_id via URL)
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 1;

// Fetch doctor data
$doctor_result = $database->query("SELECT * FROM doctor WHERE doctor_id = $doctor_id");
$doctor = $doctor_result->fetch_assoc();
$doctor_name = $doctor["dfname"] . " " . $doctor["dlname"];
$specialty = $doctor["specialties"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $doctor_name; ?> - Session</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        body {
            background-color: #f9fbff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .menu {
            background-color: #ffffff;
            border-right: 1.5px solid rgb(235, 235, 235);
            width: 21%;
            height: 100vh;
            box-shadow: 0 0px 0px 2px rgba(240, 240, 240, 0.3);
        }
        .dash-body {
            flex: 1;
            padding: 30px;
            background-color: #fafdff;
        }
        .session-container {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            max-width: 650px;
            margin: 50px auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .session-container h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .session-container h2 {
            color: #555;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .session-details {
            text-align: left;
            margin-top: 20px;
            background-color: #f4f7fc;
            border-radius: 10px;
            padding: 15px 20px;
        }
        .session-details p {
            font-size: 16px;
            color: #444;
            margin: 5px 0;
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
                                <p class="profile-title"><?php echo substr($username,0,13); ?></p>
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
            <h1><?php echo $doctor_name; ?></h1>
            <h2><?php echo $specialty; ?></h2>

            
            <form id="bookingForm">
                <input type="button" value="Book Appointment" class="login-btn btn-primary" id="bookBtn">
                <button type="button" class="back-btn" id="backBtn">Back</button>
            </form>
        </div>
    </div>
</div>

<script>
    const doctorId = "<?php echo $doctor_id; ?>";
    const bookBtn = document.getElementById("bookBtn");

    bookBtn.addEventListener("click", function() {
        bookBtn.classList.add("clicked");
        setTimeout(() => {
            bookBtn.classList.remove("clicked");
            window.location.href = `booking.php?docid=${doctorId}`;
        }, 300);
    });

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