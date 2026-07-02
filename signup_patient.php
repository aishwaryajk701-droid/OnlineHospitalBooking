<?php
// signup_patient.php
session_start();
date_default_timezone_set('Asia/Kolkata');

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

include("connection.php");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pfname   = trim($_POST['pfname'] ?? '');
    $plname   = trim($_POST['plname'] ?? '');
    $paddress = trim($_POST['paddress'] ?? '');
    $age      = trim($_POST['age'] ?? '');
    $gender   = trim($_POST['gender'] ?? '');
    $pdob     = trim($_POST['pdob'] ?? '');
    $ptel     = trim($_POST['ptel'] ?? '');
    $pemail   = trim($_POST['pemail'] ?? '');
    $ppassword= $_POST['ppassword'] ?? '';
    $cpassword= $_POST['cpassword'] ?? '';

    // ✅ Validation
    if ($pfname === '' || $plname === '') {
        $error = "First name and last name are required.";
    } elseif (!filter_var($pemail, FILTER_VALIDATE_EMAIL) || substr_compare($pemail, '.com', -4) !== 0) {
        $error = "Enter a valid email that ends with .com";
    } elseif (!preg_match("/^\+91[0-9]{10}$/", $ptel)) {
        $error = "Mobile number must start with +91 and have 10 digits.";
    } elseif (!is_numeric($age) || (int)$age < 1 || (int)$age > 120) {
        $error = "Enter a valid age between 1 and 120.";
    } elseif (!in_array($gender, ['Male','Female','Other'])) {
        $error = "Select a valid gender.";
    } elseif ($pdob == '') {
        $error = "Date of birth is required.";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/", $ppassword)) {
        $error = "Password must include letters, numbers & a special character.";
    } elseif ($ppassword !== $cpassword) {
        $error = "Passwords do not match.";
    } else {
        // ✅ Check if email exists
        $sql = "SELECT usertype FROM webuser WHERE email = ?";
        $stmt = $database->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $pemail);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows > 0) {
                $error = "An account with this email already exists.";
            } else {
                // ✅ Hash password
                $hashed = password_hash($ppassword, PASSWORD_DEFAULT);

                $insertPatient = "INSERT INTO patient (pfname, plname, paddress, age, gender, pdob, ptel, pemail, ppassword)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt2 = $database->prepare($insertPatient);

                if ($stmt2) {
                    $stmt2->bind_param(
                        "sssisssss",
                        $pfname, $plname, $paddress, $age, $gender, $pdob,
                        $ptel, $pemail, $hashed
                    );

                    if ($stmt2->execute()) {
                        // ✅ Add to webuser
                        $insertWebuser = "INSERT INTO webuser (email, usertype) VALUES (?, 'p')";
                        $stmt3 = $database->prepare($insertWebuser);
                        if ($stmt3) {
                            $stmt3->bind_param("s", $pemail);
                            $stmt3->execute();
                        }

                        $_SESSION["user"] = $pemail;
                        $_SESSION["usertype"] = "p";
                        $_SESSION["username"] = $pfname;

                        header("Location: patient/index.php");
                        exit;
                    } else {
                        $error = "Failed to create account. Please try again.";
                    }
                } else {
                    $error = "Database error: could not prepare statement.";
                }
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Create Patient Account — SmartMeet</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />

<link rel="stylesheet" href="css/animations.css">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/signup.css">

<style>
/* Same styles as your original code */
body {
  background: url('img/patientimg.png') no-repeat center center/cover;
  font-family: 'Poppins', sans-serif;
  margin: 0;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}
body::before {
  content: '';
  position: fixed; inset: 0;
  background: rgba(255,255,255,0.15);
}
.container {
  width: 700px;
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  z-index: 1;
}
.patient-icon { width: 80px; height: 80px; margin: 10px auto 15px auto; display: block; }
label { font-weight: 600; margin-bottom: 6px; display: block; }
.input-text { width: 100%; height: 46px; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 16px; box-sizing: border-box; }
textarea.input-text { height: 80px; resize: vertical; }
.header-text { font-size: 24px; margin: 0; text-align:center; }
.sub-text { color: #666; margin-bottom: 20px; text-align:center; }
.login-btn { padding: 10px 18px; border-radius: 8px; cursor:pointer; }
.btn-primary { background:#0066cc; color:#fff; border:none; width: 100%; }
.btn-primary-soft { background:#f4f7fb; color:#333; border:1px solid #e3eaf4; width: 100%; margin-top: 10px; }
.error { color:#b00020; margin-bottom:12px; text-align:center; }

#password-msg, #cpassword-msg { font-size: 0.9em; margin-top: -10px; margin-bottom: 14px; display: block; }
</style>
</head>

<body>
<div class="container">
<form method="POST" action="">
<p class="header-text">Create Patient Account</p>

<img src="img/SignupPatientimg.png"  class="patient-icon" alt="Patient Icon">
<p class="sub-text">Fill the form to continue</p>

<?php if ($error): ?>
<div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<label>First Name</label>
<input class="input-text" name="pfname" required value="<?php echo htmlspecialchars($_POST['pfname'] ?? ''); ?>">

<label>Last Name</label>
<input class="input-text" name="plname" required value="<?php echo htmlspecialchars($_POST['plname'] ?? ''); ?>">

<label>Address</label>
<textarea class="input-text" name="paddress" required><?php echo htmlspecialchars($_POST['paddress'] ?? ''); ?></textarea>

<label>Age</label>
<input class="input-text" id="age" name="age" type="number" min="1" max="120" oninput="autoDOB()" required value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>">

<label>Gender</label>
<select class="input-text" name="gender" required>
  <option value="">Select</option>
  <option value="Male" <?php if(($_POST['gender'] ?? '')==='Male') echo 'selected'; ?>>Male</option>
  <option value="Female" <?php if(($_POST['gender'] ?? '')==='Female') echo 'selected'; ?>>Female</option>
  <option value="Other" <?php if(($_POST['gender'] ?? '')==='Other') echo 'selected'; ?>>Other</option>
</select>

<label>Date of Birth</label>
<input class="input-text" id="pdob" name="pdob" type="date" required value="<?php echo htmlspecialchars($_POST['pdob'] ?? ''); ?>">

<label>Mobile Number</label>
<input class="input-text" name="ptel" type="tel" pattern="^\+91[0-9]{10}$" placeholder="+911234567890" required value="<?php echo htmlspecialchars($_POST['ptel'] ?? '+91'); ?>">

<label>Email</label>
<input class="input-text" name="pemail" type="email" required value="<?php echo htmlspecialchars($_POST['pemail'] ?? ''); ?>">

<label>Create Password</label>
<input id="ppassword" class="input-text" name="ppassword" type="password" required oninput="checkPasswordStrength()">
<span id="password-msg">At least 6 chars, letters, numbers & special char</span>

<label>Confirm Password</label>
<input id="cpassword" class="input-text" name="cpassword" type="password" required oninput="checkPasswordMatch()">
<span id="cpassword-msg"></span>

<button type="submit" class="login-btn btn-primary">Sign Up</button>
<button type="reset" class="login-btn btn-primary-soft">Reset</button>

<p style="text-align:center; margin-top:12px;">
Already have an account? <a href="login_patient.php">Login</a>
</p>
</form>
</div>

<script>
function autoDOB() {
  const age = document.getElementById("age").value;
  const dob = document.getElementById("pdob");
  if (age === "" || age < 1 || age > 120) { dob.value = ""; return; }
  const today = new Date();
  const year = today.getFullYear() - parseInt(age);
  const month = String(today.getMonth() + 1).padStart(2,'0');
  const day = String(today.getDate()).padStart(2,'0');
  dob.value = `${year}-${month}-${day}`;
}

function checkPasswordStrength(){
  const pw = document.getElementById('ppassword').value;
  const msg = document.getElementById('password-msg');
  const strongRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/;
  msg.style.color = strongRegex.test(pw) ? 'green' : 'red';
  msg.textContent = strongRegex.test(pw) ? 'Password looks strong' : 'Password is not strong';
  checkPasswordMatch();
}

function checkPasswordMatch(){
  const pw = document.getElementById('ppassword').value;
  const cpw = document.getElementById('cpassword').value;
  const msg = document.getElementById('cpassword-msg');
  if (cpw === "") { msg.textContent = ""; return; }
  msg.style.color = (pw === cpw) ? 'green' : 'red';
  msg.textContent = (pw === cpw) ? 'Passwords match' : 'Passwords do not match';
}
</script>

</body>
</html>
