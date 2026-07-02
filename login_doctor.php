<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
    <title>Doctor Login</title>
    <style>
        body {
            background: url('img/CategoryDoctor.png') no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .overlay {
            background-color: rgba(255, 255, 255, 0.15);
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
        }

        .error-msg {
            color: red;
            font-size: 13px;
            margin-top: 6px;
            text-align: left;
            display: none;
            padding-left: 2px;
        }

        .input-text {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .label-td {
            padding-bottom: 10px;
        }

        .container {
            max-width: 420px;
            margin: 60px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 35px 40px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
        }

        .header-text {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #2c5aa0;
        }

        .sub-text {
            color: #4a7cc7;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            background-color: #3b82f6;
            color: #fff;
            border: none;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background-color: #2563eb;
        }

        .illustration {
            text-align: center;
            margin-bottom: 15px;
        }

        .illustration svg {
            width: 120px;
            height: 120px;
        }

        .footer-image {
            width: 100px;
            position: absolute;
            bottom: 15px;
            right: 20px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
<div class="overlay"></div>

<?php
session_start();
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Kolkata');
$_SESSION["date"] = date('Y-m-d');

include("connection.php");

$error_message = "";
$info_message = "";

if($_POST){
    if(isset($_POST['login'])) {
        $email = $_POST['useremail'];
        $password = $_POST['userpassword'];

        if(!preg_match("/@.*\.com$/", $email)){
            $error_message = "Email must contain '@' and '.com'";
        } else {
            $result = $database->query("SELECT * FROM webuser WHERE email='$email'");
            if($result->num_rows == 1){
                $utype = $result->fetch_assoc()['usertype'];
                if ($utype=='p'){
                    $checker = $database->query("SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
                    if ($checker->num_rows==1){
                        $_SESSION['user']=$email;
                        $_SESSION['usertype']='p';
                        header('location: patient/index.php');
                        exit;
                    } else {
                        $error_message = "Invalid email or password!";
                    }
                } elseif($utype=='a'){
                    $checker = $database->query("SELECT * FROM admin WHERE aemail='$email' AND apassword='$password'");
                    if ($checker->num_rows==1){
                        $_SESSION['user']=$email;
                        $_SESSION['usertype']='a';
                        header('location: admin/index.php');
                        exit;
                    } else {
                        $error_message = "Invalid email or password!";
                    }
                } elseif($utype=='d'){
                    $checker = $database->query("SELECT * FROM doctor WHERE docemail='$email' AND docpassword='$password'");
                    if ($checker->num_rows==1){
                        $_SESSION['user']=$email;
                        $_SESSION['usertype']='d';
                        header('location: doctor/index.php');
                        exit;
                    } else {
                        $error_message = "Invalid email or password!";
                    }
                }
            } else {
                header('Location: signup_doctor.php');
                exit;
            }
        }
    } elseif(isset($_POST['forgot'])) {
        $email=$_POST['useremail'];
        $result= $database->query("SELECT * FROM webuser WHERE email='$email'");
        if($result->num_rows==1){
            $code = rand(100000, 999999);
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_email'] = $email;

            $subject = "Your Password Reset Code";
            $message = "Your password reset code is: $code";
            $headers = "From: no-reply@yourdomain.com\r\n";
            // mail($email, $subject, $message, $headers);

            $info_message = "A reset code has been sent to your email (check server mail settings).";
        } else {
            $error_message = "Email not found! Please register first.";
        }
    }
}
?>

<center>
<div class="container">
    <div class="illustration">
        <!-- Doctor Symbol SVG -->
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="doctorGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#2563eb;stop-opacity:1" />
                </linearGradient>
            </defs>
            <!-- Medical Cross Background -->
            <circle cx="100" cy="100" r="80" fill="url(#doctorGradient)" opacity="0.1"/>
            <!-- Stethoscope -->
            <path d="M 70 60 Q 70 40, 90 40 Q 100 40, 100 50 L 100 80 Q 100 100, 85 110 L 75 120" 
                  stroke="#3b82f6" stroke-width="4" fill="none" stroke-linecap="round"/>
            <path d="M 130 60 Q 130 40, 110 40 Q 100 40, 100 50" 
                  stroke="#3b82f6" stroke-width="4" fill="none" stroke-linecap="round"/>
            <circle cx="75" cy="125" r="8" fill="#3b82f6"/>
            <circle cx="90" cy="40" r="4" fill="#3b82f6"/>
            <circle cx="110" cy="40" r="4" fill="#3b82f6"/>
            <!-- Medical Cross -->
            <rect x="95" y="135" width="10" height="35" fill="#2563eb" rx="2"/>
            <rect x="80" y="150" width="40" height="10" fill="#2563eb" rx="2"/>
            <!-- Doctor's Head -->
            <circle cx="100" cy="85" r="18" fill="#fbbf77"/>
            <!-- Doctor's Cap -->
            <ellipse cx="100" cy="72" rx="20" ry="8" fill="#3b82f6"/>
            <rect x="80" y="72" width="40" height="6" fill="#2563eb"/>
        </svg>
    </div>
    <form action="" method="POST" id="loginForm">
        <p class="header-text">Doctor Login</p>
        <p class="sub-text">Login with your details to continue</p>
        <table border="0" style="width:100%;">
            <tr>
                <td class="label-td"><label for="useremail" class="form-label">Email:</label></td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="email" name="useremail" id="useremail" class="input-text" placeholder="Email Address" required>
                    <div id="emailError" class="error-msg">Email must contain '@' and '.com'</div>
                </td>
            </tr>
            <tr>
                <td class="label-td"><label for="userpassword" class="form-label">Password:</label></td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="password" name="userpassword" class="input-text" placeholder="Password">
                </td>
            </tr>

            <?php if(!empty($error_message)): ?>
            <tr><td><p style="color:red; margin-top:8px;"><?php echo $error_message; ?></p></td></tr>
            <?php endif; ?>
            
            <?php if(!empty($info_message)): ?>
            <tr><td><p style="color:green; margin-top:8px;"><?php echo $info_message; ?></p></td></tr>
            <?php endif; ?>

            <tr>
                <td style="padding-top:15px;">
                    <input type="submit" name="login" value="Login" class="login-btn">
                </td>
            </tr>
            <tr>
                <td style="padding-top:8px;">
                    <input type="submit" name="forgot" value="Forgot Password" class="login-btn" style="background:#ddd;color:#333;">
                </td>
            </tr>
        </table>

        <div style="margin-top:20px;">
            <label class="sub-text" style="font-weight: 280;">Don't have an account&#63;</label>
            <a href="signup_doctor.php" class="hover-link1 non-style-link"> Sign Up</a>
        </div>
    </form>
</div>

<!--img src="img/doctorimg.png" alt="decoration" class="footer-image"-->
</center>

<script>
document.getElementById("useremail").addEventListener("input", function() {
    const email = this.value.trim();
    const error = document.getElementById("emailError");
    if (!email.includes("@") || !email.endsWith(".com")) {
        error.style.display = "block";
    } else {
        error.style.display = "none";
    }
});
</script>
</body>
</html>