<?php
// signup_doctor.php
session_start();
date_default_timezone_set('Asia/Kolkata');

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

include("connection.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dfname      = trim($_POST['dfname'] ?? '');
    $dlname      = trim($_POST['dlname'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $age         = trim($_POST['age'] ?? '');
    $gender      = trim($_POST['gender'] ?? '');
    $dob         = trim($_POST['dob'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $specialties   = trim($_POST['specialties'] ?? '');
    $grad_year     = trim($_POST['grad_year'] ?? '');
    $experience    = trim($_POST['experience'] ?? '');
    $doctel        = trim($_POST['doctel'] ?? '');
    $docemail      = trim($_POST['docemail'] ?? '');
    $docpassword   = $_POST['docpassword'] ?? '';
    $cpassword     = $_POST['cpassword'] ?? '';

    // VALIDATIONS
    if ($dfname === '' || $dlname === '') {
        $error = "First and last name are required.";
    } elseif (!filter_var($docemail, FILTER_VALIDATE_EMAIL) || !str_ends_with($docemail, '.com')) {
        $error = "Enter a valid email ending with .com";
    } elseif (!preg_match("/^\+91[0-9]{10}$/", $doctel)) {
        $error = "Phone must start with +91 and have 10 digits.";
    } elseif (!is_numeric($age) || $age < 22) {
        $error = "Age must be 22 or above.";
    } elseif (!in_array($gender, ['Male','Female','Other'])) {
        $error = "Invalid gender selected.";
    } elseif ($dob === '') {
        $error = "Date of birth is required.";
    } elseif ($qualification === '') {
        $error = "Qualification is required.";
    } elseif ($specialties === '') {
        $error = "Specialization is required.";
    } elseif (!is_numeric($grad_year) || strlen($grad_year) != 4) {
        $error = "Graduation year must be YYYY format.";
    } elseif (!is_numeric($experience) || $experience < 0) {
        $error = "Experience must be a non-negative number.";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/", $docpassword)) {
        $error = "Password must contain letters, numbers & a special character.";
    } elseif ($docpassword !== $cpassword) {
        $error = "Passwords do not match.";
    } else {

        // CHECK EMAIL EXISTS
        $check = $database->prepare("SELECT usertype FROM webuser WHERE email=?");
        $check->bind_param("s", $docemail);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $error = "This email is already registered.";
        } else {

            $hashed = password_hash($docpassword, PASSWORD_DEFAULT);

            $insert = $database->prepare("
                INSERT INTO doctor 
                (dfname, dlname, address, age, gender, dob, qualification, specialties, grad_year, experience, docemail, docpassword, doctel) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insert->bind_param(
                "sssisssssissi",
                $dfname, $dlname, $address, $age, $gender, $dob,
                $qualification, $specialties, $grad_year, $experience,
                $docemail, $hashed, $doctel
            );

            if ($insert->execute()) {

                // INSERT INTO WEBUSER
                $w = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'd')");
                $w->bind_param("s", $docemail);
                $w->execute();

                $_SESSION["user"] = $docemail;
                $_SESSION["usertype"] = "d";
                $_SESSION["username"] = $dfname;

                header("Location: doctor/index.php");
                exit;
            } else {
                $error = "Failed to create account.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Create Doctor Account — SmartMeet</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/animations.css">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/signup.css">

<style>
body {
  background: url('img/Doctorbackground.png') no-repeat center center/cover;
  margin: 0;
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
}
body::before {
  content: "";
  position: fixed;
  inset: 0;
  background: rgba(255,255,255,0.12);
}
.container {
  width: 750px;
  background: #fff;
  padding: 35px;
  border-radius: 12px;
  z-index: 1;
  box-shadow: 0 6px 18px rgba(0,0,0,0.07);
}
.doctor-icon {
  width: 80px;
  height: 80px;
  margin: 10px auto;
  display: block;
}
.input-text {
  width: 100%;
  height: 46px;
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  margin-bottom: 14px;
  box-sizing: border-box;
}
textarea.input-text {
  height: 80px;
  resize: vertical;
}
.header-text { text-align:center; margin:0; font-size:26px; }
.sub-text { text-align:center; color:#666; margin-bottom:20px; }
.login-btn { padding: 10px 18px; border-radius: 8px; cursor:pointer; width:100%; }
.btn-primary { background:#0066cc; color:#fff; border:none; }
.btn-primary-soft { background:#f4f7fb; border:1px solid #e3eaf4; margin-top:10px; }
.error { color:#b00020; text-align:center; margin-bottom:10px; }

/* Password validation text */
#passwordMsg, #confirmMsg {
  font-size: 13px;
  margin-top: -10px;
  margin-bottom: 10px;
}
</style>
</head>

<body>
<div class="container">

<form method="POST" action="">

<p class="header-text">Create Doctor Account</p>

<img src="img/doctor_icon.png" class="doctor-icon" alt="Doctor Icon">

<p class="sub-text">Fill the details to continue</p>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<label>First Name</label>
<input name="dfname" class="input-text" required>

<label>Last Name</label>
<input name="dlname" class="input-text" required>

<label>Clinic / Home Address</label>
<textarea name="address" class="input-text" required></textarea>

<label>Age</label>
<input type="number" id="age" name="age" class="input-text" min="22" oninput="autoDOB()" required>

<label>Gender</label>
<select name="gender" class="input-text" required>
  <option value="">Select</option>
  <option>Male</option>
  <option>Female</option>
  <option>Other</option>
</select>

<label>Date of Birth</label>
<input type="date" id="dob" name="dob" class="input-text" required>

<label>Qualification</label>
<input name="qualification" class="input-text" placeholder="MBBS, MD, MS etc." required>

<label>Specialization</label>
<select name="specialties" class="input-text" required>
  <option value="">Select Specialization</option>
  <?php
    $sp = $database->query("SELECT sname FROM specialties");
    while ($row = $sp->fetch_assoc()) {
        echo "<option value='{$row['sname']}'>{$row['sname']}</option>";
    }
  ?>
</select>

<label>Graduation Year</label>
<input name="grad_year" type="number" class="input-text" placeholder="YYYY" required>

<label>Experience (in years)</label>
<input name="experience" type="number" class="input-text" required>

<label>Phone Number</label>
<input name="doctel" id="doctel" type="text" class="input-text" required 
       value="+91" maxlength="13" oninput="fixPhone()">

<label>Email</label>
<input name="docemail" type="email" class="input-text" required>

<!-- PASSWORD FIELD WITH LIVE CHECK -->
<label>Create Password</label>
<input name="docpassword" id="docpassword" type="password" class="input-text" required oninput="validatePassword()">
<div id="passwordMsg"></div>

<!-- CONFIRM PASSWORD FIELD WITH MATCH CHECK -->
<label>Confirm Password</label>
<input name="cpassword" id="cpassword" type="password" class="input-text" required oninput="checkMatch()">
<div id="confirmMsg"></div>

<button type="submit" class="login-btn btn-primary">Sign Up</button>
<button type="reset" class="login-btn btn-primary-soft">Reset</button>

<p style="text-align:center; margin-top:12px;">
Already have an account? <a href="login_doctor.php">Login</a>
</p>

</form>
</div>

<script>
function autoDOB() {
  const age = document.getElementById("age").value;
  const dob = document.getElementById("dob");

  if (age === "" || age < 22) {
    dob.value = "";
    return;
  }

  const today = new Date();
  const year = today.getFullYear() - parseInt(age);
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');

  dob.value = `${year}-${month}-${day}`;
}

function fixPhone() {
  let ph = document.getElementById("doctel").value;

  if (!ph.startsWith("+91")) {
    ph = "+91" + ph.replace(/[^0-9]/g, "").slice(-10);
  }

  if (ph.length > 13) {
    ph = ph.slice(0, 13);
  }

  document.getElementById("doctel").value = ph;
}

/* LIVE PASSWORD VALIDATION */
function validatePassword() {
  const pass = document.getElementById("docpassword").value;
  const msg = document.getElementById("passwordMsg");

  const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/;

  if (pass.length === 0) {
    msg.innerHTML = "";
    return;
  }

  if (regex.test(pass)) {
    msg.style.color = "green";
    msg.innerHTML = "✓ Strong password";
  } else {
    msg.style.color = "red";
    msg.innerHTML = "✗ Must contain letters, numbers, a special character & 6+ characters";
  }
}

/* MATCH CHECK */
function checkMatch() {
  const pass = document.getElementById("docpassword").value;
  const cpass = document.getElementById("cpassword").value;
  const msg = document.getElementById("confirmMsg");

  if (cpass.length === 0) {
    msg.innerHTML = "";
    return;
  }

  if (pass === cpass) {
    msg.style.color = "green";
    msg.innerHTML = "✓ Password matched";
  } else {
    msg.style.color = "red";
    msg.innerHTML = "✗ Password not matched";
  }
}
</script>

</body>
</html>
