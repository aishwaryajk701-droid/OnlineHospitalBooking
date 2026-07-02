<?php
session_start();
include("../connection.php");

// ✅ Check if admin logged in
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'a') {
    header("location: ../login.php");
    exit();
}

// =====================================
// ✅ ADD NEW DOCTOR (MINIMAL REQUIRED FIELDS)
// =====================================
if (isset($_POST['add_doctor'])) {
    $fname = trim($_POST['dfname']);
    $lname = trim($_POST['dlname']);
    $email = trim($_POST['docemail']);
    $pass = trim($_POST['docpassword']);
    $spe_id = $_POST['specialties'];

    // Since your DB requires many fields → we fill missing ones with defaults
    $address = "Not Provided";
    $age = 30;
    $gender = "Other";
    $dob = "1990-01-01";
    $qualification = "Not Provided";
    $grad_year = 2020;
    $experience = 0;
    $doctel = 9999999999;

    if ($fname && $lname && $email && $pass && $spe_id) {
        $check = $database->query("SELECT * FROM doctor WHERE docemail='$email'");
        if ($check->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            $database->query("
                INSERT INTO doctor 
                (dfname, dlname, address, age, gender, dob, qualification, specialties, grad_year, experience, docemail, docpassword, doctel)
                VALUES
                ('$fname', '$lname', '$address', '$age', '$gender', '$dob', '$qualification', '$spe_id', '$grad_year', '$experience', '$email', '$pass', '$doctel')
            ");

            $success = "Doctor added successfully!";
        }
    } else {
        $error = "Please fill all fields!";
    }
}

// =====================================
// ✅ UPDATE DOCTOR
// =====================================
if (isset($_POST['update_doctor'])) {
    $docid = $_POST['docid'];
    $fname = trim($_POST['dfname']);
    $lname = trim($_POST['dlname']);
    $email = trim($_POST['docemail']);
    $spe_id = $_POST['specialties'];

    if ($fname && $lname && $email && $spe_id) {
        $database->query("
            UPDATE doctor 
            SET dfname='$fname', dlname='$lname', docemail='$email', specialties='$spe_id'
            WHERE doctor_id='$docid'
        ");
        $success = "Doctor updated successfully!";
    } else {
        $error = "Please fill all required fields!";
    }
}

// =====================================
// ✅ DELETE DOCTOR
// =====================================
if (isset($_GET['delete'])) {
    $docid = $_GET['delete'];
    $database->query("DELETE FROM doctor WHERE doctor_id='$docid'");
    $success = "Doctor removed successfully!";
}

// =====================================
// ✅ Fetch all doctors
// =====================================
$result = $database->query("SELECT * FROM doctor ORDER BY doctor_id DESC");

// =====================================
// ✅ Fetch specialties for dropdown
// =====================================
$specialties = $database->query("SELECT * FROM specialties ORDER BY sname ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f6f7fb; }
        .container { width:90%; margin:40px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#333; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ddd; padding:10px; text-align:center; }
        th { background:#007bff; color:white; }
        tr:nth-child(even) { background:#f2f2f2; }
        input, select { width:90%; padding:8px; border:1px solid #ccc; border-radius:5px; }
        button, .btn { padding:8px 14px; border:none; border-radius:5px; cursor:pointer; }
        .btn-add { background:#28a745; color:white; }
        .btn-edit { background:#ffc107; color:white; }
        .btn-del { background:#dc3545; color:white; }
        .btn-back { background:#6c757d; color:white; float:left; margin-bottom:10px; }
        .message { text-align:center; padding:10px; color:green; font-weight:bold; }
        .error { text-align:center; padding:10px; color:red; font-weight:bold; }
    </style>
</head>
<body>
    
<div class="container">
    <button class="btn btn-back" onclick="history.back()">← Back</button>
    <h2>Doctor Management Panel</h2>

    <?php if(isset($success)) echo "<p class='message'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <!-- Add / Edit Doctor Form -->
    <div class="form-section">
        <form method="POST" action="">
            <h3>Add / Edit Doctor</h3>
            <input type="hidden" name="docid" id="docid">

            <label>First Name:</label><br>
            <input type="text" name="dfname" id="dfname" required><br><br>

            <label>Last Name:</label><br>
            <input type="text" name="dlname" id="dlname" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="docemail" id="docemail" required><br><br>

            <label>Password:</label><br>
            <input type="password" name="docpassword" id="docpassword" required><br><br>

            <label>Specialty:</label><br>
            <select name="specialties" id="specialties" required>
                <option value="">--Select Specialty--</option>
                <?php
                while($sp_row = $specialties->fetch_assoc()){
                    echo "<option value='{$sp_row['sname']}'>{$sp_row['sname']}</option>";
                }
                ?>
            </select><br><br>

            <button type="submit" name="add_doctor" class="btn btn-add">Add Doctor</button>
            <button type="submit" name="update_doctor" class="btn btn-edit">Update Doctor</button>
        </form>
    </div>
    

    <!-- Doctor Table -->
    <h3 style="margin-top:30px;">All Doctors</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Specialty</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['doctor_id'] ?></td>
                <td><?= $row['dfname']." ".$row['dlname'] ?></td>
                <td><?= $row['docemail'] ?></td>
                <td><?= $row['specialties'] ?></td>
                <td>
                    <button class='btn btn-edit'
                        onclick='editDoctor(
                            <?= $row["doctor_id"] ?>,
                            "<?= $row["dfname"] ?>",
                            "<?= $row["dlname"] ?>",
                            "<?= $row["docemail"] ?>",
                            "<?= $row["specialties"] ?>"
                        )'>Edit</button>

                    <a href="?delete=<?= $row['doctor_id'] ?>" onclick="return confirm('Are you sure?')">
                        <button class='btn btn-del'>Delete</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>
</div>

<script>
function editDoctor(id, fname, lname, email, spe){
    document.getElementById("docid").value = id;
    document.getElementById("dfname").value = fname;
    document.getElementById("dlname").value = lname;
    document.getElementById("docemail").value = email;
    document.getElementById("specialties").value = spe;

    document.getElementById("docpassword").required = false;
}
</script>

</body>
</html>
