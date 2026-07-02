<?php
// booking.php (Booking Completed Page)
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Completed</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background-color: #d8efff;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 520px;
            margin: 120px auto;
            background: #fff;
            border-radius: 12px;
            padding: 36px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            text-align: center;
        }
        h2 {
            color: #28a745;
            margin-bottom: 18px;
        }
        p {
            color: #333;
            font-size: 16px;
            margin-bottom: 24px;
        }
        .btn-home {
            display: inline-block;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-home:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>✅ Booking Completed</h2>
    <p>Your appointment has been successfully booked.</p>
    <a href="index.php" class="btn-home">← Back to Home</a>
</div>

</body>
</html>
