<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
    <title>Patient Login</title>
    <style>
        body {
            background: url('img/Categoryimage.png') no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
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
            color: #4b3ca7;
        }

        .sub-text {
            color: #6b58c4;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            background-color: #6b58c4;
            color: #fff;
            border: none;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background-color: #5847a4;
        }

        .illustration {
            text-align: center;
            margin-bottom: 15px;
        }

        .illustration img {
            width: 120px;
            height: auto;
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
            // Check if email exists in webuser table
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
                } else {
                    $error_message = "This page is for Patient login only!";
                }
            } else {
                // Email not found in database, redirect to signup
                header('Location: signup_patient.php');
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
        <img src="img/SignupPatientimg.png" alt="Patient Symbol">
    </div>
    <form action="" method="POST" id="loginForm">
        <p class="header-text">Patient Login</p>
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
            <a href="signup_patient.php" class="hover-link1 non-style-link"> Sign Up</a>
        </div>
    </form>
</div>

<img src="images/decoration.png" alt="decoration" class="footer-image">
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