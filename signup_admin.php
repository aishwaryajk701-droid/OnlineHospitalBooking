<?php
// signup_admin.php
session_start();
date_default_timezone_set('Asia/Kolkata');

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

include("connection.php");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fname         = trim($_POST['fname'] ?? '');
    $lname         = trim($_POST['lname'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $age           = trim($_POST['age'] ?? '');
    $gender        = trim($_POST['gender'] ?? '');
    $dob           = trim($_POST['dob'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $grad_year     = trim($_POST['grad_year'] ?? '');
    $tele          = trim($_POST['tele'] ?? '');
    $email         = trim($_POST['aemail'] ?? '');
    $password      = $_POST['apassword'] ?? '';
    $cpassword     = $_POST['cpassword'] ?? '';

    // VALIDATION
    if ($fname === '' || $lname === '') {
        $error = "First and last name are required.";
    } elseif (!preg_match("/^[0-9]{10}$/", $tele)) {
        $error = "Mobile number must be 10 digits.";
    } elseif (!is_numeric($age) || $age < 18 || $age > 120) {
        $error = "Enter a valid age (18–120).";
    } elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $error = "Select a valid gender.";
    } elseif ($dob == '') {
        $error = "Date of Birth is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '.com')) {
        $error = "Enter a valid email ending with .com";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/", $password)) {
        $error = "Password must include letters, numbers & special character.";
    } elseif ($password !== $cpassword) {
        $error = "Passwords do not match.";
    } else {

        // CHECK EMAIL IN webuser
        $sql = "SELECT email FROM webuser WHERE email = ?";
        $stmt = $database->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                $error = "This email is already registered.";
            } else {

              

                // INSERT admin
                $insertAdmin = "INSERT INTO admin 
                (fname, lname, address, age, gender, dob, qualification, grad_year, aemail, apassword, tele)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt2 = $database->prepare($insertAdmin);

                if ($stmt2) {
                    $stmt2->bind_param(
                        "sssisssisss",
                        $fname, $lname, $address, $age, $gender, $dob,
                        $qualification, $grad_year, $email, $apassword, $tele
                    );

                    if ($stmt2->execute()) {

                        // INSERT into webuser
                        $stmt3 = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'a')");
                        $stmt3->bind_param("s", $email);
                        $stmt3->execute();

                        // LOGIN SESSION
                        $_SESSION["user"] = $email;
                        $_SESSION["usertype"] = "a";
                        $_SESSION["username"] = $fname;

                        header("Location: admin/index.php");
                        exit;

                    } else {
                        $error = "Account creation failed.";
                    }
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Admin Account — SmartMeet</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/animations.css">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/signup.css">

<style>
body {
  background: url('img/adminimg.png') no-repeat center center/cover;
  font-family: 'Poppins', sans-serif;
  margin: 0;
  min-height: 100vh;
  display: flex; justify-content: center; align-items: center;
}
body::before {
  content:''; position: fixed; inset: 0;
  background: rgba(255,255,255,0.15);
}
.container {
  width: 750px;
  background:#fff;
  padding:32px;
  border-radius:12px;
  box-shadow:0 6px 18px rgba(0,0,0,0.06);
  z-index:1;
}

.admin-icon {
  width: 85px;
  margin: 0 auto 12px;
  display: block;
}

input, select, textarea {
  width:100%; height:46px; padding:10px 12px;
  border:1px solid #ddd; border-radius:6px; margin-bottom:16px;
  box-sizing:border-box;
}
textarea { height:80px; resize:vertical; }

.header-text { font-size: 26px; text-align:center; }
.sub-text { text-align:center; color:#666; margin-bottom:20px; }
.error { color:#b00020; text-align:center; margin-bottom:16px; }

#password-msg, #cpassword-msg {
  font-size: 0.9em; margin-top:-10px; margin-bottom:14px; display:block;
}

/* Bigger Buttons */
.login-btn {
  width: 100%;
  height: 35px ;
  font-size: 15px;
  padding: 14px;
  border-radius: 10px;
}
.btn-primary-soft {
  font-size: 15px;
  padding: 12px;
}
</style>
</head>

<body>
<div class="container">
<form method="POST">

<img src="img/admin_icon.png" class="admin-icon">

<p class="header-text">Create Admin Account</p>
<p class="sub-text">Please fill the details carefully</p>

<?php if ($error): ?>
<div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<label>First Name</label>
<input name="fname" required value="<?php echo $_POST['fname'] ?? ''; ?>">

<label>Last Name</label>
<input name="lname" required value="<?php echo $_POST['lname'] ?? ''; ?>">

<label>Address</label>
<textarea name="address" required><?php echo $_POST['address'] ?? ''; ?></textarea>

<label>Age</label>
<input type="number" id="age" name="age" min="18" max="120" oninput="autoDOB()" required
value="<?php echo $_POST['age'] ?? ''; ?>">

<label>Gender</label>
<select name="gender" required>
  <option value="">Select</option>
  <option value="Male"   <?php if(($_POST['gender'] ?? '')==='Male') echo 'selected'; ?>>Male</option>
  <option value="Female" <?php if(($_POST['gender'] ?? '')==='Female') echo 'selected'; ?>>Female</option>
  <option value="Other"  <?php if(($_POST['gender'] ?? '')==='Other') echo 'selected'; ?>>Other</option>
</select>

<label>Date of Birth</label>
<input type="date" id="dob" name="dob" required value="<?php echo $_POST['dob'] ?? ''; ?>">

<label>Qualification</label>
<input name="qualification" required value="<?php echo $_POST['qualification'] ?? ''; ?>">

<label>Graduation Year</label>
<input type="number" name="grad_year" min="1900" max="<?php echo date('Y'); ?>" required
value="<?php echo $_POST['grad_year'] ?? ''; ?>">

<label>Mobile Number</label>
<input name="tele" type="tel" pattern="[0-9]{10}" placeholder="1234567890" required
value="<?php echo $_POST['tele'] ?? ''; ?>">

<label>Email</label>
<input name="email" type="email" required value="<?php echo $_POST['email'] ?? ''; ?>">

<label>Create Password</label>
<input id="password" name="password" type="password" required oninput="checkPasswordStrength()">
<span id="password-msg">At least 6 chars, letters, numbers & special chars</span>

<label>Confirm Password</label>
<input id="cpassword" name="cpassword" type="password" required oninput="checkPasswordMatch()">
<span id="cpassword-msg"></span>

<button class="login-btn btn-primary" type="submit">SIGN UP</button>
<button class="login-btn btn-primary-soft" type="reset">RESET</button>

<p style="text-align:center; margin-top:12px;">
Already have an account? <a href="login_admin.php">Login</a>
</p>

</form>
</div>

<script>
function autoDOB() {
  let age = document.getElementById("age").value;
  let dob = document.getElementById("dob");
  if (age === "" || age < 18 || age > 120) { dob.value = ""; return; }

  let today = new Date();
  let year = today.getFullYear() - parseInt(age);
  let month = String(today.getMonth() + 1).padStart(2, '0');
  let day = String(today.getDate()).padStart(2, '0');
  dob.value = `${year}-${month}-${day}`;
}

function checkPasswordStrength(){
  const pw = document.getElementById('password').value;
  const msg = document.getElementById('password-msg');
  const strongRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/;

  msg.style.color = strongRegex.test(pw) ? 'green' : 'red';
  msg.textContent = strongRegex.test(pw) ? 'Strong Password' : 'Weak Password';
  checkPasswordMatch();
}

function checkPasswordMatch(){
  const pw = document.getElementById('password').value;
  const cpw = document.getElementById('cpassword').value;
  const msg = document.getElementById('cpassword-msg');

  if (cpw === "") { msg.textContent = ""; return; }
  msg.style.color = (pw === cpw) ? 'green' : 'red';
  msg.textContent = (pw === cpw) ? 'Passwords match' : 'Passwords do not match';
}
</script>

</body>
</html>
